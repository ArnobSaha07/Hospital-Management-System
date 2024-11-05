<?php

namespace App\Http\Controllers;

use App\Exports\VaccinatedPatientExport;
use App\Http\Requests\CreateVaccinatedPatientRequest;
use App\Http\Requests\UpdateVaccinatedPatientRequest;
use App\Models\VaccinatedPatients;
use App\Repositories\VaccinatedPatientRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VaccinatedPatientController extends AppBaseController
{
    /**
     * @var VaccinatedPatientRepository
     */
    private $vaccinatedPatientRepository;

    public function __construct(VaccinatedPatientRepository $vaccinatedPatientRepository)
    {
        $this->middleware('check_menu_access');
        $this->vaccinatedPatientRepository = $vaccinatedPatientRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|Response|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data = $this->vaccinatedPatientRepository->getVaccinatedPatientData();

        return view('vaccinated_patients.index')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateVaccinatedPatientRequest $request): JsonResponse
    {
        try {
            $input = $request->all();
            $checkValidation = checkVaccinatePatientValidation($input, null, null);
            if ($checkValidation) {
                return $this->sendError(__('messages.flash.the_patient'));
            }
            $this->vaccinatedPatientRepository->create($input);

            return $this->sendSuccess(__('messages.flash.vaccinated_patients_saved'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VaccinatedPatients $vaccinatedPatient): JsonResponse
    {
        if (! canAccessRecord(VaccinatedPatients::class, $vaccinatedPatient->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($vaccinatedPatient, __('messages.flash.vaccinated_patients_retrieved'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVaccinatedPatientRequest $request, VaccinatedPatients $vaccinatedPatient): JsonResponse
    {
        try {
            $input = $request->all();
            if ($input['patient_id'] == $vaccinatedPatient->patient_id &&
                $input['vaccination_id'] == $vaccinatedPatient->vaccination_id &&
                $input['dose_number'] == $vaccinatedPatient->dose_number) {
            } else {
                $checkValidation = checkVaccinatePatientValidation($input, $vaccinatedPatient, $isCreate = true);
                if ($checkValidation) {
                    return $this->sendError(__('messages.flash.the_patient'));
                }
            }
            $this->vaccinatedPatientRepository->update($input, $vaccinatedPatient->id);

            return $this->sendSuccess(__('messages.flash.vaccinated_patients_updated'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VaccinatedPatients $vaccinatedPatient): JsonResponse
    {
        if (! canAccessRecord(VaccinatedPatients::class, $vaccinatedPatient->id)) {
            return $this->sendError(__('messages.flash.vaccinated_patient_not_found'));
        }

        try {
            $vaccinatedPatient->delete();

            return $this->sendSuccess(__('messages.flash.vaccinated_patients_deleted'));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function vaccinatedPatientExport(): BinaryFileResponse
    {
        $response = Excel::download(new VaccinatedPatientExport, 'vaccinated_patient-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }
}
