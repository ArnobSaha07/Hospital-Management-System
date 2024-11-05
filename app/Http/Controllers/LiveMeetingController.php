<?php

namespace App\Http\Controllers;

use App;
use App\Http\Requests\LiveMeetingRequest;
use App\Models\LiveMeeting;
use App\Repositories\LiveMeetingRepository;
use App\Repositories\ZoomRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveMeetingController extends AppBaseController
{
    /** @var LiveMeetingRepository */
    private $liveMeetingRepository;

        /** @var ZoomRepository */
        private $zoomRepository;

    /**
     * LiveMeetingController constructor.
     */
    public function __construct(LiveMeetingRepository $liveMeetingRepository,ZoomRepository $zoomRepository)
    {
        $this->liveMeetingRepository = $liveMeetingRepository;
        $this->zoomRepository = $zoomRepository;
    }

    /**
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $users = $this->liveMeetingRepository->getUsers();
        $status = LiveMeeting::status;

        return view('live_consultations.member_index', compact('users', 'status'));
    }

    public function liveMeetingStore(LiveMeetingRequest $request): JsonResponse
    {
        if (count($request->get('staff_list')) > 10) {
            return $this->sendError(__('messages.new_change.staff_limit'));
        }

        try {
            $this->liveMeetingRepository->store($request->all());
            $this->liveMeetingRepository->createNotification($request->all());

            return $this->sendSuccess(__('messages.flash.live_meeting_saved'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function getChangeStatus(Request $request): JsonResponse
    {
        $liveMeeting = LiveMeeting::findOrFail($request->get('id'));
        $status = null;

        if ($request->get('statusId') == LiveMeeting::STATUS_AWAITED) {
            $status = LiveMeeting::STATUS_AWAITED;
        } elseif ($request->get('statusId') == LiveMeeting::STATUS_CANCELLED) {
            $status = LiveMeeting::STATUS_CANCELLED;
        } else {
            $status = LiveMeeting::STATUS_FINISHED;
        }

        $liveMeeting->update([
            'status' => $status,
        ]);

        return $this->sendsuccess(__('messages.common.status_updated_successfully'));
    }

    public function getLiveStatus(LiveMeeting $liveMeeting): JsonResponse
    {
        $data['liveMeeting'] = LiveMeeting::with('user')->find($liveMeeting->id);

        /** @var ZoomRepository $zoomRepo */
        $zoomRepo = App::make(ZoomRepository::class, ['createdBy' => $liveMeeting->created_by]);
        $data['zoomLiveData'] = $zoomRepo->zoomGet($liveMeeting->meeting_id);

        return $this->sendResponse($data, __('messages.flash.live_meeting_retrieved'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LiveMeeting $liveMeeting): JsonResponse
    {
        if (! canAccessRecord(LiveMeeting::class, $liveMeeting->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        $liveMeeting->load('members');
        $meetingUsers = $liveMeeting->members->pluck('id')->toArray();
        $liveMeeting->setAttribute('meetingUsers', $meetingUsers);

        return $this->sendResponse($liveMeeting, __('messages.flash.live_meeting_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LiveMeetingRequest $request, LiveMeeting $liveMeeting): JsonResponse
    {
        if (count($request->get('staff_list')) > 10) {
            return $this->sendError(__('messages.new_change.staff_limit'));
        }

        try {
            $this->liveMeetingRepository->edit($request->all(), $liveMeeting);

            return $this->sendSuccess(__('messages.flash.live_meeting_updated'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function show(LiveMeeting $liveMeeting): JsonResponse
    {
        if (! canAccessRecord(LiveMeeting::class, $liveMeeting->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        $liveMeeting = LiveMeeting::with(['user'])->find($liveMeeting->id);

        return $this->sendResponse($liveMeeting, __('messages.flash.live_meeting_retrieved'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LiveMeeting $liveMeeting): JsonResponse
    {
        if (! canAccessRecord(LiveMeeting::class, $liveMeeting->id)) {
            return $this->sendError(__('messages.flash.live_meeting_not_found'));
        }

        try {
            $this->zoomRepository->destroyZoomMeeting($liveMeeting->meeting_id);
            $liveMeeting->delete();

            return $this->sendSuccess(__('messages.flash.live_meeting_deleted'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
