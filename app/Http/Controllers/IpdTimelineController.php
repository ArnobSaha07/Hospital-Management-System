<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdTimelineRequest;
use App\Http\Requests\UpdateIpdTimelineRequest;
use App\Models\IpdTimeline;
use App\Repositories\IpdTimelineRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class IpdTimelineController extends AppBaseController
{
    /** @var IpdTimelineRepository */
    private $ipdTimelineRepository;

    public function __construct(IpdTimelineRepository $ipdTimelineRepo)
    {
        $this->ipdTimelineRepository = $ipdTimelineRepo;
    }

    /**
     * Display a listing of the IpdTimeline.
     *
     * @return array|string
     *
     * @throws Throwable
     */
    public function index(Request $request)
    {
        $ipdTimelines = $this->ipdTimelineRepository->getTimeLines($request->get('id'));

        return view('ipd_timelines.index', compact('ipdTimelines'))->render();
    }

    /**
     * Store a newly created IpdTimeline in storage.
     */
    public function store(CreateIpdTimelineRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->ipdTimelineRepository->store($input);

        return $this->sendSuccess(__('messages.flash.IPD_timeline_saved'));
    }

    /**
     * Show the form for editing the specified IpdTimeline.
     */
    public function edit(IpdTimeline $ipdTimeline): JsonResponse
    {
        if (! canAccessRecord(IpdTimeline::class, $ipdTimeline->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($ipdTimeline, __('messages.flash.IPD_timeline_retrieved'));
    }

    /**
     * Update the specified IpdTimeline in storage.
     */
    public function update(IpdTimeline $ipdTimeline, UpdateIpdTimelineRequest $request): JsonResponse
    {
        $this->ipdTimelineRepository->updateIpdTimeline($request->all(), $ipdTimeline->id);

        return $this->sendSuccess(__('messages.flash.IPD_timeline_updated'));
    }

    /**
     * Remove the specified IpdTimeline from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(IpdTimeline $ipdTimeline): JsonResponse
    {
        if (! canAccessRecord(IpdTimeline::class, $ipdTimeline->id)) {
            return $this->sendError(__('messages.flash.ipd_timeline_not_found'));
        }

        $this->ipdTimelineRepository->deleteIpdTimeline($ipdTimeline->id);

        return $this->sendSuccess(__('messages.flash.IPD_timeline_deleted'));
    }

    public function downloadMedia(IpdTimeline $ipdTimeline): Media
    {
        $media = $ipdTimeline->getMedia(IpdTimeline::IPD_TIMELINE_PATH)->first();
        if ($media != null) {
            $media = $media->id;
            $mediaItem = Media::findOrFail($media);

            return $mediaItem;
        }

        return '';
    }
}
