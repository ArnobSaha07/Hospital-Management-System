<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\AppBaseController;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientAPIController extends AppBaseController
{
    public function index(): JsonResponse
    {
        $patients = Patient::query()->where('tenant_id',getLoggedInUser()->tenant_id)->with('patientUser')->orderBy('id', 'desc')->get();

        $data = [];
        foreach ($patients as $patient) {
            $data[] = $patient->prepareData();
        }

        return $this->sendResponse($data, 'Patients Retrieved Successfully');
    }

    public function show($id): JsonResponse
    {
        $patient = Patient::where('tenant_id',getLoggedInUser()->tenant_id)->with('patientUser')->find($id);

        return $this->sendResponse($patient->preparePatientDetail(), 'Patient Retrieved Successfully');
    }

    public function filter(Request $request): JsonResponse
    {
        $status = $request->get('status');
        $patientsQuery = Patient::where('tenant_id', getLoggedInUser()->tenant_id)->with('patientUser');
        $data = [];

        if ($status == 'all') {
            $patients = $patientsQuery->orderBy('id', 'desc')->get();
            foreach ($patients as $patient) {
                $data[] = $patient->prepareData();
            }

            return $this->sendResponse($data, 'Patients Retrieved Successfully');
        } elseif ($status == 'deactive') {
            $patients = $patientsQuery->whereHas('user', function ($q) {
                $q->where('status', 0);
            })->orderBy('id', 'desc')->get();

            foreach ($patients as $patient) {
                $data[] = $patient->prepareData();
            }

            return $this->sendResponse($data, 'Patients Retrieved Successfully');
        }elseif ($status == 'active') {
            $patients = $patientsQuery->whereHas('user', function ($q) {
                $q->where('status', 1);
            })->orderBy('id', 'desc')->get();

            foreach ($patients as $patient) {
                $data[] = $patient->prepareData();
            }

            return $this->sendResponse($data, 'Patients Retrieved Successfully');
        }  else {
            return $this->sendError('Patient not found');
        }
    }
}
