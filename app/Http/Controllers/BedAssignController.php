<?php

namespace App\Http\Controllers;

use App\Exports\BedAssignExport;
use App\Http\Requests\CreateBedAssignRequest;
use App\Http\Requests\UpdateBedAssignRequest;
use App\Models\BedAssign;
use App\Models\BedType;
use App\Models\IpdPatientDepartment;
use App\Models\PatientAdmission;
use App\Models\PatientCase;
use App\Repositories\BedAssignRepository;
use Carbon\Carbon;
use Exception;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BedAssignController extends AppBaseController
{
    /** @var BedAssignRepository */
    private $bedAssignRepository;

    public function __construct(BedAssignRepository $bedAssignRepo)
    {
        $this->bedAssignRepository = $bedAssignRepo;
    }

    /**
     * Display a listing of the BedAssign.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $data['statusArr'] = BedAssign::STATUS_ARR;

        return view('bed_assigns.index', $data);
    }

    /**
     * Show the form for creating a new BedAssign.
     *
     * @return Factory|View
     */
    public function create(Request $request): View
    {
        $bedId = $request->get('bed_id', null);
        $beds = $this->bedAssignRepository->getBeds();
        $cases = $this->bedAssignRepository->getCases();

        return view('bed_assigns.create', compact('cases', 'beds', 'bedId'));
    }

    /**
     * Store a newly created BedAssign in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateBedAssignRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $patientId = PatientCase::with('patient.user')->whereCaseId($input['case_id'])->first();
        $birthDate = $patientId->patient->user->dob;
        $assign_date = Carbon::parse($input['assign_date'])->toDateString();

        if (! empty($birthDate) && $assign_date < $birthDate) {
            Flash::error(__('messages.flash.assign_date_smaller'));

            return redirect()->back()->withInput($input);
        }
        $this->bedAssignRepository->store($input);
        $this->bedAssignRepository->createNotification($input);
        Flash::success(__('messages.flash.bed_assign_save'));

        return redirect(route('bed-assigns.index'));
    }

    /**
     * Display the specified BedAssign.
     *
     * @return Factory|View
     */
    public function show(BedAssign $bedAssign)
    {
        if (! canAccessRecord(BedAssign::class, $bedAssign->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('bed_assigns.show')->with('bedAssign', $bedAssign);
    }

    /**
     * Show the form for editing the specified BedAssign.
     *
     * @return Factory|View
     */
    public function edit(BedAssign $bedAssign)
    {
        if (! canAccessRecord(BedAssign::class, $bedAssign->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $beds = $this->bedAssignRepository->getPatientBeds($bedAssign);
        $cases = $this->bedAssignRepository->getPatientCases($bedAssign);

        return view('bed_assigns.edit', compact('cases', 'beds', 'bedAssign'));
    }

    /**
     * Update the specified BedAssign in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(BedAssign $bedAssign, UpdateBedAssignRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;
        $patientId = IpdPatientDepartment::with('patient.patientUser')->where('id', $input['ipd_patient_department_id'])->latest()->first();
        $birthDate = $patientId->patient->patientUser->dob;
        $input['patient_id'] = $patientId->patient_id;
        $assign_date = Carbon::parse($input['assign_date'])->toDateString();
        if (! empty($birthDate) && $assign_date < $birthDate) {
            Flash::error(__('messages.flash.assign_date_smaller'));

            return redirect()->back()->withInput($input);
        }
        $bedAssign = $this->bedAssignRepository->update($input, $bedAssign);
        Flash::success(__('messages.flash.bed_assign_update'));

        return redirect(route('bed-assigns.index'));
    }

    /**
     * Remove the specified BedAssign from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(BedAssign $bedAssign): JsonResponse
    {
        if (! canAccessRecord(BedAssign::class, $bedAssign->id)) {
            return $this->sendError(__('messages.flash.bed_assign_not_found'));
        }

        $bedAssign->bed->update(['is_available' => 1]);
        $this->bedAssignRepository->delete($bedAssign->id);

        return $this->sendSuccess(__('messages.flash.bed_assign_delete'));
    }

    public function activeDeactiveStatus(int $id): JsonResponse
    {
        $bedAssign = BedAssign::with('bed')->find($id);
        $status = ! $bedAssign->status;
        $bedAssign->update(['status' => $status]);
        $bedAssign->bed->update(['is_available' => 1]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    /**
     * @return Application|Factory|View
     */
    public function bedStatus(): View
    {
        $bedTypes = BedType::with(['beds.bedAssigns.patient.user'])->get();
        $patientAdmissions = PatientAdmission::whereHas('bed')->with('bed.bedType')->get();

        return view('bed_status.index', compact('bedTypes', 'patientAdmissions'));
    }

    public function bedAssignExport(): BinaryFileResponse
    {
        $response = Excel::download(new BedAssignExport, 'bed-assigns-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    public function getIpdPatientsList(Request $request): JsonResponse
    {
        $ipdPatients = $this->bedAssignRepository->getIpdPatients($request->get('id'));

        return $this->sendResponse($ipdPatients, __('messages.flash.retrieve'));
    }
}
