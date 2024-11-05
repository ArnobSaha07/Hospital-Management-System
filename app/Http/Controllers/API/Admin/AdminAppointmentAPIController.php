<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\AppBaseController;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAppointmentAPIController extends AppBaseController
{
    public function index(): JsonResponse
    {
        $appointments = Appointment::where('tenant_id',getLoggedInUser()->tenant_id)->with('patient.patientUser', 'doctor.doctorUser', 'department')->orderBy('id', 'desc')->get();
        $data = [];

        foreach ($appointments as $appointment) {
            $data[] = $appointment->prepareAppointmentForAdmin();
        }

        return $this->sendResponse($data, 'Appointments Retrieved Successfully');
    }

    public function filter(Request $request): \Illuminate\Http\JsonResponse
    {
        $status = $request->get('status');

        if ($status == 'all') {
            $appointments = Appointment::where('tenant_id',getLoggedInUser()->tenant_id)->with('patient', 'doctor', 'department')->orderBy('id', 'desc')->get();
            $data = [];
            foreach ($appointments as $appointment) {
                $data[] = $appointment->prepareAppointmentForAdmin();
            }

            return $this->sendResponse($data, 'Appointments Retrieved Successfully');
        } elseif ($status == 'pending') {
            $appointments = Appointment::where('is_completed', Appointment::STATUS_PENDING)->orderBy('id', 'desc')->with('patient', 'doctor', 'department')->get();
            $data = [];
            foreach ($appointments as $appointment) {
                $data[] = $appointment->prepareAppointmentForAdmin();
            }

            return $this->sendResponse($data, 'Appointments Retrieved Successfully');
        } elseif ($status == 'completed') {
            $appointments = Appointment::where('is_completed', Appointment::STATUS_COMPLETED)->where('tenant_id',getLoggedInUser()->tenant_id)->orderBy('id', 'desc')->with('patient', 'doctor', 'department')->get();
            $data = [];
            foreach ($appointments as $appointment) {
                $data[] = $appointment->prepareAppointmentForAdmin();
            }

            return $this->sendResponse($data, 'Appointments Retrieved Successfully');
        } elseif ($status == 'cancelled') {
            $appointments = Appointment::where('is_completed', Appointment::STATUS_CANCELLED)->where('tenant_id',getLoggedInUser()->tenant_id)->orderBy('id', 'desc')->with('patient', 'doctor', 'department')->get();
            $data = [];
            foreach ($appointments as $appointment) {
                $data[] = $appointment->prepareAppointmentForAdmin();
            }

            return $this->sendResponse($data, 'Appointments Retrieved Successfully');
        } elseif ($status == 'past') {
            $appointments = Appointment::whereDate('opd_date', '<', Carbon::today())->where('tenant_id',getLoggedInUser()->tenant_id)->orderBy('id', 'desc')->orderBy('id', 'desc')->with('patient', 'doctor', 'department')->get();
            $data = [];
            foreach ($appointments as $appointment) {
                $data[] = $appointment->prepareAppointmentForAdmin();
            }

            return $this->sendResponse($data, 'Appointments Retrieved Successfully');
        } else {
            return $this->sendError(__('messages.appointment').' '.__('messages.common.not_found'));
        }
    }
    public function confirmAppointment($id): \Illuminate\Http\JsonResponse
    {

        $appointment = Appointment::where('id', $id)->where('is_completed', '!=', Appointment::STATUS_CANCELLED)->where('tenant_id',getLoggedInUser()->tenant_id)->orderBy('id', 'desc')->first();
        if (! $appointment) {
            return $this->sendError(__('messages.appointment').' '.__('messages.common.not_found'));
        }

        $appointment->update(['is_completed' => Appointment::STATUS_COMPLETED]);

        return $this->sendSuccess(__('messages.common.appointment_confirmed_successfully'));
    }

    public function cancelAppointment(Request $request): JsonResponse
    {
        $appointment = Appointment::where('id', $request->id)->where('tenant_id',getLoggedInUser()->tenant_id)->orderBy('id', 'desc')->first();

        if (! $appointment) {
            return $this->sendError(__('messages.web_menu.appointment').' '.__('messages.common.not_found'));
        }

        $appointment->update(['is_completed' => Appointment::STATUS_CANCELLED]);

        return $this->sendSuccess(__('messages.web_menu.appointment').' '.__('messages.common.cancelled_successfully'));
    }


}
