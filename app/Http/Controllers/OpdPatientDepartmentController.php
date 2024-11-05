<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpdPatientDepartmentRequest;
use App\Http\Requests\UpdateOpdPatientDepartmentRequest;
use App\Models\CustomField;
use App\Models\DoctorOPDCharge;
use App\Models\OpdPatientDepartment;
use App\Repositories\OpdPatientDepartmentRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Class OpdPatientDepartmentController
 */
class OpdPatientDepartmentController extends AppBaseController
{
    /** @var OpdPatientDepartmentRepository */
    private $opdPatientDepartmentRepository;

    public function __construct(OpdPatientDepartmentRepository $opdPatientDepartmentRepo)
    {
        $this->opdPatientDepartmentRepository = $opdPatientDepartmentRepo;
    }

    /**
     * Display a listing of the OpdPatientDepartment.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('opd_patient_departments.index');
    }

    /**
     * Show the form for creating a new OpdPatientDepartment.
     *
     * @return Factory|View
     */
    public function create(Request $request): View
    {
        $data = $this->opdPatientDepartmentRepository->getAssociatedData();
        $data['revisit'] = ($request->get('revisit')) ? $request->get('revisit') : 0;
        $customField = CustomField::where('module_name', CustomField::OpdPatient)->get()->toArray();
        if ($data['revisit']) {
            $id = $data['revisit'];
            $data['last_visit'] = OpdPatientDepartment::findOrFail($id);
        }

        return view('opd_patient_departments.create', compact('data','customField'));
    }

    /**
     * Store a newly created OpdPatientDepartment in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreateOpdPatientDepartmentRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['standard_charge'] = removeCommaFromNumbers($input['standard_charge']);
        $this->opdPatientDepartmentRepository->store($input);
        $this->opdPatientDepartmentRepository->createNotification($input);
        Flash::success(__('messages.flash.OPD_Patient_saved'));

        return redirect(route('opd.patient.index'));
    }

    /**
     * Display the specified OpdPatientDepartment.
     *
     * @return Factory|View
     */
    public function show(OpdPatientDepartment $opdPatientDepartment)
    {
        if (! canAccessRecord(OpdPatientDepartment::class, $opdPatientDepartment->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $doctors = $this->opdPatientDepartmentRepository->getDoctorsData();
        $medicineCategories = $this->opdPatientDepartmentRepository->getMedicinesCategoriesData();
        $medicineCategoriesList = $this->opdPatientDepartmentRepository->getMedicineCategoriesList();
        $doseDurationList = $this->opdPatientDepartmentRepository->getDoseDurationList();
        $doseIntervalList = $this->opdPatientDepartmentRepository->getDoseIntervalList();
        $mealList = $this->opdPatientDepartmentRepository->getMealList();

        //        $doctorsList = $this->opdPatientDepartmentRepository->getDoctorsList();

        return view('opd_patient_departments.show',
            compact('opdPatientDepartment', 'doctors', 'medicineCategories','medicineCategoriesList','doseDurationList','doseIntervalList','mealList'));
    }

    /**
     * Show the form for editing the specified Ipd Diagnosis.
     *
     * @return Factory|View
     */
    public function edit(OpdPatientDepartment $opdPatientDepartment)
    {
        if (! canAccessRecord(OpdPatientDepartment::class, $opdPatientDepartment->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $data = $this->opdPatientDepartmentRepository->getAssociatedData();
        $customField = CustomField::where('module_name', CustomField::OpdPatient)->get()->toArray();

        return view('opd_patient_departments.edit', compact('data', 'opdPatientDepartment','customField'));
    }

    /**
     * Update the specified Ipd Diagnosis in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(OpdPatientDepartment $opdPatientDepartment, UpdateOpdPatientDepartmentRequest $request): RedirectResponse
    {
        $input = $request->all();
        $this->opdPatientDepartmentRepository->updateOpdPatientDepartment($input, $opdPatientDepartment);
        Flash::success(__('messages.flash.OPD_Patient_updated'));

        return redirect(route('opd.patient.index'));
    }

    /**
     * Remove the specified OpdPatientDepartment from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(OpdPatientDepartment $opdPatientDepartment): JsonResponse
    {
        if (! canAccessRecord(OpdPatientDepartment::class, $opdPatientDepartment->id)) {
            return $this->sendError(__('messages.flash.opd_patient_not_found'));
        }

        $opdPatientDepartment->delete();

        return $this->sendSuccess(__('messages.flash.OPD_Patient_deleted'));
    }

    public function getPatientCasesList(Request $request): JsonResponse
    {
        $patientCases = $this->opdPatientDepartmentRepository->getPatientCases($request->get('id'));

        return $this->sendResponse($patientCases, __('messages.flash.retrieve'));
    }

    public function getDoctorOPDCharge(Request $request): JsonResponse
    {
        $doctorOPDCharge = DoctorOPDCharge::whereDoctorId($request->get('id'))->get();

        return $this->sendResponse($doctorOPDCharge, __('messages.flash.OPD_charge_retrieved'));
    }
}
