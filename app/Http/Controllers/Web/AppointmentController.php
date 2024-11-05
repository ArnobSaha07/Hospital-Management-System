<?php

namespace App\Http\Controllers\Web;

use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Department;
use App\Models\Appointment;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AppBaseController;
use App\Repositories\AppointmentRepository;
use App\Http\Requests\CreateWebAppointmentRequest;
use App\Mail\NotifyMailHospitalAdminForBookingAppointment;
use App\Repositories\AppointmentTransactionRepository;

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
     * Store a newly created appointment in storage.
     */
    public function store(CreateWebAppointmentRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['opd_date'] = $input['opd_date'].$input['time'];
        $input['status'] = true;
        $jsonFields = [];

        foreach ($input as $key => $value) {
            if (strpos($key, 'field') === 0) {
                $jsonFields[$key] = $value;
            }
        }

        $input['custom_field'] = !empty($jsonFields) ? $jsonFields : null;
        try {
            DB::beginTransaction();
            if ($input['patient_type'] == 2 && ! empty($input['patient_type'])) {
                $input['tenant_id'] = User::where('username', $input['hospital_username'])->first()->tenant_id;
                if($input['payment_type'] != 8 && $input['payment_type'] != 7){
                    $data = $this->appointmentRepository->create($input);
                }
                if($input['payment_type'] == 1 || $input['payment_type'] == 2 || $input['payment_type'] == 3 || $input['payment_type'] == 5){
                   $data->update(['patient_type',\App\Models\Appointment::TYPE_CASH]);
                }
                if($input['payment_type'] == 1){
                    DB::commit();
                    return $this->sendResponse([
                        'appointment_id' => $data->id,
                        'payment_type' => $input['payment_type'],
                        'amount' => $input['appointment_charge'],
                    ],'Stripe session created successfully');

                }elseif($input['payment_type'] == 2){
                    DB::commit();
                    return $this->sendResponse([
                        'appointment_id' => $data->id,
                        'payment_type' => $input['payment_type'],
                        'amount' =>  $input['appointment_charge'],
                    ],'Razorpay session created successfully');

                }elseif($input['payment_type'] == 3){
                    DB::commit();
                    return $this->sendResponse([
                        'appointment_id' => $data->id,
                        'payment_type' => $input['payment_type'],
                        'amount' =>  $input['appointment_charge'],
                    ],'Razorpay session created successfully');
                }
                elseif($input['payment_type'] == 5){
                    DB::commit();
                    return $this->sendResponse([
                        'appointment_id' => $data->id,
                        'payment_type' => $input['payment_type'],
                        'amount' =>  $input['appointment_charge'],
                    ],'FlutterWave session created successfully');
                }elseif($input['payment_type'] == 7){
                    DB::commit();
                    return $this->sendResponse([
                        'input' => $input,
                        'payment_type' => $input['payment_type'],
                        'amount' =>  $input['appointment_charge'],
                    ],'PhonePe session created successfully');
                }elseif($input['payment_type'] == 8){
                    DB::commit();
                    $payStackData = [
                        'payment_type' => $input['payment_type'],
                        'amount' =>  $input['appointment_charge'],
                        'input' => $input,
                    ];

                    return $this->sendResponse(['payStackData' => $payStackData],'Paystack created successfully');
                }
                else{
                    $appointment = Appointment::find($data['id']);
                    $appointment->update(['payment_status' => 1]);
                }

                // $this->appointmentRepository->create($input);

                $hospitalDefaultAdmin = User::where('username', $input['hospital_username'])->first();

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

            }

            if ($input['patient_type'] == 1 && ! empty($input['patient_type'])) {
                $emailExists = User::whereEmail($input['email'])->exists();
                if ($emailExists) {
                    return $this->sendError('Email already exists, please select old patient.');
                }
                $appointmentDepartmentId = $input['department_id'];

                $input['department_id'] = Department::whereName('Patient')->first()->id;
                $input['dob'] = (! empty($input['dob']) || isset($input['dob'])) ? $input['dob'] : null;
                $input['phone'] = (! empty($input['phone']) || isset($input['phone'])) ? $input['phone'] : null;
                $input['password'] = Hash::make($input['password']);
                $input['tenant_id'] = User::where('username', $input['hospital_username'])->first()->tenant_id;
                $userData = Arr::only($input,
                    ['first_name', 'last_name', 'gender', 'password', 'email', 'department_id', 'status', 'tenant_id']);

                $user = User::create($userData);
                if (isset($input['email'])) {
                    $user->sendEmailVerificationNotification();
                }

                $patient = new Patient();
                $patient->user_id = $user->id;
                $patient->tenant_id = $user->tenant_id;
                $patient->patient_unique_id = strtoupper(Patient::generateUniquePatientId());
                $patient->save();

                $ownerId = $patient->id;
                $ownerType = Patient::class;
                $user->update(['owner_id' => $ownerId, 'owner_type' => $ownerType]);
                $user->assignRole($input['department_id']);
                $appointment = Appointment::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $input['doctor_id'],
                    'department_id' => $appointmentDepartmentId,
                    'opd_date' => $input['opd_date'],
                    'problem' => $input['problem'],
                    'tenant_id' => $input['tenant_id'],
                    'custom_field' => !empty($jsonFields) ? $jsonFields : null,
                ]);



                $hospitalDefaultAdmin = User::where('username', $input['hospital_username'])->first();
                if (! empty($hospitalDefaultAdmin)) {

                    $hospitalDefaultAdminEmail = $hospitalDefaultAdmin->email;
                    $doctor = Doctor::whereId($input['doctor_id'])->first();

                    $mailData = [
                        'booking_date' => Carbon::parse($input['opd_date'])->translatedFormat('g:i A').' '.Carbon::parse($input['opd_date'])->translatedFormat('jS M, Y'),
                        'patient_name' => $user->full_name,
                        'patient_email' => $user->email,
                        'doctor_name' => $doctor->user->full_name,
                        'doctor_department' => $doctor->department->title,
                        'doctor_email' => $doctor->user->email,
                    ];

                    $mailData['patient_type'] = 'New';

                    Mail::to($hospitalDefaultAdminEmail)
                        ->send(new NotifyMailHospitalAdminForBookingAppointment('emails.booking_appointment_mail',
                        __('messages.new_change.notify_mail_for_patient_book'),
                            $mailData));
                    Mail::to($doctor->user->email)
                        ->send(new NotifyMailHospitalAdminForBookingAppointment('emails.booking_appointment_mail',
                        __('messages.new_change.notify_mail_for_patient_book'),
                            $mailData));
                }

                if($input['payment_type'] == 1 || $input['payment_type'] == 2 || $input['payment_type'] == 3 || $input['payment_type'] == 5){
                    DB::commit();
                    return $this->sendResponse([
                        'appointment_id' => $appointment->id,
                        'payment_type' => $input['payment_type'],
                        'amount' =>$input['appointment_charge']
                    ],'payment session created successfully');

                }elseif($input['payment_type'] == 8){
                    DB::commit();
                    $input['patient_id'] = $patient->id;
                    $payStackData = [
                        'payment_type' => $input['payment_type'],
                        'amount' =>  $input['appointment_charge'],
                        'input' => $input,
                    ];
                    return $this->sendResponse(['payStackData' => $payStackData],'Paystack created successfully');
                }elseif($input['payment_type'] == 7){
                    DB::commit();
                    $input['patient_id'] = $patient->id;
                    return $this->sendResponse([
                        'input' => $input,
                        'payment_type' => $input['payment_type'],
                        'amount' =>  $input['appointment_charge'],
                    ],'PhonePe session created successfully');
                }else{
                    $appointment = Appointment::find($appointment->id);
                    $appointment->update(['payment_status' => 1]);
                }
                // $dataApp = $this->appointmentRepository->createNewAppointment($input);

            }

            DB::commit();

            return $this->sendSuccess('Appointment saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 404);
        }
    }

    public function getDoctors(Request $request): JsonResponse
    {
        $id = $request->get('id');

        $doctors = $this->appointmentRepository->getDoctors($id);

        return $this->sendResponse($doctors, 'Retrieved successfully');
    }

    public function getDoctorList(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $doctorArr = $this->appointmentRepository->getDoctorList($id);

        return $this->sendResponse($doctorArr, 'Retrieved successfully');
    }

    public function getBookingSlot(Request $request): JsonResponse
    {
        $inputs = $request->all();
        $data = $this->appointmentRepository->getBookingSlot($inputs);

        return $this->sendResponse($data, 'Retrieved successfully');
    }

    public function getPatientDetails($email): JsonResponse
    {
        /** @var Patient $patient */
        $patient = Patient::with('user')->get()->where('user.status', '=', 1)->where('user.email', $email)->first();
        $data = null;
        if ($patient != null) {
            $data = [
                $patient->id => $patient->user->full_name,
            ];
        }

        return $this->sendResponse($data, 'User Retrieved Successfully');
    }
}
