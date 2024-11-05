<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\AppBaseController;
use App\Models\AdvancedPayment;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Bill;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Nurse;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardAPIController extends AppBaseController
{
    public function index()
    {
        $totalSum = 0;
        $amount = Invoice::where('tenant_id',getLoggedInUser()->tenant_id)->get();

        foreach ($amount as $amounts) {
            $total = 0;
            if ($amounts['discount'] != 0) {
                $total += $amounts['amount'] - ($amounts['amount'] * $amounts['discount'] / 100);
            } else {
                $totalSum += $amounts['amount'];
            }

            $totalSum += $total;
        }

        $currency = Setting::where('key','current_currency')->Where('tenant_id', getLoggedInUser()->tenant_id)->first();

        $data['invoiceAmount'] = $totalSum;
        $data['billAmount'] = (float) Bill::where('tenant_id', getLoggedInUser()->tenant_id)->sum('amount');
        $data['paymentAmount'] = Payment::where('tenant_id',getLoggedInUser()->tenant_id)->sum('amount');
        $data['advancePaymentAmount'] = AdvancedPayment::where('tenant_id',getLoggedInUser()->tenant_id)->sum('amount');
        $data['doctor'] = Doctor::where('tenant_id',getLoggedInUser()->tenant_id)->count();
        $data['patients'] = Patient::where('tenant_id',getLoggedInUser()->tenant_id)->count();
        $data['nurses'] = Nurse::where('tenant_id',getLoggedInUser()->tenant_id)->count();
        $data['availableBeds'] = Bed::where('tenant_id',getLoggedInUser()->tenant_id)->whereIsAvailable(1)->count();
        $data['currency'] = $currency->value;
        $data['currency_symbol'] = getAdminCurrencySymbol($data['currency']);
        $now = Carbon::now();
        $sixDays = $now->copy()->addDays(6);
        $upcomingAppointments = Appointment::where('tenant_id',getLoggedInUser()->tenant_id)->with(['patient.user', 'doctor.user'])->whereBetween('opd_date', [$now, $sixDays])->orderBy('id','desc')->get();

        $appointments = [];

        foreach ($upcomingAppointments as $appointment) {
            $appointments[] = [
                'id' => $appointment->id ?? __('messages.common.n/a'),
                'patient_id' => $appointment->patient->id ?? __('messages.common.n/a'),
                'patient_name' => $appointment->patient->user->full_name ?? __('messages.common.n/a'),
                'patine_image' => $appointment->patient->patientUser->image_url ?? __('messages.common.n/a'),
                'appointment_date' => \Carbon\Carbon::parse($appointment->opd_date)->translatedFormat('jS M,Y')?? __('messages.common.n/a'),
                'appointment_time' => \Carbon\Carbon::parse($appointment->opd_date)->translatedFormat('h:i A') ?? __('messages.common.n/a'),
                'doctor_id' => $appointment->doctor->id ?? __('messages.common.n/a'),
                'doctor_name' => $appointment->doctor->user->full_name ?? __('messages.common.n/a'),
                'doctor_department_id' => $appointment->doctor->department->id ?? __('messages.common.n/a'),
                'doctor_department' => $appointment->doctor->department->title ?? __('messages.common.n/a')

            ];
        }

        $data['upcomingAppointments'] = $appointments;

        return $this->sendSuccess($data,'Admin Dashboard retrieve Successfully');
    }
}
