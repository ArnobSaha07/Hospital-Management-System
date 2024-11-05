<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpdPrescriptionRequest;
use App\Http\Requests\UpdateOpdPrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Models\Medicine;
use App\Models\OpdPrescription;
use App\Repositories\OpdPrescriptionRepository;
use Illuminate\Http\Request;
use Exception;
use Flash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;
use Throwable;
use \PDF;

class OpdPrescriptionController extends AppBaseController
{
    /** @var OpdPrescriptionRepository */
    private $opdPrescriptionRepository;

    public function __construct(OpdPrescriptionRepository $opdPrescriptionRepo)
    {
        $this->opdPrescriptionRepository = $opdPrescriptionRepo;
    }

    /**
     * Store a newly created OpdPrescription in storage.
     */
    public function store(CreateOpdPrescriptionRequest $request): JsonResponse
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

                return $this->sendError(__('messages.medicine_bills.available_quantity') .' '.$medicine->name.' '.__('messages.new_change.is') .' '.$available.'.');

            }
        }
        $this->opdPrescriptionRepository->store($input);
        $this->opdPrescriptionRepository->createNotification($input);

        return $this->sendSuccess(__('messages.flash.IPD_Prescription_saved'));
    }

    /**
     * Display the specified OPD Prescription.
     *
     * @return array|string
     *
     * @throws Throwable
     */
    public function show(OpdPrescription $opdPrescription)
    {
        if (! canAccessRecord(OpdPrescription::class, $opdPrescription->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('opd_prescriptions.show_opd_prescription_data', compact('opdPrescription'))->render();
    }

    /**
     * Show the form for editing the specified OpdPrescription.
     */
    public function edit(OpdPrescription $opdPrescription): JsonResponse
    {
        if (! canAccessRecord(OpdPrescription::class, $opdPrescription->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }
        $opdPrescription->load(['opdPrescriptionItems.medicine']);
        $opdPrescriptionData = $this->opdPrescriptionRepository->getOpdPrescriptionData($opdPrescription);

        return $this->sendResponse($opdPrescriptionData, __('messages.flash.IPD_Prescription_retrieved'));
    }

    /**
     * Update the specified OpdPrescriptionItem in storage.
     */
    public function update(OpdPrescription $opdPrescription, UpdateOpdPrescriptionRequest $request): JsonResponse
    {
        $opdPrescription->load('opdPrescriptionItems');
        $prescriptionMedicineArray = [];
        $inputdoseAndMedicine = [];
        foreach ($opdPrescription->opdPrescriptionItems as $prescriptionMedicine) {
            $prescriptionMedicineArray[$prescriptionMedicine->medicine_id] = $prescriptionMedicine->dosage;
        }

        foreach ($request->medicine_id as $key => $value) {
            $inputdoseAndMedicine[$value] = $request->dosage[$key];
        }

        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $arr = collect($input['medicine_id']);
        $duplicateIds = $arr->duplicates();

        foreach ($input['medicine_id'] as $key => $value) {
            $result = array_intersect($prescriptionMedicineArray, $inputdoseAndMedicine);

            $medicine = Medicine::find($input['medicine_id'][$key]);
            $qty = $input['day'][$key] * $input['dose_interval'][$key];

            if (! empty($duplicateIds)) {
                foreach ($duplicateIds as $key => $value) {
                    $medicine = Medicine::find($duplicateIds[$key]);

                    return $this->sendError(__('messages.medicine_bills.duplicate_medicine'));
                }
            }

            if ($medicine->available_quantity < $qty && ! array_key_exists($input['medicine_id'][$key], $result)) {
                $available = $medicine->available_quantity == null ? 0 : $medicine->available_quantity;

                return $this->sendError(__('messages.medicine_bills.available_quantity') .' '.$medicine->name.' '.__('messages.new_change.is') .' '.$available.'.');
            }
        }

        $this->opdPrescriptionRepository->updateOpdPrescriptionItems($request->all(), $opdPrescription);

        return $this->sendSuccess(__('messages.flash.IPD_Prescription_updated'));
    }

    /**
     * Remove the specified OpdPrescriptionItem from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(OpdPrescription $opdPrescription): JsonResponse
    {
        if (! canAccessRecord(OpdPrescription::class, $opdPrescription->id)) {
            return $this->sendError(__('messages.flash.ipd_prescription_not_found'));
        }

        $opdPrescription->opdPrescriptionItems()->delete();
        $opdPrescription->delete();

        return $this->sendSuccess(__('messages.flash.IPD_Prescription_deleted'));
    }

    public function convertToPDF($id): \Illuminate\Http\Response
    {
        if(app()->getLocale() == "zh"){
            app()->setLocale("en");
        }
        $opdPrescription = OpdPrescription::find($id);

        $pdf = PDF::loadView('opd_prescriptions.opd_prescription_pdf', compact('opdPrescription'));

        return $pdf->stream(__('messages.delete.prescription'));
    }

    public function getMedicineList(Request $request)
    {
        $chargeCategories = $this->opdPrescriptionRepository->getMedicines($request->get('id'));

        return $this->sendResponse($chargeCategories, 'Retrieved successfully');
    }
}
