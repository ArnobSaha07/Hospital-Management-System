<?php

namespace App\Http\Controllers;

use App\Exports\AppointmentExport;
use App\Http\Requests\CreateAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Mail\NotifyMailHospitalAdminForBookingAppointment;
use App\Models\Appointment;
use App\Models\CustomField;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserTenant;
use App\Repositories\AppointmentRepository;
use App\Repositories\AppointmentTransactionRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class AppointmentController
 */
class AppointmentController extends AppBaseController
{
    /** @var AppointmentRepository */
    private $appointmentRepository;

    /** @var AppointmentTransactionRepository */
    private $appointmentTransactionRepository;

    public function __construct(AppointmentRepository $appointmentRepo, AppointmentTransactionRepository $appointmentTransactionRepo)
    {
        $this->appointmentRepository = $appointmentRepo;
        $this->appointmentTransactionRepository = $appointmentTransactionRepo;
    }

    /**
     * Display a listing of the appointment.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $statusArr = Appointment::STATUS_ARR;

        return view('appointments.index', compact('statusArr'));
    }

    /**
     * Show the form for creating a new appointment.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $patients = $this->appointmentRepository->getPatients();
        $departments = $this->appointmentRepository->getDoctorDepartments();
        $statusArr = Appointment::STATUS_PENDING;
        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $stripeKeyValue = Setting::whereTenantId($tenantId)->where('key', '=', 'stripe_key')->first();
        if(isset($stripeKeyValue->value) && !empty($stripeKeyValue)){
            $stripeKey = $stripeKeyValue->value;
        }else{
            $stripeKey = null;
        }
        $customField = CustomField::where('module_name', CustomField::Appointment)->get()->toArray();

        return view('appointments.create', compact('patients', 'departments', 'statusArr', 'stripeKey', 'customField'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(CreateAppointmentRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['opd_date'] = $input['opd_date'].$input['time'];
        $input['is_completed'] = isset($input['status']) ? Appointment::STATUS_COMPLETED : Appointment::STATUS_PENDING;
        $input['payment_type'] = $input['payment_type'] ?? 4;
        if ($request->user()->hasRole('Patient')) {
            $input['patient_id'] = $request->user()->owner_id;
        }

        $jsonFields = [];

        foreach ($input as $key => $value) {
            if (strpos($key, 'field') === 0) {
                $jsonFields[$key] = $value;
            }
        }
        $input['custom_field'] = !empty($jsonFields) ? $jsonFields : null;

        if($input['payment_type'] != 8 && $input['payment_type'] != 7){
            $data = $this->appointmentRepository->create($input);
        }

        $this->appointmentRepository->createNotification($input);
        if ($input['payment_type'] == 1 || $input['payment_type'] == 2 || $input['payment_type'] == 3 || $input['payment_type'] == 5) {
            $data->update(['payment_type' => 4]);
        }
        if($input['payment_type'] == 1){

            return $this->sendResponse([
                'appointment_id' => $data->id,
                'payment_type' => $input['payment_type'],
                'amount' => $input['appointment_charge'],
            ],'Stripe session created successfully');

        }elseif($input['payment_type'] == 2){

            return $this->sendResponse([
                'appointment_id' => $data->id,
                'payment_type' => $input['payment_type'],
                'amount' =>  $input['appointment_charge'],
            ],'Razorpay session created successfully');
        }elseif($input['payment_type'] == 3){

            return $this->sendResponse([
                'appointment_id' => $data->id,
                'payment_type' => $input['payment_type'],
                'amount' =>  $input['appointment_charge'],
            ],'Paypal session created successfully');
        }elseif($input['payment_type'] == 5){

            return $this->sendResponse([
                'appointment_id' => $data->id,
                'payment_type' => $input['payment_type'],
                'amount' =>  $input['appointment_charge'],
            ],'FlutterWave created successfully');
        }elseif($input['payment_type'] == 7){

            return $this->sendResponse([
                'input' => $input,
                'payment_type' => $input['payment_type'],
                'amount' =>  $input['appointment_charge'],
            ],'PhonePe created successfully');
        }elseif($input['payment_type'] == 8){

            $payStackData = [
                'payment_type' => $input['payment_type'],
                'amount' =>  $input['appointment_charge'],
                'input' => $input,
            ];
            return $this->sendResponse(['payStackData' => $payStackData],'payStack created successfully');
        }else{
            $data = $this->appointmentTransactionRepository->store($data);
        }

        $userId = UserTenant::whereTenantId(getLoggedInUser()->tenant_id)->value('user_id');
        $hospitalDefaultAdmin = User::whereId($userId)->first();

        if (! empty($hospitalDefaultAdmin)) {

            $hospitalDefaultAdminEmail = $hospitalDefaultAdmin->email;
            $doctor = Doctor::whereId($input['doctor_id'])->first();
            $patient = Patient::whereId($input['patient_id'])->first();

            $mailData = [
                'booking_date' => Carbon::parse($input['opd_date'])->translatedFormat('g:i A').' '.Carbon::parse($input['opd_date'])->translatedFormat('jS M, Y'),
                'patient_name' => $patient->user->full_name,
                'patient_email' => $patient->user->email,
                'doctor_name' => $doctor->user->full_name,
                'doctor_department' => $doctor->department->title,
                'doctor_email' => $doctor->user->email,
            ];

            $mailData['patient_type'] = 'Old';

            Mail::to($hospitalDefaultAdminEmail)
                ->send(new NotifyMailHospitalAdminForBookingAppointment('emails.booking_appointment_mail',
                    __('messages.new_change.notify_mail_for_patient_book'),
                    $mailData));
            Mail::to($doctor->user->email)
                ->send(new NotifyMailHospitalAdminForBookingAppointment('emails.booking_appointment_mail',
                    __('messages.new_change.notify_mail_for_patient_book'),
                    $mailData));
        }

        return $this->sendSuccess(__('messages.flash.appointment_saved'));
    }

    /**
     * Display the specified appointment.
     *
     * @return Factory|View|RedirectResponse
     */
    public function show(Appointment $appointment): View
    {
        return view('appointments.show')->with('appointment', $appointment);
    }

    /**
     * Show the form for editing the specified appointment.
     *
     * @return Factory|View
     */

    public function edit(Appointment $appointment)
    {
        $patients = $this->appointmentRepository->getPatients();
        $doctors = $this->appointmentRepository->getDoctors($appointment->department_id);
        $departments = $this->appointmentRepository->getDoctorDepartments();
        $statusArr = $appointment->is_completed;
        $customField = CustomField::where('module_name', CustomField::Appointment)->get()->toArray();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors', 'departments', 'statusArr','customField'));
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Appointment $appointment, UpdateAppointmentRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['opd_date'] = $input['opd_date'].$input['time'];
        $input['is_completed'] = isset($input['status']) ? Appointment::STATUS_COMPLETED : Appointment::STATUS_PENDING;
        if ($request->user()->hasRole('Patient')) {
            $input['patient_id'] = $request->user()->owner_id;
        }
        $jsonFields = [];

        foreach ($input as $key => $value) {
            if (strpos($key, 'field') === 0) {
                $jsonFields[$key] = $value;
            }
        }

        $input['custom_field'] = !empty($jsonFields) ? $jsonFields : null;

        $appointment = $this->appointmentRepository->update($input, $appointment->id);

        return $this->sendSuccess(__('messages.flash.appointment_updated'));
    }

    /**
     * Remove the specified appointment from storage.
     *
     * @return RedirectResponse|Redirector|JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Appointment $appointment)
    {
        $this->appointmentRepository->delete($appointment->id);

        return $this->sendSuccess(__('messages.flash.appointment_delete'));
    }

    public function getDoctors(Request $request): JsonResponse
    {
        $id = $request->get('id');

        $doctors = $this->appointmentRepository->getDoctors($id);

        return $this->sendResponse($doctors, __('messages.flash.retrieve'));
    }

    public function getBookingSlot(Request $request): JsonResponse
    {
        $inputs = $request->all();
        $data = $this->appointmentRepository->getBookingSlot($inputs);

        return $this->sendResponse($data, __('messages.flash.retrieve'));
    }

    public function appointmentExport(): BinaryFileResponse
    {
        $response = Excel::download(new AppointmentExport, 'appointments-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    public function status(Appointment $appointment): JsonResponse
    {
        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAppointmentHasDoctor = Appointment::whereId($appointment->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientAppointmentHasDoctor) {
                return $this->sendError(__('messages.flash.appointment_not_found'));
            }
        }

        if (! canAccessRecord(Appointment::class, $appointment->id)) {
            return $this->sendError(__('messages.flash.appointment_not_found'));
        }
        $isCompleted = ! $appointment->is_completed;
        $appointment->update(['is_completed' => $isCompleted]);

        return $this->sendSuccess(__('messages.common.status_updated_successfully'));
    }

    public function cancelAppointment(Appointment $appointment): JsonResponse
    {
        if (getLoggedInUser()->hasRole('Doctor')) {
            $patientAppointmentHasDoctor = Appointment::whereId($appointment->id)->whereDoctorId(getLoggedInUser()->owner_id)->exists();
            if (! $patientAppointmentHasDoctor) {
                return $this->sendError(__('messages.flash.appointment_not_found'));
            }
        }

        if (! canAccessRecord(Appointment::class, $appointment->id)) {
            return $this->sendError(__('messages.flash.appointment_not_found'));
        }

        $appointment->update(['is_completed' => Appointment::STATUS_CANCELLED]);

        return $this->sendSuccess(__('messages.flash.appointment_cancel'));
    }

    public function getAppointmentCharge()
    {
        $doctorId = request()->get('doctor_id');
        $charge = [];
        if(isset($doctorId)){
            $charge = Doctor::whereId($doctorId)->first();
        }

        return $this->sendResponse($charge,__('Appointment charge retrieved successfully.'));
    }
}
