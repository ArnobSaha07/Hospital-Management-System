<?php

namespace App\Http\Controllers;

use App\Exports\PatientCaseExport;
use App\Http\Requests\CreatePatientCaseRequest;
use App\Http\Requests\UpdatePatientCaseRequest;
use App\Models\BedAssign;
use App\Models\BirthReport;
use App\Models\DeathReport;
use App\Models\IpdPatientDepartment;
use App\Models\OperationReport;
use App\Models\Patient;
use App\Models\PatientCase;
use App\Repositories\PatientCaseRepository;
use Carbon\Carbon;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatientCaseController extends AppBaseController
{
    /** @var PatientCaseRepository */
    private $patientCaseRepository;

    public function __construct(PatientCaseRepository $patientCaseManagerRepo)
    {
        $this->patientCaseRepository = $patientCaseManagerRepo;
    }

    /**
     * Display a listing of the PatientCase.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = PatientCase::STATUS_ARR;

        return view('patient_cases.index', $data);
    }

    /**
     * Show the form for creating a new PatientCase.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $patients = $this->patientCaseRepository->getPatients();
        $doctors = $this->patientCaseRepository->getDoctors();

        return view('patient_cases.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created PatientCase in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreatePatientCaseRequest $request): RedirectResponse
    {
        $input = $request->all();
        $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
        $birthDate = $patientId->patientUser->dob;
        $caseDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $caseDate < $birthDate) {
            Flash::error(__('messages.flash.case_date_smaller'));

            return redirect()->back()->withInput($input);
        }

        $input['fee'] = removeCommaFromNumbers($input['fee']);
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['phone'] = preparePhoneNumber($input, 'phone');

        $this->patientCaseRepository->store($input);
        $this->patientCaseRepository->createNotification($input);

        Flash::success(__('messages.flash.case_saved'));

        return redirect(route('patient-cases.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(PatientCase $patientCase)
    {
        if (! canAccessRecord(PatientCase::class, $patientCase->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        //        if (getLoggedInUser()->hasRole('Doctor')) {
        //            $patientCaseHasDoctor = PatientCase::whereId($id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
        //            if(!$patientCaseHasDoctor){
        //                return Redirect::back();
        //            }
        //        }

        return view('patient_cases.show')->with('patientCase', $patientCase);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(PatientCase $patientCase)
    {
        if (! canAccessRecord(PatientCase::class, $patientCase->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $patients = $this->patientCaseRepository->getPatients();
        $doctors = $this->patientCaseRepository->getDoctors();

        return view('patient_cases.edit', compact('patientCase', 'patients', 'doctors'));
    }

    /**
     * Update the specified PatientCase in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(PatientCase $patientCase, UpdatePatientCaseRequest $request): RedirectResponse
    {
        $input = $request->all();
        $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
        $birthDate = $patientId->patientUser->dob;
        $caseDate = Carbon::parse($input['date'])->toDateString();
        if (! empty($birthDate) && $caseDate < $birthDate) {
            Flash::error(__('messages.flash.case_date_smaller'));

            return redirect()->back()->withInput($input);
        }
        $input['fee'] = removeCommaFromNumbers($input['fee']);
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['phone'] = preparePhoneNumber($input, 'phone');

        $patientCase = $this->patientCaseRepository->update($input, $patientCase->id);

        Flash::success(__('messages.flash.case_updated'));

        return redirect(route('patient-cases.index'));
    }

    /**
     * Remove the specified PatientCase from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(PatientCase $patientCase): JsonResponse
    {
        if (! canAccessRecord(PatientCase::class, $patientCase->id)) {
            return $this->sendError(__('messages.flash.patient_case_not_found'));
        }

        $patientCaseModel = [
            BedAssign::class, BirthReport::class, DeathReport::class, OperationReport::class,
            IpdPatientDepartment::class,
        ];
        $result = canDelete($patientCaseModel, 'case_id', $patientCase->case_id);
        if ($result) {
            return $this->sendError(__('messages.flash.case_cant_deleted'));
        }
        $this->patientCaseRepository->delete($patientCase->id);

        return $this->sendSuccess(__('messages.flash.case_deleted'));
    }

    public function activeDeActiveStatus(int $id): JsonResponse
    {
        $patientCase = PatientCase::findOrFail($id);
        $patientCase->status = ! $patientCase->status;
        $patientCase->update(['status' => $patientCase->status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function patientCaseExport(): BinaryFileResponse
    {
        $response = Excel::download(new PatientCaseExport, 'patient-cases-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    public function showModal(PatientCase $patientCase): JsonResponse
    {
        $patientCase->load(['patient.patientUser', 'doctor.doctorUser']);

        return $this->sendResponse($patientCase, __('messages.flash.case_retrieved'));
    }
}
