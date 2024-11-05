<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\CreateServiceSliderRequest;
use App\Http\Requests\UpdateServiceSliderRequest;
use App\Models\ServiceSlider;
use App\Repositories\ServiceSliderRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class ServiceSliderController extends AppBaseController
{
    /**
     * @var ServiceSliderRepository
     */
    protected $serviceSliderRepo;

    public function __construct(ServiceSliderRepository $serviceSliderRepository)
    {
        $this->serviceSliderRepo = $serviceSliderRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): \Illuminate\View\View
    {
        return view('landing.service_slider.index');
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function store(createServiceSliderRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->serviceSliderRepo->store($input);

        return $this->sendSuccess(__('messages.new_change.service_slider_store'));
    }

    public function edit(ServiceSlider $serviceSlider): JsonResponse
    {
        return $this->sendResponse($serviceSlider, 'ServiceSlider retrieved successfully.');
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(UpdateServiceSliderRequest $request, $id): JsonResponse
    {
        $input = $request->all();
        $this->serviceSliderRepo->update($input, $id);

        return $this->sendSuccess(__('messages.new_change.service_slider_update'));
    }

    public function destroy(ServiceSlider $serviceSlider): JsonResponse
    {
        $serviceSlider->delete();

        return $this->sendSuccess('service Slider deleted successfully.');
    }
}
