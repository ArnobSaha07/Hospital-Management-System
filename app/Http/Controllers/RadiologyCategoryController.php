<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRadiologyCategoryRequest;
use App\Http\Requests\UpdateRadiologyCategoryRequest;
use App\Models\RadiologyCategory;
use App\Models\RadiologyTest;
use App\Repositories\RadiologyCategoryRepository;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RadiologyCategoryController extends AppBaseController
{
    /** @var RadiologyCategoryRepository */
    private $radiologyCategoryRepository;

    public function __construct(RadiologyCategoryRepository $radiologyCategoryRepo)
    {
        $this->middleware('check_menu_access');
        $this->radiologyCategoryRepository = $radiologyCategoryRepo;
    }

    /**
     * Display a listing of the RadiologyCategory.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('radiology_categories.index');
    }

    /**
     * Store a newly created RadiologyCategory in storage.
     */
    public function store(CreateRadiologyCategoryRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->radiologyCategoryRepository->create($input);

        return $this->sendSuccess(__('messages.flash.radiology_category_saved'));
    }

    /**
     * Show the form for editing the specified RadiologyCategory.
     */
    public function edit(RadiologyCategory $radiologyCategory): JsonResponse
    {
        if (! canAccessRecord(RadiologyCategory::class, $radiologyCategory->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($radiologyCategory, __('messages.flash.radiology_category_retrieved'));
    }

    /**
     * Update the specified RadiologyCategory in storage.
     */
    public function update(RadiologyCategory $radiologyCategory, UpdateRadiologyCategoryRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->radiologyCategoryRepository->update($input, $radiologyCategory->id);

        return $this->sendSuccess(__('messages.flash.radiology_category_updated'));
    }

    /**
     * Remove the specified RadiologyCategory from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(RadiologyCategory $radiologyCategory): JsonResponse
    {
        if (! canAccessRecord(RadiologyCategory::class, $radiologyCategory->id)) {
            return $this->sendError(__('messages.flash.radiology_category_not_found'));
        }

        $radiologyCategoryModels = [
            RadiologyTest::class,
        ];
        $result = canDelete($radiologyCategoryModels, 'category_id', $radiologyCategory->id);
        if ($result) {
            return $this->sendError(__('messages.flash.radiology_category_cant_deleted'));
        }

        $radiologyCategory->delete();

        return $this->sendSuccess(__('messages.flash.radiology_category_deleted'));
    }
}
