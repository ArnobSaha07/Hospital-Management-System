<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Bill;
use App\Models\Setting;

class BillAPIController extends AppBaseController
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        if (getLoggedinPatient()) {
            $bills = Bill::where('patient_id', getLoggedInUser()->patient->id)->orderBy('id', 'desc')->get();
            $data = [];
            foreach ($bills as $bill) {
                $data[] = $bill->prepareBills();
            }

            return $this->sendResponse($data, 'Bills Retrieved Successfully');
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $bill = Bill::with(['billItems.medicine', 'patient', 'patientAdmission'])->where('id', $id)->where('patient_id', getLoggedInUser()->patient->id)->first();
        $appLogo = Setting::whereTenantId(getLoggedInUser()->tenant_id)->pluck('value', 'key')->toArray();

        if (! $bill) {
            return $this->sendError(__('messages.bill.bill').' '.__('messages.common.not_found'));
        }
        
        $billDetails = $bill->prepareBillDetails();
        $billDetails['app_logo'] = $appLogo['app_logo'];
        return $this->sendResponse($billDetails, 'Bill Retrieved Successfully');
    }
}
