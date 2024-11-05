<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\EventGoogleCalendar;
use App\Models\GoogleCalendarIntegration;
use App\Models\GoogleCalendarList;
use App\Models\LiveConsultation;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Flash;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Support\Facades\Validator;
use Log;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class GoogleMeetCalendarController extends AppBaseController
{
    public $client;

    public function googleConfig()
    {
        $name = getGoogleJsonFilePath();

        if (empty($name)) {
            return;
        }

        $this->client = new Google_Client();
        // Set the application name, this is included in the User-Agent HTTP header.
        $this->client->setApplicationName(config('app.name'));
        // Set the authentication credentials we downloaded from Google.
        $this->client->setAuthConfig(storage_path(getGoogleJsonFilePath()));
        // Setting offline here means we can pull data from the venue's calendar when they are not actively using the site.
        $this->client->setAccessType('offline');
        // This will include any other scopes (Google APIs) previously granted by the venue
        $this->client->setIncludeGrantedScopes(true);
        // Set this to force to consent form to display.
        $this->client->setApprovalPrompt('force');
        // Add the Google Calendar scope to the request.
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['googleCalendarIntegrationExists'] = GoogleCalendarIntegration::where('user_id', getLoggedInUserId())->exists();
        $data['googleCalendarLists'] = GoogleCalendarList::with('eventGoogleCalendar')->where('user_id', getLoggedInUserId())->get();

        return view('google_meet.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($liveConsultation, $accessToken, $meta)
    {
        $date = $liveConsultation['consultation_date'];
        $startTime = Carbon::parse($date);
        $endTime = $startTime->copy()->addMinutes($liveConsultation['consultation_duration_minutes']);
        $startDateTime = $startTime->toRfc3339String();
        $endDateTime = $endTime->toRfc3339String();

        $results = [];
        if ($accessToken) {
            $this->client->setAccessToken($accessToken);
            $service = new Google_Service_Calendar($this->client);

            foreach ($meta['lists'] as $calendarId) {
                $event = new Google_Service_Calendar_Event([
                    'summary' => $meta['name'],
                    'start' => ['dateTime' => $startDateTime],
                    'end' => ['dateTime' => $endDateTime],
                    'reminders' => ['useDefault' => true],
                    'description' => $meta['description'],
                ]);

                if ($liveConsultation->platform_type == LiveConsultation::GOOGLE_MEET) {

                    $data = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

                    $conference = new \Google_Service_Calendar_ConferenceData();
                    $conferenceRequest = new \Google_Service_Calendar_CreateConferenceRequest();
                    $conferenceRequest->setRequestId('randomString123');
                    $conference->setCreateRequest($conferenceRequest);
                    $data->setConferenceData($conference);

                    $data = $service->events->patch($calendarId, $data->id, $data, ['conferenceDataVersion' => 1]);

                    $data['google_meet_link'] = $data->hangoutLink;
                } else {
                    $data = $service->events->insert($calendarId, $event);
                }

                $data['google_calendar_id'] = $calendarId;
                $results[] = $data;
            }

            return $results;
        } else {
            return $results;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // For redirect to Generate redirect URL
    public function oauth()
    {
        $this->googleConfig();

        $name = getGoogleJsonFilePath();

        $jsonFilePath = storage_path($name);
        if (!file_exists($jsonFilePath)) {
            Flash::error(__('messages.google_meet.upload_again_json_file'));
            return redirect()->back();
        }

        if (empty($name) && (file_exists(storage_path()))) {
            Flash::error(__('messages.google_meet.validate_json_file'));

            return redirect()->back();
        }

        $authUrl = $this->client->createAuthUrl();
        $filteredUrl = filter_var($authUrl, FILTER_SANITIZE_URL);

        return redirect($filteredUrl);
    }

    // if Successfully connected to Google Calendar then store data
    public function redirect(Request $request)
    {
        $this->googleConfig();
        try {

            DB::beginTransaction();

            $accessToken = $this->client->fetchAccessTokenWithAuthCode($request->get('code'));

            $exists = GoogleCalendarIntegration::where('user_id', getLoggedInUserId())->exists();

            if ($exists) {
                GoogleCalendarIntegration::where('user_id', getLoggedInUserId())->delete();
                GoogleCalendarList::where('user_id', getLoggedInUserId())->delete();
            }

            $googleCalendarIntegration = GoogleCalendarIntegration::create([
                'user_id' => getLoggedInUserId(),
                'access_token' => $accessToken['access_token'],
                'last_used_at' => Carbon::now(),
                'meta' => json_encode($accessToken),
            ]);

            $this->client->setAccessToken($accessToken);
            $calendarLists = $this->fetchCalendarListAndSyncToDB();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
        }

        Flash::success(__('messages.google_meet.google_calendar_connect'));

        return redirect(route('connect-google-calendar.index'));
    }

    // For the store multiple google Calendar
    public function fetchCalendarListAndSyncToDB()
    {
        $gcHelper = new Google_Service_Calendar($this->client);
        // Use the Google Client calendar service. This gives us methods for interacting
        // with the Google Calendar API
        $calendarList = $gcHelper->calendarList->listCalendarList();

        $googleCalendarList = [];
        foreach ($calendarList->getItems() as $calendarListEntry) {
            if ($calendarListEntry->accessRole == 'owner') {
                $googleCalendarList[] = GoogleCalendarList::create([
                    'user_id' => getLoggedInUserId(),
                    'calendar_name' => $calendarListEntry['summary'],
                    'google_calendar_id' => $calendarListEntry['id'],
                    'meta' => json_encode($calendarListEntry),
                ]);
            }
        }

        return $googleCalendarList;
    }

    // For the save our google event calendar
    public function eventGoogleCalendarStore(Request $request)
    {
        $eventGoogleCalendars = EventGoogleCalendar::where('user_id', getLoggedInUserId())->get();

        if ($eventGoogleCalendars) {
            foreach ($eventGoogleCalendars as $eventGoogleCalendar) {
                $eventGoogleCalendar->delete();
            }
        }

        $input = $request->all();
        $googleCalendarIds = $input['google_calendar'];

        foreach ($googleCalendarIds as $googleCalendarId) {
            $googleCalendarListId = GoogleCalendarList::find($googleCalendarId)->google_calendar_id;
            $data = [
                'user_id' => getLoggedInUserId(),
                'google_calendar_list_id' => $googleCalendarId,
                'google_calendar_id' => $googleCalendarListId,
            ];

            EventGoogleCalendar::create($data);
        }

        return $this->sendSuccess(__('messages.google_meet.google_calendar_add'));
    }

    // For the Sync Google event Calendar
    public function syncGoogleCalendarList()
    {
        $this->getAccessToken(getLoggedInUserId());

        $gcHelper = new Google_Service_Calendar($this->client);
        // Use the Google Client calendar service. This gives us methods for interacting
        // with the Google Calendar API
        $calendarList = $gcHelper->calendarList->listCalendarList();

        $googleCalendarList = [];

        $existingCalendars = GoogleCalendarList::where('user_id', getLoggedInUserId())
            ->pluck('google_calendar_id', 'google_calendar_id')
            ->toArray();

        foreach ($calendarList->getItems() as $calendarListEntry) {
            if ($calendarListEntry->accessRole == 'owner') {
                $exists = GoogleCalendarList::where('user_id', getLoggedInUserId())
                    ->where('google_calendar_id', $calendarListEntry['id'])
                    ->first();
                unset($existingCalendars[$calendarListEntry['id']]);

                if (!$exists) {
                    $googleCalendarList[] = GoogleCalendarList::create([
                        'user_id' => getLoggedInUserId(),
                        'calendar_name' => $calendarListEntry['summary'],
                        'google_calendar_id' => $calendarListEntry['id'],
                        'meta' => json_encode($calendarListEntry),
                    ]);
                }
            }
        }

        EventGoogleCalendar::whereIn('google_calendar_id', $existingCalendars)->delete();
        GoogleCalendarList::whereIn('google_calendar_id', $existingCalendars)->delete();

        return $this->sendSuccess(__('messages.google_meet.google_calendar_update'));
    }

    // this function use for check token expiration
    public function getAccessToken($userId)
    {
        $this->googleConfig();

        $user = User::with('gCredentials')->find($userId);

        if (empty($user->gCredentials)) {
            throw new UnprocessableEntityHttpException(__('messages.google_meet.disconnect_or_reconnect'));
        }

        $accessToken = json_decode($user->gCredentials->meta, true);

        if (is_array($accessToken) && count($accessToken) == 0) {
            throw new UnprocessableEntityHttpException(__('messages.google_meet.disconnect_or_reconnect'));
        } elseif ($accessToken == null) {
            throw new UnprocessableEntityHttpException(__('messages.google_meet.disconnect_or_reconnect'));
        }

        if (empty($accessToken['access_token'])) {
            throw new UnprocessableEntityHttpException(__('messages.google_meet.disconnect_or_reconnect'));
        }

        try {
            // Refresh the token if it's expired.
            $this->client->setAccessToken($accessToken);

            if ($this->client->isAccessTokenExpired()) {
                Log::info('expired');

                if (isset($accessToken['refresh_token'])) {
                    $accessToken = $this->client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);

                }

                if (is_array($accessToken) && count($accessToken) == 0) {
                    throw new UnprocessableEntityHttpException(__('messages.google_meet.disconnect_or_reconnect'));
                } elseif ($accessToken == null) {
                    throw new UnprocessableEntityHttpException(__('messages.google_meet.disconnect_or_reconnect'));
                }

                if (empty($accessToken['access_token'])) {
                    throw new UnprocessableEntityHttpException(__('messages.google_meet.disconnect_or_reconnect'));
                }

                $calendarRecord = GoogleCalendarIntegration::where('user_id', $user->id)->first();
                $calendarRecord->update([
                    'access_token' => $accessToken['access_token'],
                    'meta' => json_encode($accessToken),
                    'last_used_at' => Carbon::now(),
                ]);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            throw new UnprocessableEntityHttpException($exception->getMessage());
        }

        return $accessToken['access_token'];
    }

    // For the delete / disconnect google calendar
    public function disconnectGoogleCalendar()
    {
        EventGoogleCalendar::where('user_id', getLoggedInUserId())->delete();
        GoogleCalendarIntegration::where('user_id', getLoggedInUserId())->delete();
        GoogleCalendarList::where('user_id', getLoggedInUserId())->delete();

        Flash::success(__('messages.google_meet.google_calendar_disconnect'));

        return redirect(route('connect-google-calendar.index'));
    }

    public function validateGoogleCalendarJsonFile($input)
    {
        $rules = [
            'google_json_file' => 'required|file|mimes:json',
        ];

        $messages = [
            'google_json_file.required' => __('messages.google_meet.upload_json_file'),
            'google_json_file.file' => __('messages.google_meet.upload_file'),
            'google_json_file.mimes' => __('messages.google_meet.invalid_json_format'),
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->first();
        }
    }

    public function googleCalendarJsonFileStore(Request $request)
    {
        $input = $request->all();

        $error = $this->validateGoogleCalendarJsonFile($input);

        if ($error) {
            Flash::error($error);
            return redirect()->route('connect-google-calendar.index');
        }

        if (!empty($input['google_json_file'])) {
            $doctor = Doctor::whereUserId(Auth::id())->first();
            $googleCalendarJsonFileMedia = $doctor->media->first();

            if ($googleCalendarJsonFileMedia !== null) {
                $doctor->deleteMedia($googleCalendarJsonFileMedia->id);
            }

            $googleCalendarJsonNewFile = $doctor->addMedia($input['google_json_file'])
                ->toMediaCollection(Doctor::GOOGLE_JSON_FILE_PATH, 'google_json_file');

            $jsonFilePath = $googleCalendarJsonNewFile->getPath();
            $googleJsonFilePath = str_replace(storage_path(), '', $jsonFilePath);

            $doctor->update(['google_json_file_path' => $googleJsonFilePath]);

            Flash::success(__('messages.google_meet.json_file_saved_successfully'));
        }

        return redirect()->route('connect-google-calendar.index');
    }
}
