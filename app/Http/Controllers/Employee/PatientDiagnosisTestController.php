<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\AppBaseController;
use App\Models\PatientDiagnosisTest;
use App\Repositories\PatientDiagnosisTestRepository;
use \PDF;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PatientDiagnosisTestController extends AppBaseController
{
    /**
     * @var PatientDiagnosisTestRepository
     */
    private $patientDiagnosisTestRepository;

    public function __construct(
        PatientDiagnosisTestRepository $patientDiagnosisTestRepository
    ) {
        $this->patientDiagnosisTestRepository = $patientDiagnosisTestRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('employees.patient_diagnosis_test.index');
    }

    /**
     * Display the specified resource.
     *
     * @return Application|Factory|View
     */
    public function show(PatientDiagnosisTest $patientDiagnosisTest)
    {
        if (! canAccessRecord(PatientDiagnosisTest::class, $patientDiagnosisTest->id)) {
            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            if (getLoggedInUser()->owner_id != $patientDiagnosisTest->patient_id) {
                return Redirect::back();
            }
        }

        $patientDiagnosisTests = $this->patientDiagnosisTestRepository->getPatientDiagnosisTestProperty($patientDiagnosisTest->id);

        return view('employees.patient_diagnosis_test.show', compact('patientDiagnosisTests', 'patientDiagnosisTest'));
    }

    public function convertToPdf(PatientDiagnosisTest $patientDiagnosisTest)
    {
        if(app()->getLocale() == "zh"){
            app()->setLocale("en");
        }
        $data = $this->patientDiagnosisTestRepository->getSettingList();
        $data['patientDiagnosisTest'] = $patientDiagnosisTest;
        $data['patientDiagnosisTests'] = $this->patientDiagnosisTestRepository->getPatientDiagnosisTestProperty($patientDiagnosisTest->id);

        $pdf = PDF::loadView('employees.patient_diagnosis_test.diagnosis_test_pdf', $data);

        return $pdf->stream($patientDiagnosisTest->patient->user->full_name.'-'.$patientDiagnosisTest->report_number);
    }
}
