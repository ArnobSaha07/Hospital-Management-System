<?php

namespace App\Http\Controllers\API\Doctor;

use App\Http\Controllers\AppBaseController;
use App\Models\Doctor;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorAPIController extends AppBaseController
{
    /** @var ScheduleRepository */
    private $scheduleRepository;

    public function __construct(ScheduleRepository $scheduleRepo)
    {
        $this->scheduleRepository = $scheduleRepo;
    }

    public function index(): JsonResponse
    {
        $doctors = Doctor::query()->where('tenant_id',getLoggedInUser()->tenant_id)->with('doctorUser')->orderBy('id', 'desc')->get();
        $data = [];
        foreach ($doctors as $doctor) {
            $data[] = $doctor->prepareDoctor();
        }

        return $this->sendResponse($data, 'Doctors Retrieved Successfully');
    }

    public function users(): JsonResponse
    {
        $users = User::where('owner_type', \App\Models\Doctor::class)->orWhere('owner_type', '=', null)->get();

        $data = [];
        foreach ($users as $user) {
            $data[] = $user->prepareUserForDoctor();
        }

        return $this->sendResponse($data, 'Users Retrieved Successfully');
    }

    public function show($id): JsonResponse
    {
        $doctor = Doctor::where('tenant_id',getLoggedInUser()->tenant_id)->with('doctorUser')->find($id);
        /** @var Doctor $doctor */
        if($doctor){
            return $this->sendResponse($doctor->prepareDoctorDetail(), 'Doctor Retrieved Successfully');
        }else {
            return $this->sendError('Doctor not found');
        }
    }

    /**
     * @param $id
     */
    public function doctorScheduleList(): JsonResponse
    {
        $id = getLoggedInUser()->owner_id;
        $schedules = Schedule::with('scheduleDays')->where('doctor_id', $id)->first();
        if (empty($schedules)) {
            return $this->sendError(__('messages.common.doctor_scheduled').' '.__('messages.common.not_found'));
        }
        $scheduleDays = ScheduleDay::with('schedule')->where('doctor_id', $id)->where('schedule_id', $schedules->id)->get();
        $per_patient_time = \Carbon\Carbon::createFromFormat('H:i:s', $schedules->per_patient_time)->format('H:i:s');

        $data = [];
        foreach ($scheduleDays as $key => $schedule) {
            $data[] = $schedule->prepareScheduleDay();
        }

        $response = [
            'id' => $schedules->id,
            'per_patient_time' => $per_patient_time,
            'schedule' => $data,
        ];

        return $this->sendResponse($response, 'Doctor Scheduled Retrieved Successfully');
    }

    public function doctorScheduleUpdate($id, Request $request): JsonResponse
    {
        $input = $request->all();

        $input['doctor_id'] = getLoggedInUser()->owner_id;

        $schedule = $this->scheduleRepository->update($input, $id);
        if ($schedule) {
            return $this->sendSuccess(__('messages.common.doctor_scheduled').' '.__('messages.common.updated_successfully'));
        } else {
            return $this->sendError(__('messages.common.doctor_scheduled').' '.__('messages.common.not_updated'));
        }
    }

    public function filter(Request $request): JsonResponse
    {
        $status = $request->get('status');
        $doctorsQuery = Doctor::where('tenant_id',getLoggedInUser()->tenant_id)->with('doctorUser');
        $data = [];

        if($status == 'all'){
            $doctors = $doctorsQuery->orderBy('id', 'desc')->get();
            foreach ($doctors as $doctor) {
                $data[] = $doctor->prepareDoctor();
            }

            return $this->sendResponse($data, 'Doctors Retrieved Successfully');
        }elseif($status == 'deactive'){
            $doctors = $doctorsQuery->whereHas('user', function ($q) {
                $q->where('status', 0);
            })->orderBy('id', 'desc')->get();
            foreach ($doctors as $doctor) {
                $data[] = $doctor->prepareDoctor();
            }

            return $this->sendResponse($data, 'Doctors Retrieved Successfully');
        }elseif($status == 'active'){
            $doctors = $doctorsQuery->whereHas('user', function ($q) {
                $q->where('status', 1);
            })->orderBy('id', 'desc')->get();
            foreach ($doctors as $doctor) {
                $data[] = $doctor->prepareDoctor();
            }

            return $this->sendResponse($data, 'Doctors Retrieved Successfully');
        }else{
            return $this->sendError('Doctor not found');
        }

    }
}
