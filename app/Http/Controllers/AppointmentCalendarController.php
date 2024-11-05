<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Repositories\AppointmentCalendarRepository;
use App\Repositories\AppointmentRepository;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AppointmentCalendarController extends AppBaseController
{
    /** @var AppointmentCalendarRepository */
    private $appointmentCalendarRepository;

    /** @var AppointmentRepository */
    private $appointmentRepository;

    public function __construct(
        AppointmentCalendarRepository $appointmentCalendarRepo,
        AppointmentRepository $appointmentRepository
    ) {
        $this->appointmentCalendarRepository = $appointmentCalendarRepo;
        $this->appointmentRepository = $appointmentRepository;
    }

    /**
     * @return Factory|View
     */
    public function index(): View
    {
        $patients = $this->appointmentRepository->getPatients();
        $doctorArr = $this->appointmentRepository->getDoctorLists();
        $statusArr = Appointment::STATUS_PENDING;
        $payemnt_mode = Appointment::EDIT_PAYMENT_TYPES;

        return view('appointment_calendars.index', compact('patients', 'doctorArr', 'statusArr','payemnt_mode'));
    }

    public function calendarList(): JsonResponse
    {
        $appointments = $this->appointmentCalendarRepository->getAppointments();

        return $this->sendResponse($appointments, __('messages.flash.appointment_list_retrieved'));
    }

    public function getAppointmentDetails(Appointment $appointment): JsonResponse
    {
        $appointmentDetails['patient'] = $appointment->patient->user->full_name;
        $appointmentDetails['department'] = $appointment->doctor->department->title;
        $appointmentDetails['doctor'] = $appointment->doctor->user->full_name;
        $appointmentDetails['opdDate'] = Carbon::parse($appointment->opd_date)->format('jS M, Y g:i A');
        $appointmentDetails['status'] = ($appointment->is_completed === Appointment::STATUS_COMPLETED) ? __('messages.appointment.completed') : __('messages.appointment.pending');
        $appointmentDetails['problem'] = $appointment->problem;
        $appointmentDetails['is_completed'] = ($appointment->is_completed) ? 'completed' : 'pending';

        return $this->sendResponse($appointmentDetails, __('messages.flash.appointment_retrieved'));
    }
}
