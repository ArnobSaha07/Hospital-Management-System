<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\AppBaseController;
use App\Models\PatientAdmission;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PatientAdmissionController extends AppBaseController
{
    /**
     * Display a listing of the PatientAdmission.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = PatientAdmission::STATUS_ARR;

        return view('employees.patient_admissions.index', $data);
    }

    /**
     * Display the specified PatientAdmission.
     *
     * @return Factory|View
     */
    public function show(PatientAdmission $patientAdmission)
    {
        if (! canAccessRecord(PatientAdmission::class, $patientAdmission->id)) {
            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            if (getLoggedInUser()->owner_id != $patientAdmission->patient_id) {
                return Redirect::back();
            }
        }

        return view('employees.patient_admissions.show')->with('patientAdmission', $patientAdmission);
    }
}
