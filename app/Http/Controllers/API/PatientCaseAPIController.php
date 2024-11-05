<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\PatientCase;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class PatientCaseAPIController extends AppBaseController
{
    public function index(): JsonResponse
    {
        $patientCases = PatientCase::with('doctor')->where('patient_id', getLoggedInUser()->owner_id)->orderBy('id', 'desc')->get();
        $appLogo = Setting::whereTenantId(getLoggedInUser()->tenant_id)->pluck('value', 'key')->toArray();

        $data = [];


        foreach ($patientCases as $patientCase) {
            /** @var PatientCase $patientCase */
            $patientCaseData = $patientCase->preparePatientCase();
            $patientCaseData['app_logo'] = $appLogo['app_logo'];

            $data[] = $patientCaseData;
        }

        return $this->sendResponse($data, 'Patient Cases Retrieved successfully.');
    }

    public function show($id): JsonResponse
    {
        $patientCase = PatientCase::with('doctor')->where('id', $id)->where('patient_id', getLoggedInUser()->owner_id)->first();
        $appLogo = Setting::whereTenantId(getLoggedInUser()->tenant_id)->pluck('value', 'key')->toArray();

        if (! $patientCase) {
            return $this->sendError(__('messages.patients_cases').' '.__('messages.common.not_found'));
        }
        $patientCaseDetails = $patientCase->preparePatientCase();
        $patientCaseDetails['app_logo'] = $appLogo['app_logo'];
        return $this->sendResponse($patientCaseDetails, 'Patient Cases Retrieved successfully.');
    }
}
