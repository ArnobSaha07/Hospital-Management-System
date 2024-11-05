<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDoctorDepartmentRequest;
use App\Http\Requests\UpdateDoctorDepartmentRequest;
use App\Models\Doctor;
use App\Models\DoctorDepartment;
use App\Repositories\DoctorDepartmentRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DoctorDepartmentController extends AppBaseController
{
    /** @var DoctorDepartmentRepository */
    private $doctorDepartmentRepository;

    public function __construct(DoctorDepartmentRepository $doctorDepartmentRepo)
    {
        $this->doctorDepartmentRepository = $doctorDepartmentRepo;
    }

    /**
     * Display a listing of the DoctorDepartment.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('doctor_departments.index');
    }

    /**
     * Store a newly created DoctorDepartment in storage.
     */
    public function store(CreateDoctorDepartmentRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->doctorDepartmentRepository->create($input);

        return $this->sendSuccess(__('messages.flash.doctor_department_saved'));
    }

    /**
     * @param  DoctorDepartment  $doctorDepartment
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function show($id)
    {
        if (! canAccessRecord(DoctorDepartment::class, $id)) {
            return Redirect::back();
        }

        $doctorDepartment = DoctorDepartment::find($id);
        if (empty($doctorDepartment)) {
            Flash::error(__('messages.flash.doctor_department_not_found'));

            return redirect(route('doctor-departments.index'));
        }
        $doctors = $doctorDepartment->doctors;

        $doctorDepartment = $this->doctorDepartmentRepository->find($doctorDepartment->id);

        return view('doctor_departments.show', compact('doctors', 'doctorDepartment'));
    }

    /**
     * Show the form for editing the specified DoctorDepartment.
     */
    public function edit(DoctorDepartment $doctorDepartment): JsonResponse
    {
        if (! canAccessRecord(DoctorDepartment::class, $doctorDepartment->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($doctorDepartment, __('messages.flash.doctor_department_retrieved'));
    }

    /**
     * Update the specified DoctorDepartment in storage.
     */
    public function update(DoctorDepartment $doctorDepartment, UpdateDoctorDepartmentRequest $request): JsonResponse
    {
        $input = $request->all();
        $doctorDepartment->update($input);

        return $this->sendSuccess(__('messages.flash.doctor_department_updated'));
    }

    /**
     * Remove the specified DoctorDepartment from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(DoctorDepartment $doctorDepartment): JsonResponse
    {
        if (! canAccessRecord(DoctorDepartment::class, $doctorDepartment->id)) {
            return $this->sendError(__('messages.flash.doctor_department_not_found'));
        }

        $doctorDepartmentModels = [
            Doctor::class,
        ];
        $result = canDelete($doctorDepartmentModels, 'doctor_department_id', $doctorDepartment->id);
        if ($result) {
            return $this->sendError(__('messages.flash.doctor_department_cant_deleted'));
        }
        $doctorDepartment->delete();

        return $this->sendSuccess(__('messages.flash.doctor_department_deleted'));
    }
}
