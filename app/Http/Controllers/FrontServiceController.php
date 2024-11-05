<?php

namespace App\Http\Controllers;

use App\Http\Requests\FrontServiceRequest;
use App\Models\FrontService;
use App\Repositories\FrontServiceRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontServiceController extends AppBaseController
{
    /** @var FrontServiceRepository */
    private $frontServiceRepository;

    public function __construct(FrontServiceRepository $frontServiceRepository)
    {
        $this->frontServiceRepository = $frontServiceRepository;
    }

    /**
     * @return Application|Factory|View
     *
     * @throws \Exception
     */
    public function index(Request $request): View
    {
        return view('front_settings.front_services.index');
    }

    /**
     * Store a newly created FrontService in storage.
     */
    public function store(FrontServiceRequest $request): JsonResponse
    {
        try {
            $input = $request->all();
            $this->frontServiceRepository->store($input);

            return $this->sendSuccess(__('messages.flash.frontService_saved'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified FrontService.
     */
    public function edit($id): JsonResponse
    {
        if (! canAccessRecord(FrontService::class, $id)) {
            return $this->sendError(__('messages.flash.front_service_not_found'));
        }

        $frontService = FrontService::find($id);

        return $this->sendResponse($frontService, __('messages.flash.frontService_retrieved'));
    }

    public function update($id, FrontServiceRequest $request): JsonResponse
    {
        try {
            $this->frontServiceRepository->updateFrontService($request->all(), $id);

            return $this->sendSuccess(__('messages.flash.frontService_updated'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified FrontService from storage.
     *
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        if (! canAccessRecord(FrontService::class, $id)) {
            return $this->sendError(__('messages.flash.front_service_not_found'));
        }

        try {
            $frontService = FrontService::find($id);
            $frontService->clearMediaCollection(FrontService::PATH);
            $frontService->delete();

            return $this->sendSuccess(__('messages.flash.frontService_deleted'));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
