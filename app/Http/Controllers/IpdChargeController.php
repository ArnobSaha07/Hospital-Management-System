<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdChargeRequest;
use App\Http\Requests\UpdateIpdChargeRequest;
use App\Models\IpdCharge;
use App\Repositories\IpdChargeRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Response;

class IpdChargeController extends AppBaseController
{
    /** @var IpdChargeRepository */
    private $ipdChargeRepository;

    public function __construct(IpdChargeRepository $ipdChargeRepo)
    {
        $this->ipdChargeRepository = $ipdChargeRepo;
    }

    /**
     * Display a listing of the IpdCharge.
     *
     *
     * @throws Exception
     */
    public function index(Request $request): Response
    {
    }

    /**
     * Store a newly created IpdCharge in storage.
     */
    public function store(CreateIpdChargeRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $input['applied_charge'] = removeCommaFromNumbers($input['applied_charge']);
        $this->ipdChargeRepository->create($input);
        $this->ipdChargeRepository->createNotification($input);

        return $this->sendSuccess(__('messages.flash.IPD_charge_saved'));
    }

    /**
     * Show the form for editing the specified Ipd Diagnosis.
     */
    public function edit(IpdCharge $ipdCharge): JsonResponse
    {
        if (! canAccessRecord(IpdCharge::class, $ipdCharge->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($ipdCharge, __('messages.flash.IPD_charge_retrieved'));
    }

    /**
     * Update the specified Ipd Diagnosis in storage.
     */
    public function update(IpdCharge $ipdCharge, UpdateIpdChargeRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $input['applied_charge'] = removeCommaFromNumbers($input['applied_charge']);
        $this->ipdChargeRepository->update($input, $ipdCharge->id);

        return $this->sendSuccess(__('messages.flash.IPD_charge_updated'));
    }

    /**
     * Remove the specified IpdCharge from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(IpdCharge $ipdCharge): JsonResponse
    {
        if (! canAccessRecord(IpdCharge::class, $ipdCharge->id)) {
            return $this->sendError(__('messages.flash.ipd_charge_not_found'));
        }

        $ipdCharge->delete();

        return $this->sendSuccess(__('messages.flash.IPD_charge_deleted'));
    }

    public function getChargeCategoryList(Request $request): JsonResponse
    {
        $chargeCategories = $this->ipdChargeRepository->getChargeCategories($request->get('id'));

        return $this->sendResponse($chargeCategories, __('messages.flash.retrieve'));
    }

    public function getChargeList(Request $request): JsonResponse
    {
        $charges = $this->ipdChargeRepository->getCharges($request->get('id'));

        return $this->sendResponse($charges, __('messages.flash.retrieve'));
    }

    public function getChargeStandardRate(Request $request): JsonResponse
    {
        $chargeStandardRate = $this->ipdChargeRepository->getChargeStandardRate($request->get('id'),
            $request->get('isEdit'), $request->get('onceOnEditRender'), $request->get('ipdChargeId'));

        return $this->sendResponse($chargeStandardRate, __('messages.flash.retrieve'));
    }
}
