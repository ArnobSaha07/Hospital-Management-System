<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpdTimelineRequest;
use App\Http\Requests\UpdateOpdTimelineRequest;
use App\Models\OpdTimeline;
use App\Repositories\OpdTimelineRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class OpdTimelineController extends AppBaseController
{
    /** @var OpdTimelineRepository */
    private $opdTimelineRepository;

    public function __construct(OpdTimelineRepository $opdTimelineRepo)
    {
        $this->opdTimelineRepository = $opdTimelineRepo;
    }

    /**
     * Display a listing of the OpdTimeline.
     *
     * @return array|string
     *
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $opdTimelines = $this->opdTimelineRepository->getTimeLines($request->get('id'));

        return view('opd_timelines.index', compact('opdTimelines'))->render();
    }

    /**
     * Store a newly created OpdTimeline in storage.
     */
    public function store(CreateOpdTimelineRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->opdTimelineRepository->store($input);

        return $this->sendSuccess(__('messages.flash.OPD_timeline_saved'));
    }

    /**
     * Show the form for editing the specified OpdTimeline.
     */
    public function edit(OpdTimeline $opdTimeline): JsonResponse
    {
        if (! canAccessRecord(OpdTimeline::class, $opdTimeline->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($opdTimeline, __('messages.flash.OPD_timeline_retrieved'));
    }

    /**
     * Update the specified OpdTimeline in storage.
     */
    public function update(OpdTimeline $opdTimeline, UpdateOpdTimelineRequest $request): JsonResponse
    {
        $this->opdTimelineRepository->updateOpdTimeline($request->all(), $opdTimeline->id);

        return $this->sendSuccess(__('messages.flash.OPD_timeline_updated'));
    }

    /**
     * Remove the specified OpdTimeline from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(OpdTimeline $opdTimeline): JsonResponse
    {
        if (! canAccessRecord(OpdTimeline::class, $opdTimeline->id)) {
            return $this->sendError(__('messages.flash.opd_timeline_not_found'));
        }

        $this->opdTimelineRepository->deleteOpdTimeline($opdTimeline->id);

        return $this->sendSuccess(__('messages.flash.OPD_timeline_deleted'));
    }

    public function downloadMedia(OpdTimeline $opdTimeline): Media
    {
        $media = $opdTimeline->getMedia(OpdTimeline::OPD_TIMELINE_PATH)->first();
        if ($media) {
            return $media;
        }

        return '';
    }
}
