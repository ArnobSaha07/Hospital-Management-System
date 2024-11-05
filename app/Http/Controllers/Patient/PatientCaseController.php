<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\PatientCase;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PatientCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('patients_cases_list.index');
    }

    /**
     * Display the specified resource.
     *
     * @return Factory|View
     */
    public function show(int $id)
    {
        if (! canAccessRecord(PatientCase::class, $id)) {
            return Redirect::back();
        }

        $patientCase = PatientCase::findOrFail($id);

        if (getLoggedInUser()->hasRole('Patient')) {
            if (getLoggedInUser()->owner_id != $patientCase->patient_id) {
                return Redirect::back();
            }
        }

        return view('patients_cases_list.show')->with('patientCase', $patientCase);
    }
}
