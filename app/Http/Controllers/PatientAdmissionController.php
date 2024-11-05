<?php

namespace App\Http\Controllers;

use App\Exports\PatientAdmissionExport;
use App\Http\Requests\CreatePatientAdmissionRequest;
use App\Http\Requests\UpdatePatientAdmissionRequest;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\PatientAdmission;
use App\Repositories\PatientAdmissionRepository;
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

class PatientAdmissionController extends AppBaseController
{
    /** @var PatientAdmissionRepository */
    private $patientAdmissionRepository;

    public function __construct(PatientAdmissionRepository $patientAdmissionRepo)
    {
        $this->patientAdmissionRepository = $patientAdmissionRepo;
    }

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

        return view('patient_admissions.index', $data);
    }

    /**
     * Show the form for creating a new PatientAdmission.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $data = $this->patientAdmissionRepository->getSyncList();

        return view('patient_admissions.create', compact('data'));
    }

    /**
     * Store a newly created PatientAdmission in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreatePatientAdmissionRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
        $birthDate = $patientId->user->dob;
        $admissionDate = Carbon::parse($input['admission_date'])->toDateString();
        if (! empty($birthDate) && $admissionDate < $birthDate) {
            Flash::error(__('messages.flash.admission_date_smaller'));

            return redirect()->back()->withInput($input);
        }

        $this->patientAdmissionRepository->store($input);

        Flash::success(__('messages.flash.patient_admission_saved'));

        return redirect(route('patient-admissions.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(PatientAdmission $patientAdmission)
    {
        if (! canAccessRecord(PatientAdmission::class, $patientAdmission->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('patient_admissions.show')->with('patientAdmission', $patientAdmission);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(PatientAdmission $patientAdmission)
    {
        if (! canAccessRecord(PatientAdmission::class, $patientAdmission->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAdmissionHasDoctor = PatientAdmission::whereId($patientAdmission->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientAdmissionHasDoctor) {
                return Redirect::back();
            }
        }

        $data = $this->patientAdmissionRepository->getSyncList($patientAdmission);
        $data['patientAdmissionDate'] = PatientAdmission::whereId($patientAdmission->id)->with('patient',
            function ($q) {
                $q->with('user');
            })->first();

        return view('patient_admissions.edit', compact('data', 'patientAdmission'));
    }

    /**
     * Update the specified PatientAdmission in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(PatientAdmission $patientAdmission, UpdatePatientAdmissionRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $patientId = Patient::with('patientUser')->whereId($patientAdmission->patient_id)->first();
        $birthDate = $patientId->patientUser->dob;
        $admissionDate = Carbon::parse($input['admission_date'])->toDateString();
        if (! empty($birthDate) && $admissionDate < $birthDate) {
            Flash::error(__('messages.flash.admission_date_smaller'));

            return redirect()->back()->withInput($input);
        }
        $this->patientAdmissionRepository->update($input, $patientAdmission);

        Flash::success(__('messages.flash.patient_admission_updated'));

        return redirect(route('patient-admissions.index'));
    }

    /**
     * Remove the specified PatientAdmission from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(PatientAdmission $patientAdmission): JsonResponse
    {
        if (! canAccessRecord(PatientAdmission::class, $patientAdmission->id)) {
            return $this->sendError(__('messages.flash.patient_admission_not_found'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAdmissionHasDoctor = PatientAdmission::whereId($patientAdmission->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientAdmissionHasDoctor) {
                return $this->sendError(__('messages.flash.patient_admission_not_found'));
            }
        }

        $patientAdmissionModel = [
            Bill::class,
        ];
        $result = canDelete($patientAdmissionModel, 'patient_admission_id', $patientAdmission->patient_admission_id);
        if ($result) {
            return $this->sendError(__('messages.flash.patient_admission_cant_deleted'));
        }

        if (! empty($patientAdmission->bed_id)) {
            $this->patientAdmissionRepository->setBedAvailable($patientAdmission->bed_id);
        }
        $this->patientAdmissionRepository->delete($patientAdmission->id);

        return $this->sendSuccess(__('messages.flash.patient_admission_deleted'));
    }

    public function activeDeactiveStatus(int $id): JsonResponse
    {
        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAdmissionHasDoctor = PatientAdmission::whereId($id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientAdmissionHasDoctor) {
                return $this->sendError(__('messages.flash.patient_admission_not_found'));
            }
        }

        $patientAdmission = PatientAdmission::findOrFail($id);
        $status = ! $patientAdmission->status;
        $patientAdmission->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function patientAdmissionExport(): BinaryFileResponse
    {
        $response = Excel::download(new PatientAdmissionExport, 'patient-admissions-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    public function showModal(PatientAdmission $patientAdmission): JsonResponse
    {
        if (! canAccessRecord(PatientAdmission::class, $patientAdmission->id)) {
            return $this->sendError(__('messages.flash.patient_admission_not_found'));
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAdmissionHasDoctor = PatientAdmission::whereId($patientAdmission->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientAdmissionHasDoctor) {
                return $this->sendError(__('messages.flash.patient_admission_not_found'));
            }
        }

        $patientAdmission->load(['patient.patientUser', 'doctor.doctorUser', 'package', 'insurance', 'bed']);
        $patientAdmission['admission_date'] = date('jS M,Y g:i A', strtotime($patientAdmission->admission_date));

        return $this->sendResponse($patientAdmission, __('messages.flash.patient_admission_retrieved'));
    }
}
