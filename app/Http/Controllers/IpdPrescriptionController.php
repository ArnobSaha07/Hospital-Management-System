<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdPrescriptionRequest;
use App\Http\Requests\UpdateIpdPrescriptionRequest;
use App\Models\IpdPrescription;
use App\Models\Medicine;
use App\Repositories\IpdPrescriptionRepository;
use Exception;
use Flash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Response;
use Throwable;
use \PDF;

class IpdPrescriptionController extends AppBaseController
{
    /** @var IpdPrescriptionRepository */
    private $ipdPrescriptionRepository;

    public function __construct(IpdPrescriptionRepository $ipdPrescriptionRepo)
    {
        $this->ipdPrescriptionRepository = $ipdPrescriptionRepo;
    }

    /**
     * Store a newly created IpdPrescription in storage.
     */
    public function store(CreateIpdPrescriptionRequest $request): JsonResponse
    {
        $input = $request->all();
        $arr = collect($input['medicine_id']);
        $duplicateIds = $arr->duplicates();
        foreach ($input['medicine_id'] as $key => $value) {
            $medicine = Medicine::find($input['medicine_id'][$key]);
            if (! empty($duplicateIds)) {
                foreach ($duplicateIds as $key => $value) {
                    $medicine = Medicine::find($duplicateIds[$key]);

                    return $this->sendError(__('messages.medicine_bills.duplicate_medicine'));
                }
            }
        }
        foreach ($input['medicine_id'] as $key => $value) {
            $medicine = Medicine::find($input['medicine_id'][$key]);
            $qty = $input['day'][$key] * $input['dose_interval'][$key];
            if ($medicine->available_quantity < $qty) {
                $available = $medicine->available_quantity == null ? 0 : $medicine->available_quantity;

                return $this->sendError(__('messages.medicine_bills.available_quantity') .$medicine->name.__('messages.new_change.is') .$available.'.');

            }
        }
        $this->ipdPrescriptionRepository->store($input);
        $this->ipdPrescriptionRepository->createNotification($input);

        return $this->sendSuccess(__('messages.flash.IPD_Prescription_saved'));
    }

    /**
     * Display the specified IPD Prescription.
     *
     * @return array|string
     *
     * @throws Throwable
     */
    public function show(IpdPrescription $ipdPrescription)
    {
        if (! canAccessRecord(IpdPrescription::class, $ipdPrescription->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('ipd_prescriptions.show_ipd_prescription_data', compact('ipdPrescription'))->render();
    }

    /**
     * Show the form for editing the specified IpdPrescription.
     */
    public function edit(IpdPrescription $ipdPrescription): JsonResponse
    {
        if (! canAccessRecord(IpdPrescription::class, $ipdPrescription->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }
        $ipdPrescription->load(['ipdPrescriptionItems.medicine']);
        $ipdPrescriptionData = $this->ipdPrescriptionRepository->getIpdPrescriptionData($ipdPrescription);

        return $this->sendResponse($ipdPrescriptionData, __('messages.flash.IPD_Prescription_retrieved'));
    }

    /**
     * Update the specified IpdPrescriptionItem in storage.
     */
    public function update(IpdPrescription $ipdPrescription, UpdateIpdPrescriptionRequest $request): JsonResponse
    {

        $ipdPrescription->load('ipdPrescriptionItems');
        $prescriptionMedicineArray = [];
        $inputdoseAndMedicine = [];
        foreach ($ipdPrescription->ipdPrescriptionItems as $prescriptionMedicine) {
            $prescriptionMedicineArray[$prescriptionMedicine->medicine_id] = $prescriptionMedicine->dosage;
        }

        foreach ($request->medicine_id as $key => $value) {
            $inputdoseAndMedicine[$value] = $request->dosage[$key];
        }

        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        foreach ($input['medicine_id'] as $key => $value) {
            $result = array_intersect($prescriptionMedicineArray, $inputdoseAndMedicine);

            $medicine = Medicine::find($input['medicine_id'][$key]);
            $qty = $input['day'][$key] * $input['dose_interval'][$key];

            if ($medicine->available_quantity < $qty && ! array_key_exists($input['medicine_id'][$key], $result)) {
                $available = $medicine->available_quantity == null ? 0 : $medicine->available_quantity;

                return $this->sendError(__('messages.medicine_bills.available_quantity') .$medicine->name.__('messages.new_change.is') .$available.'.');
            }
        }

        $this->ipdPrescriptionRepository->updateIpdPrescriptionItems($request->all(), $ipdPrescription);

        return $this->sendSuccess(__('messages.flash.IPD_Prescription_updated'));
    }

    /**
     * Remove the specified IpdPrescriptionItem from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(IpdPrescription $ipdPrescription): JsonResponse
    {
        if (! canAccessRecord(IpdPrescription::class, $ipdPrescription->id)) {
            return $this->sendError(__('messages.flash.ipd_prescription_not_found'));
        }

        $ipdPrescription->ipdPrescriptionItems()->delete();
        $ipdPrescription->delete();

        return $this->sendSuccess(__('messages.flash.IPD_Prescription_deleted'));
    }

    public function getMedicineList(Request $request): JsonResponse
    {
        $chargeCategories = $this->ipdPrescriptionRepository->getMedicines($request->get('id'));

        return $this->sendResponse($chargeCategories, __('messages.flash.retrieve'));
    }

    public function convertToPDF($id): \Illuminate\Http\Response
    {
        if(app()->getLocale() == "zh"){
            app()->setLocale("en");
        }
        $ipdPrescription = IpdPrescription::find($id);

        $pdf = PDF::loadView('ipd_prescriptions.ipd_prescription_pdf', compact('ipdPrescription'));

        return $pdf->stream(__('messages.delete.ipd_prescription'));
    }
}
