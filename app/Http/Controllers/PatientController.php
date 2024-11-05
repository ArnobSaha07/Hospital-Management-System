<?php

namespace App\Http\Controllers;

use App;
use App\Exports\PatientExport;
use App\Http\Requests\CreatePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\AdvancedPayment;
use App\Models\Appointment;
use App\Models\BedAssign;
use App\Models\Bill;
use App\Models\BirthReport;
use App\Models\CustomField;
use App\Models\DeathReport;
use App\Models\InvestigationReport;
use App\Models\Invoice;
use App\Models\IpdPatientDepartment;
use App\Models\OperationReport;
use App\Models\Patient;
use App\Models\PatientAdmission;
use App\Models\PatientCase;
use App\Models\Prescription;
use App\Models\Vaccination;
use App\Repositories\AdvancedPaymentRepository;
use App\Repositories\PatientRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatientController extends AppBaseController
{
    /** @var PatientRepository */
    private $patientRepository;

    public function __construct(PatientRepository $patientRepo)
    {
        $this->patientRepository = $patientRepo;
    }

    /**
     * Display a listing of the Patient.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = Patient::STATUS_ARR;

        return view('patients.index', $data);
    }

    /**
     * Show the form for creating a new Patient.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $bloodGroup = getBloodGroups();
        $customField = CustomField::where('module_name', CustomField::Patient)->get()->toArray();

        return view('patients.create', compact('bloodGroup','customField'));
    }

    /**
     * Store a newly created Patient in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreatePatientRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['region_code'] = regionCode($input['prefix_code']);

        $this->patientRepository->store($input);
        $this->patientRepository->createNotification($input);
        Flash::success(__('messages.flash.Patient_saved'));

        return redirect(route('patients.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Patient $patient)
    {
        if (! canAccessRecord(Patient::class, $patient->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAppointmentHasDoctor = Appointment::whereDoctorId(getLoggedInUser()->owner_id)->where('patient_id',
                $patient->id)->exists();
            $birthReportHasDoctor = BirthReport::whereDoctorId(getLoggedInUser()->owner_id)->where('patient_id',
                $patient->id)->exists();
            $deathReportHasDoctor = DeathReport::whereDoctorId(getLoggedInUser()->owner_id)->where('patient_id',
                $patient->id)->exists();
            $InvestigationReportHasDoctor = InvestigationReport::whereDoctorId(getLoggedInUser()->owner_id)->where('patient_id',
                $patient->id)->exists();
            $operationReportHasDoctor = OperationReport::whereDoctorId(getLoggedInUser()->owner_id)->where('patient_id',
                $patient->id)->exists();
            if (! ($patientAppointmentHasDoctor) && ! ($birthReportHasDoctor) && ! ($deathReportHasDoctor) && ! ($InvestigationReportHasDoctor) && ! ($operationReportHasDoctor)) {
                return Redirect::back();
            }
        }

        if (getLoggedInUser()->hasRole('Patient')) {
            if (getLoggedInUser()->owner_id != $patient->id) {
                return Redirect::back();
            }
        }

        $data = $this->patientRepository->getPatientAssociatedData($patient->id);
        $advancedPaymentRepo = App::make(AdvancedPaymentRepository::class);
        $patients = $advancedPaymentRepo->getPatients();
        $user = Auth::user();
        if ($user->hasRole('Doctor')) {
            $vaccinationPatients = getPatientsList($user->owner_id);
        } else {
            $vaccinationPatients = Patient::getActivePatientNames();
        }
        $vaccinations = Vaccination::toBase()->pluck('name', 'id')->toArray();
        natcasesort($vaccinations);

        return view('patients.show', compact('data', 'patients', 'vaccinations', 'vaccinationPatients'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Patient $patient)
    {
        if (! canAccessRecord(Patient::class, $patient->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $user = $patient->user;
        $bloodGroup = getBloodGroups();
        $customField = CustomField::where('module_name', CustomField::Patient)->get()->toArray();

        return view('patients.edit', compact('patient', 'user', 'bloodGroup','customField'));
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function update(Patient $patient, UpdatePatientRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $input['region_code'] = regionCode($input['prefix_code']);

        $this->patientRepository->update($input, $patient);

        Flash::success(__('messages.flash.Patient_updated'));

        return redirect(route('patients.index'));
    }

    /**
     * Remove the specified Patient from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Patient $patient): JsonResponse
    {
        if (! canAccessRecord(Patient::class, $patient->id)) {
            return $this->sendError(__('messages.flash.patient_not_found'));
        }

        $patientModels = [
            BirthReport::class, DeathReport::class, InvestigationReport::class, OperationReport::class,
            Appointment::class, BedAssign::class, PatientAdmission::class, PatientCase::class, Bill::class,
            Invoice::class, AdvancedPayment::class, Prescription::class, IpdPatientDepartment::class,
        ];
        $result = canDelete($patientModels, 'patient_id', $patient->id);
        if ($result) {
            return $this->sendError(__('messages.flash.Patient_cant_deleted'));
        }
        $patient->user()->delete();
        $patient->address()->delete();
        $patient->delete();

        return $this->sendSuccess(__('messages.flash.Patient_deleted'));
    }

    public function activeDeactiveStatus(int $id): JsonResponse
    {
        $patient = Patient::findOrFail($id);
        $status = ! $patient->patientUser->status;
        $patient->patientUser()->update(['status' => $status]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function patientExport(): BinaryFileResponse
    {
        $response = Excel::download(new PatientExport, 'patients-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    /**
     * @return Patient|Builder|Model|object|null
     */
    public function getBirthDate($id)
    {
        return Patient::whereId($id)->with('user')->first();
    }
}
