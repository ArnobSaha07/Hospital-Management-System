<?php

namespace App\Http\Controllers;

use App;
use App\Http\Requests\CreateZoomCredentialRequest;
use App\Http\Requests\LiveConsultationRequest;
use App\Models\LiveConsultation;
use App\Models\UserGoogleEventSchedule;
use App\Models\UserZoomCredential;
use App\Repositories\LiveConsultationRepository;
use App\Repositories\PatientCaseRepository;
use App\Repositories\ZoomRepository;
use Auth;
use DB;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App as FacadesApp;

/**
 * Class LiveConsultationController
 */
class LiveConsultationController extends AppBaseController
{
    /** @var LiveConsultationRepository */
    private $liveConsultationRepository;

    /** @var PatientCaseRepository */
    private $patientCaseRepository;

    /** @var ZoomRepository */
    private $zoomRepository;

    /**
     * LiveConsultationController constructor.
     */
    public function __construct(
        LiveConsultationRepository $liveConsultationRepository,
        PatientCaseRepository $patientCaseRepository,
        ZoomRepository $zoomRepository
    ) {
        $this->liveConsultationRepository = $liveConsultationRepository;
        $this->patientCaseRepository = $patientCaseRepository;
        $this->zoomRepository = $zoomRepository;
    }

    /**
     * Display a listing of the LabTechnician.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $doctors = $this->patientCaseRepository->getDoctors();
        $patients = $this->patientCaseRepository->getPatients();
        $type = LiveConsultation::STATUS_TYPE;
        $status = LiveConsultation::status;

        return view('live_consultations.index', compact('doctors', 'patients', 'type', 'status'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LiveConsultationRequest $request): JsonResponse
    {
        try {

            DB::beginTransaction();

            if($request->platform_type == LiveConsultation::GOOGLE_MEET){

                /** @var GoogleMeetCalendarController $getAccessToken */
                $getAccessToken = App::make(GoogleMeetCalendarController::class);
                $getAccessToken->getAccessToken(getLoggedInUserId());

                $this->liveConsultationRepository->googleMeetStore($request->all());
            }else{
                $this->liveConsultationRepository->store($request->all());
            }

            $this->liveConsultationRepository->createNotification($request->all());

            DB::commit();

            return $this->sendSuccess(__('messages.flash.live_consultation_saved'));
        } catch (Exception $e) {
            DB::rollBack();

            $responseData = json_decode($e->getMessage(), true);

            if (isset($responseData['error'])) {
                $errorCode = $responseData['error']['code'];

                if($errorCode == 401){
                    return $this->sendError(__('messages.google_meet.disconnect_or_reconnect'));
                }
            }

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LiveConsultation $liveConsultation): JsonResponse
    {
        if (! canAccessRecord(LiveConsultation::class, $liveConsultation->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($liveConsultation, __('messages.flash.live_consultation_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LiveConsultationRequest $request, LiveConsultation $liveConsultation): JsonResponse
    {
        try {
            $this->liveConsultationRepository->edit($request->all(), $liveConsultation);

            return $this->sendSuccess(__('messages.flash.live_consultation_updated'));
        } catch (Exception $e) {

            $responseData = json_decode($e->getMessage(), true);

            if (isset($responseData['error'])) {
                $errorCode = $responseData['error']['code'];

                if($errorCode == 401){
                    return $this->sendError(__('messages.google_meet.disconnect_or_reconnect'));
                }
            }

            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LiveConsultation $liveConsultation): JsonResponse
    {
        if (! canAccessRecord(LiveConsultation::class, $liveConsultation->id)) {
            return $this->sendError(__('messages.flash.live_consultation_not_found'));
        }

        try {
            if($liveConsultation->platform_type == LiveConsultation::GOOGLE_MEET){
                $userGoogleEventCalendar = UserGoogleEventSchedule::where(['user_id' => Auth::id(),'google_live_consultation_id' => $liveConsultation->id])->first();
                $userGoogleEventCalendar->delete();
                $liveConsultation->delete();
            }else{
                $this->zoomRepository->destroyZoomMeeting($liveConsultation->meeting_id);
                $liveConsultation->delete();
            }

            return $this->sendSuccess(__('messages.flash.live_consultation_deleted'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function getTypeNumber(Request $request): JsonResponse
    {
        try {
            $typeNumber = $this->liveConsultationRepository->getTypeNumber($request->all());

            return $this->sendResponse($typeNumber, 'Type Number Retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function getChangeStatus(Request $request): JsonResponse
    {
        $liveConsultation = LiveConsultation::findOrFail($request->get('id'));
        $status = null;

        if ($request->get('statusId') == LiveConsultation::STATUS_AWAITED) {
            $status = LiveConsultation::STATUS_AWAITED;
        } elseif ($request->get('statusId') == LiveConsultation::STATUS_CANCELLED) {
            $status = LiveConsultation::STATUS_CANCELLED;
        } else {
            $status = LiveConsultation::STATUS_FINISHED;
        }

        $liveConsultation->update([
            'status' => $status,
        ]);

        return $this->sendsuccess(__('messages.common.status_updated_successfully'));
    }

    public function getLiveStatus(LiveConsultation $liveConsultation): JsonResponse
    {
        $data['liveConsultation'] = LiveConsultation::with('user')->find($liveConsultation->id);
        /** @var ZoomRepository $zoomRepo */
        $zoomRepo = App::make(ZoomRepository::class, ['createdBy' => $liveConsultation->created_by]);

        $data['zoomLiveData'] = $zoomRepo->zoomGet($liveConsultation->meeting_id);

        return $this->sendResponse($data, __('messages.flash.live_status_retrieved'));
    }

    public function show(LiveConsultation $liveConsultation): JsonResponse
    {
        $data['liveConsultation'] = LiveConsultation::with([
            'user', 'patient.patientUser', 'doctor.doctorUser', 'opdPatient', 'ipdPatient',
        ])->find($liveConsultation->id);
        $data['typeNumber'] = ($liveConsultation->type == LiveConsultation::OPD) ? $liveConsultation->opdPatient->opd_number : $liveConsultation->ipdPatient->ipd_number;

        return $this->sendResponse($data, __('messages.flash.live_consultation_retrieved'));
    }

    public function zoomCredential(int $id): JsonResponse
    {
        try {
            $data = UserZoomCredential::where('user_id', $id)->first();

            return $this->sendResponse($data, __('messages.flash.user_zoom_credential_retrieved'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function zoomCredentialCreate(CreateZoomCredentialRequest $request): JsonResponse
    {
        try {
            $this->liveConsultationRepository->createUserZoom($request->all());

            return $this->sendSuccess(__('messages.flash.user_zoom_credential_saved'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function zoomConnect(Request $request)
    {
        $userZoomCredential = UserZoomCredential::where('user_id', getLoggedInUserId())->first();
        if ($userZoomCredential == null) {
            app()->setLocale(getLoggedInUser()->language);
            return redirect()->back()->withErrors(__('messages.new_change.add_credential'));
        }
        $clientID = $userZoomCredential->zoom_api_key;
        $callbackURL = config('app.zoom_callback');
        $url = "https://zoom.us/oauth/authorize?client_id=$clientID&response_type=code&redirect_uri=$callbackURL";

        return redirect($url);
    }

    public function zoomCallback(Request $request)
    {
        /** $zoomRepo Zoom */
        $zoomRepo = FacadesApp::make(ZoomRepository::class);
        $zoomRepo->connectWithZoom($request->get('code'));

        return redirect(route('live.consultation.index'));
    }
}
