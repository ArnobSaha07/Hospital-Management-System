<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSmsRequest;
use App\Models\Sms;
use App\Models\Subscription;
use App\Repositories\SmsRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class SmsController extends AppBaseController
{
    /**
     * @var SmsRepository
     */
    private $smsRepository;

    public function __construct(SmsRepository $smsRepository)
    {
        $this->smsRepository = $smsRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $roles = Sms::ROLE_TYPES;

        return view('sms.index', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function store(CreateSmsRequest $request): JsonResponse
    {
        if (getLoggedInUser()->hasRole('Admin')) {
            $smsCount = Subscription::whereUserId(getLoggedInUserId())->whereStatus(1)->value('sms_limit');

            if (! isset($request->number)) {
                $smsCount = $smsCount - (count($request->send_to));
                if ($smsCount < 0) {
                    return $this->sendError(__('messages.flash.sms_limit_over'));
                }
            } else {
                if ($smsCount <= 0) {
                    return $this->sendError(__('messages.flash.sms_limit_over'));
                }
            }
        }

        $input = $request->all();
        $this->smsRepository->store($input);

        return $this->sendSuccess(__('messages.flash.sms_send'));
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|View
     */
    public function show(Sms $sms)
    {
        if (! canAccessRecord(Sms::class, $sms->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $sms = Sms::with('user.roles')->find($sms->id);

        return view('sms.show', compact('sms'));
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Sms $sms): JsonResponse
    {
        if (! canAccessRecord(Sms::class, $sms->id)) {
            return $this->sendError(__('messages.flash.sms_not_found'));
        }

        if (! getLoggedInUser()->hasRole('Admin')) {
            if (getLoggedInUser()->id != $sms->send_by) {
                return $this->sendError(__('messages.flash.sms_not_found'));
            }
        }

        $this->smsRepository->delete($sms->id);

        return $this->sendSuccess(__('messages.flash.sms_delete'));
    }

    public function getUsersList(Request $request): JsonResponse
    {
        if (empty($request->get('id'))) {
            return $this->sendError(__('messages.flash.user_list_not'));
        }

        $usersData = Sms::CLASS_TYPES[$request->id]::with('user')
            ->whereHas('user', function (Builder $query) {
                $query->whereNotNull('phone');
            })
            ->get()->where('user.status', '=', 1)
            ->pluck('user.full_name', 'user.id');

        return $this->sendResponse($usersData, __('messages.flash.retrieved'));
    }

    public function showModal(Sms $sms): JsonResponse
    {
        if (! canAccessRecord(Sms::class, $sms->id)) {
            return $this->sendError(__('messages.flash.sms_not_found'));
        }

        if (! getLoggedInUser()->hasRole('Admin')) {
            if (getLoggedInUser()->id != $sms->send_by) {
                return $this->sendError(__('messages.flash.sms_not_found'));
            }
        }

        $sms = Sms::with(['user.roles', 'sendBy'])->find($sms->id);

        return $this->sendResponse($sms, __('messages.flash.sms_retrieved'));
    }
}
