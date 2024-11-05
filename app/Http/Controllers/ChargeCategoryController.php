<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateChargeCategoryRequest;
use App\Http\Requests\UpdateChargeCategoryRequest;
use App\Models\ChargeCategory;
use App\Models\RadiologyTest;
use App\Repositories\ChargeCategoryRepository;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ChargeCategoryController extends AppBaseController
{
    /** @var ChargeCategoryRepository */
    private $chargeCategoryRepository;

    public function __construct(ChargeCategoryRepository $chargeCategoryRepo)
    {
        $this->chargeCategoryRepository = $chargeCategoryRepo;
    }

    /**
     * Display a listing of the ChargeCategory.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $chargeTypes = ChargeCategory::CHARGE_TYPES;
        asort($chargeTypes);

        return view('charge_categories.index', compact('chargeTypes'));
    }

    /**
     * Store a newly created ChargeCategory in storage.
     */
    public function store(CreateChargeCategoryRequest $request): JsonResponse
    {
        $input = $request->all();

        $chargeCategory = $this->chargeCategoryRepository->create($input);

        return $this->sendSuccess(__('messages.flash.charge_category_saved'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(ChargeCategory $chargeCategory)
    {
        if (! canAccessRecord(ChargeCategory::class, $chargeCategory->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $chargeTypes = ChargeCategory::CHARGE_TYPES;

        return view('charge_categories.show', compact('chargeCategory', 'chargeTypes'));
    }

    /**
     * Show the form for editing the specified ChargeCategory.
     */
    public function edit(ChargeCategory $chargeCategory): JsonResponse
    {
        if (! canAccessRecord(ChargeCategory::class, $chargeCategory->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($chargeCategory, __('messages.flash.charge_category_retrieved'));
    }

    /**
     * Update the specified ChargeCategory in storage.
     */
    public function update(ChargeCategory $chargeCategory, UpdateChargeCategoryRequest $request): JsonResponse
    {
        $chargeCategory = $this->chargeCategoryRepository->update($request->all(), $chargeCategory->id);

        return $this->sendSuccess(__('messages.flash.charge_category_updated'));
    }

    /**
     * Remove the specified ChargeCategory from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(ChargeCategory $chargeCategory): JsonResponse
    {
        if (! canAccessRecord(ChargeCategory::class, $chargeCategory->id)) {
            return $this->sendError(__('messages.flash.charge_category_not_found'));
        }

        $chargeCategoryModels = [
            RadiologyTest::class,
        ];
        $result = canDelete($chargeCategoryModels, 'charge_category_id', $chargeCategory->id);
        if ($result) {
            return $this->sendError(__('messages.flash.charge_category_not_found'));
        }
        $this->chargeCategoryRepository->delete($chargeCategory->id);

        return $this->sendSuccess(__('messages.flash.charge_category_deleted'));
    }
}
