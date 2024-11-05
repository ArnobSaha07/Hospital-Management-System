<?php

namespace App\Repositories;

use App;
use App\Mail\NotifyMailLiveMeeting;
use App\Models\LiveMeeting;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserTenant;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class LiveMeetingRepository
 */
class LiveMeetingRepository extends BaseRepository
{
    const MEETING_TYPE_INSTANT = 1;

    const MEETING_TYPE_SCHEDULE = 2;

    const MEETING_TYPE_RECURRING = 3;

    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'consultation_title',
        'consultation_date',
        'consultation_duration_minutes',
        'description',
    ];

    /**
     * Return searchable fields
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LiveMeeting::class;
    }

    /**
     * @throws BindingResolutionException
     */
    public function store(array $input): bool
    {
        /** @var ZoomRepository $zoomRepo */
        $zoomRepo = App::makeWith(ZoomRepository::class, ['createdBy' => getLoggedInUserId()]);

        try {
            $input['created_by'] = getLoggedInUserId();
            $startTime = $input['consultation_date'];
            $input['consultation_date'] = Carbon::parse($startTime)->format('Y-m-d H:i:s');
            $zoom = $zoomRepo->createZoomMeeting($input);
            $input['password'] = $zoom['password'];
            $input['meeting_id'] = $zoom['id'];
            $input['meta'] = $zoom;

            $zoomModel = LiveMeeting::create($input);
            $zoomModel->members()->attach($input['staff_list']);

            $userId = UserTenant::whereTenantId(getLoggedInUser()->tenant_id)->value('user_id');
            $hospitalDefaultAdmin = User::whereId($userId)->first();

            if (! empty($hospitalDefaultAdmin)) {

                $hospitalDefaultAdminEmail = $hospitalDefaultAdmin->email;

                $mailData = [
                    'consultation_title' => $input['consultation_title'],
                    'consultation_date' => $input['consultation_date'],
                    'consultation_duration_time' => $input['consultation_duration_minutes'],
                    'created_by' => getLoggedInUser()->full_name,
                ];

                $mailData['zoom_redirect_url'] = $input['meta']['start_url'];

                Mail::to(getLoggedInUser()->email)
                    ->send(new NotifyMailLiveMeeting('emails.live_meeting_created_mail',
                        __('messages.new_change.meeting_create'),
                        $mailData));

                $mailData['zoom_redirect_url'] = $input['meta']['join_url'];

                if (getLoggedInUser()->email != $hospitalDefaultAdminEmail) {
                    Mail::to($hospitalDefaultAdminEmail)
                        ->send(new NotifyMailLiveMeeting('emails.live_meeting_created_mail',
                        __('messages.new_change.meeting_create'),
                            $mailData));
                }

                foreach ($input['staff_list'] as $userId) {
                    $user = User::withoutGlobalScope(new \Stancl\Tenancy\Database\TenantScope())->whereId($userId)->first();
                    if (! empty($user)) {
                        if ($user->email != $hospitalDefaultAdminEmail && $user->email != getLoggedInUser()->email) {
                            Mail::to($user->email)
                                ->send(new NotifyMailLiveMeeting('emails.live_meeting_created_mail',
                                __('messages.new_change.meeting_create'),
                                    $mailData));
                        }
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function edit(array $input, LiveMeeting $liveMeeting): bool
    {
        try {
            /** @var ZoomRepository $zoomRepo */
            $zoomRepo = App::make(ZoomRepository::class, ['createdBy' => $liveMeeting->created_by]);

            $zoomSessionUpdate = $zoomRepo->updateZoomMeeting($input,$liveMeeting);
            $input['created_by'] = $liveMeeting->created_by != getLoggedInUserId() ? $liveMeeting->created_by : getLoggedInUserId();
            $startTime = $input['consultation_date'];
            $input['consultation_date'] = Carbon::parse($startTime)->format('Y-m-d H:i:s');

            $liveMeeting->update($input);
            $liveMeeting->members()->sync($input['staff_list']);

            return true;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function getUsers(): array
    {
        try {
            $roles = User::orderBy('first_name')->whereHas('roles', function (Builder $query) {
                $query->where('name', '!=', 'Patient');
            })->where('status', '=', 1)->get();
            $result = [];
            foreach ($roles as $role) {
                foreach ($role->roles as $roleName) {
                    $result[$role->id] = $role->full_name.' ('.$roleName->name.')';
                }
            }

            return $result;
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function createNotification(array $input = [])
    {
        try {
            $id = $input['staff_list'];
            $users = [];
            foreach ($id as $key => $value) {
                $users[$value] = User::where('id', $value)->pluck('owner_type', 'id')->first();
            }

            foreach ($users as $key => $userId) {
                $userIds[$key] = Notification::NOTIFICATION_FOR[User::getOwnerType($userId)];
            }

            unset($userIds[getLoggedInUserId()]);

            foreach ($userIds as $key => $notification) {
                addNotification([
                    Notification::NOTIFICATION_TYPE['Live Meeting'],
                    $key,
                    $notification,
                    getLoggedInUser()->first_name.' '.getLoggedInUser()->last_name.' has been created a live meeting.',
                ]);
            }
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}
