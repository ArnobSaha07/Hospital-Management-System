<?php

use App\Http\Controllers\API\Admin\AdminAppointmentAPIController;
use App\Http\Controllers\API\Admin\AdminDashboardAPIController;
use App\Http\Controllers\API\Admin\AdminSettingAPIController;
use App\Http\Controllers\API\Admin\PatientAPIController;
use App\Http\Controllers\API\AppointmentAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BillAPIController;
use App\Http\Controllers\API\DiagnosisTestAPIController;
use App\Http\Controllers\API\Doctor\BirthReportAPIController;
use App\Http\Controllers\API\Doctor\DoctorAppointmentAPIController;
use App\Http\Controllers\API\Doctor\DoctorBedAssignController;
use App\Http\Controllers\API\Doctor\DoctorAPIController;
use App\Http\Controllers\API\Doctor\DoctorDeathReportAPIController;
use App\Http\Controllers\API\Doctor\DoctorInvestigationReportController;
use App\Http\Controllers\API\Doctor\DoctorLiveConsultationAPIController;
use App\Http\Controllers\API\Doctor\DoctorOperationAPIController;
use App\Http\Controllers\API\Doctor\DoctorPatientAdmissionAPIController;
use App\Http\Controllers\API\Doctor\PayrollAPIController;
use App\Http\Controllers\API\DocumentAPIController;
use App\Http\Controllers\API\InvoiceAPIController;
use App\Http\Controllers\API\LiveConsultationAPIController;
use App\Http\Controllers\API\NoticeboardAPIController;
use App\Http\Controllers\API\PatientAdmissionAPIController;
use App\Http\Controllers\API\PatientCaseAPIController;
use App\Http\Controllers\API\PrescriptionAPIController;
use App\Http\Controllers\API\RegistrationAPIController;
use App\Http\Controllers\API\SuperAdmin\SettingAPIController;
use App\Http\Controllers\API\SuperAdmin\SuperAdminAPIController;
use App\Http\Controllers\API\SuperAdmin\SuperAdminHospitalAPIController;
use App\Http\Controllers\API\SuperAdmin\SuperAdminHospitalTypeAPIContoller;
use App\Http\Controllers\API\SuperAdmin\SuperAdminSubscriptionsAPIController;
use App\Http\Controllers\API\SuperAdmin\SuperAdminTransactionAPIController;
use App\Http\Controllers\API\UserAPIController;
use App\Http\Controllers\API\VaccinatedPatientAPIController;
use App\Models\Setting;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/patient-register', [RegistrationAPIController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/forgot-password',
    [AuthController::class, 'sendPasswordResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
Route::post('/password',
    [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('set.password');
Route::post('/reset-password', [AuthController::class, 'changePassword'])->name('password.reset');
Route::post('/hospitals-register', [SuperAdminHospitalAPIController::class, 'hospitalRegister']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // update user profile
    Route::get('get-profile', [UserAPIController::class, 'getProfile'])->name('profile.get');
    Route::get('edit-profile', [UserAPIController::class, 'editProfile'])->name('edit-profile');
    Route::post('update-profile', [UserAPIController::class, 'updateProfile'])->name('update-profile');
    Route::patch('/change-password', [UserAPIController::class, 'changePassword'])->name('user.changePassword');

    //notice-boards
    Route::get('/notice-board', [NoticeboardAPIController::class, 'index']);
    Route::post('/notice-board-show/{id}', [NoticeboardAPIController::class, 'show']);

    //Super Admin-panel APIs
    Route::middleware('role:Super Admin')->group(function () {

        Route::post('hospitals-store', [SuperAdminHospitalAPIController::class, 'createHospital']);

        //Dashboard
        Route::get('dashboard',[SuperAdminAPIController::class,'index']);
        Route::get('income-chart', [SuperAdminAPIController::class, 'incomeChart']);

        //Setting
        Route::get('general-settings',[SettingAPIController::class, 'editSuperAdminSettings']);
        Route::post('general-settings', [SettingAPIController::class, 'updateSuperAdminSettings']);

        //Transactions
        Route::get('transactions', [SuperAdminTransactionAPIController::class, 'showTransaction']);
        Route::post('transactions-filter', [SuperAdminTransactionAPIController::class, 'filter']);

        //Subscription
        Route::get('subscription', [SuperAdminSubscriptionsAPIController::class, 'showSubscription']);
        Route::get('subscription-edit/{id}', [SuperAdminSubscriptionsAPIController::class, 'editSubscriptions']);
        Route::post('subscription-update/{id}', [SuperAdminSubscriptionsAPIController::class, 'updateSubscriptions']);
        Route::post('subscription-filter', [SuperAdminSubscriptionsAPIController::class, 'filter']);

        //Hospitals
        Route::get('hospitals', [SuperAdminHospitalAPIController::class, 'showHospital']);
        Route::get('hospitals-edit/{id}', [SuperAdminHospitalAPIController::class, 'editHospital']);
        Route::post('hospitals-update/{id}', [SuperAdminHospitalAPIController::class, 'updateHospital']);
        Route::delete('hospitals-delete/{id}', [SuperAdminHospitalAPIController::class, 'deleteHospital']);
        Route::post('hospitals-filter', [SuperAdminHospitalAPIController::class,'filter']);

        //Hospital-Type
        Route::get('/hospital-type',[SuperAdminHospitalTypeAPIContoller::class, 'index']);
        Route::post('/hospital-type-show/{id}',[SuperAdminHospitalTypeAPIContoller::class, 'show']);

    });


    //Patient-panel APIs
    Route::middleware('role:Admin|Patient')->group(function () {

        //Appointments
        Route::get('appointments', [AppointmentAPIController::class, 'index']);
        Route::post('/appointment-filter', [AppointmentAPIController::class, 'filter']);
        Route::post('/cancel-appointment', [AppointmentAPIController::class, 'cancelAppointment']);
        Route::post('/delete-appointment', [AppointmentAPIController::class, 'destroy']);
        Route::get('/doctor-department', [AppointmentAPIController::class, 'getDoctorDepartment']);
        Route::post('/doctor/{id}', [AppointmentAPIController::class, 'getDoctors']);
        Route::post('/slot-booking', [AppointmentAPIController::class, 'bookingSlots']);
        Route::post('/appointment-create', [AppointmentAPIController::class, 'store']);
        Route::get('admin-appointments/filter', [AdminAppointmentAPIController::class, 'filter']);

        //Bills
        Route::get('bills', [BillAPIController::class, 'index']);
        Route::get('bills/{id}', [BillAPIController::class, 'show']);

        //Diagnosis Test
        Route::get('diagnosis', [DiagnosisTestAPIController::class, 'index']);
        Route::get('diagnosis/{id}', [DiagnosisTestAPIController::class, 'show']);

        //documents
        Route::get('/documents', [DocumentAPIController::class, 'index']);
        Route::get('/document-type', [DocumentAPIController::class, 'getDocumentTypes']);
        Route::post('/document-store', [DocumentAPIController::class, 'create']);
        Route::post('/document-update/{id}', [DocumentAPIController::class, 'update']);
        Route::get('/document-edit/{id}', [DocumentAPIController::class, 'edit']);
        Route::get('/document-delete/{id}', [DocumentAPIController::class, 'destroy']);
        Route::get('/document-download/{id}', [DocumentAPIController::class, 'downloadDocs']);

        //invoice
        Route::get('/invoices', [InvoiceAPIController::class, 'index']);
        Route::get('/invoice/{id}', [InvoiceAPIController::class, 'show']);

        // Live Consultation
        Route::get('live-consultation', [LiveConsultationAPIController::class, 'index']);
        Route::get('live-consultation/{id}', [LiveConsultationAPIController::class, 'show']);
        Route::get('live-consultation-meeting/{id}', [LiveConsultationAPIController::class, 'meeting']);
        Route::post('live-consultation-filter', [LiveConsultationAPIController::class, 'filter']);

        //Patient Cases
        Route::get('patient-cases', [PatientCaseAPIController::class, 'index']);
        Route::get('patient-cases/{id}', [PatientCaseAPIController::class, 'show']);

        //Patient Admissions
        Route::get('patient-admissions', [PatientAdmissionAPIController::class, 'index']);
        Route::get('patient-admissions/{id}', [PatientAdmissionAPIController::class, 'show']);

        //Prescriptions
        Route::get('patient-prescription', [PrescriptionAPIController::class, 'index']);
        Route::get('patient-prescription/{id}', [PrescriptionAPIController::class, 'prescriptionShow']);

        //vaccinated patient
        Route::get('/vaccinated-patient', [VaccinatedPatientAPIController::class, 'index']);

    });
     //Doctor-panel APIs
    Route::middleware('role:Admin|Doctor')->prefix('doctors')->group(function () {

        //appointment
        Route::get('/appointment', [DoctorAppointmentAPIController::class, 'index']);
        Route::get('appointment-filter', [DoctorAppointmentAPIController::class, 'filter']);
        Route::post('confirm-appointment/{id}', [DoctorAppointmentAPIController::class, 'confirmAppointment']);

        //bed-assign
        Route::get('bed-assign', [DoctorBedAssignController::class, 'index']);
        Route::get('bed-assign-filter', [DoctorBedAssignController::class, 'filter']);
        Route::get('bed-assign/{id}', [DoctorBedAssignController::class, 'show']);
        Route::get('patient-cases', [DoctorBedAssignController::class, 'patientCase']);
        Route::get('ipd-patient/{caseId}', [DoctorBedAssignController::class, 'ipdPatient']);
        Route::get('beds', [DoctorBedAssignController::class, 'getBeds']);
        Route::post('bed-assign-edit-bed', [DoctorBedAssignController::class, 'getEditBeds']);
        Route::post('bed-assign-create', [DoctorBedAssignController::class, 'store']);
        Route::get('bed-assign-edit/{id}', [DoctorBedAssignController::class, 'edit']);
        Route::post('bed-assign-update/{id}', [DoctorBedAssignController::class, 'update']);
        Route::post('bed-assign-delete/{id}', [DoctorBedAssignController::class, 'delete']);
        Route::get('bed-status', [DoctorBedAssignController::class, 'showBedStatus']);
        Route::get('bed-status-detail/{id}', [DoctorBedAssignController::class, 'showBedStatusDetail']);



        //Doctors
        Route::get('doctors', [DoctorAPIController::class, 'index']);
        Route::get('doctors/{id}', [DoctorAPIController::class, 'show']);
        Route::post('doctors/filter', [DoctorAPIController::class, 'filter']);

        //Schedules
        Route::get('doctor-schedule', [DoctorAPIController::class, 'doctorScheduleList']);
        Route::post('doctor-schedule/update/{id}', [DoctorAPIController::class, 'doctorScheduleUpdate']);

        //Prescriptions
        Route::get('prescriptions', [PrescriptionAPIController::class, 'DoctorPrescriptionList']);
        Route::get('prescriptions/{id}', [PrescriptionAPIController::class, 'prescriptionShow']);
        Route::delete('prescriptions/{id}', [PrescriptionAPIController::class, 'destroy']);

        //Documents
        Route::get('doctor-documents', [DocumentAPIController::class, 'index']);
        Route::get('users', [DoctorAPIController::class, 'users']);
        Route::get('doctor-documents/edit/{id}', [DocumentAPIController::class, 'edit']);
        Route::get('doctor-document-type', [DocumentAPIController::class, 'getDocumentTypes']);
        Route::get('doctor-patients', [DocumentAPIController::class, 'getPatientList']);
        Route::post('doctor-document-store', [DocumentAPIController::class, 'create']);
        Route::post('doctor-document-update/{id}', [DocumentAPIController::class, 'update']);
        Route::delete('doctor-document-delete/{id}', [DocumentAPIController::class, 'destroy']);

        //Diagnosis Test
        Route::get('doctor-diagnosis', [DiagnosisTestAPIController::class, 'index']);
        Route::get('doctor-diagnosis/{id}', [DiagnosisTestAPIController::class, 'show']);
        Route::delete('doctor-diagnosis/{id}', [DiagnosisTestAPIController::class, 'destroy']);

        //live-consultation
        Route::get('live-consultation', [DoctorLiveConsultationAPIController::class, 'index']);
        Route::get('live-consultation-meeting/{id}',
            [DoctorLiveConsultationAPIController::class, 'liveConsultancyMeeting']);
        Route::get('live-consultation-filter', [DoctorLiveConsultationAPIController::class, 'filter']);
        Route::get('live-consultation-show/{id}', [DoctorLiveConsultationAPIController::class, 'show']);

          //PayRoll
        Route::get('doctor-payroll', [PayrollAPIController::class, 'index']);
        Route::get('doctor-payroll/{id}', [PayrollAPIController::class, 'show']);

        //patient-admission
        Route::get('patient-admission', [DoctorPatientAdmissionAPIController::class, 'index']);
        Route::get('patient-admission-show/{id}', [DoctorPatientAdmissionAPIController::class, 'show']);
        Route::delete('patient-admission-delete/{id}', [DoctorPatientAdmissionAPIController::class, 'delete']);

        //birth reports
        Route::get('birth-report', [BirthReportAPIController::class, 'index']);
        Route::delete('birth-report/{id}', [BirthReportAPIController::class, 'delete']);

        //operation report
        Route::get('operation-report', [DoctorOperationAPIController::class, 'index']);
        Route::delete('operation-report/{id}', [DoctorOperationAPIController::class, 'delete']);

        //death reports
        Route::get('death-report', [DoctorDeathReportAPIController::class, 'index']);
        Route::delete('death-report/{id}', [DoctorDeathReportAPIController::class, 'delete']);

         //investigation report
         Route::get('investigation-report', [DoctorInvestigationReportController::class, 'index']);
         Route::delete('investigation-report/{id}', [DoctorInvestigationReportController::class, 'delete']);

        //case detail
        Route::get('case-detail/{caseId}', [DoctorOperationAPIController::class, 'show']);


    });

    Route::middleware('role:Admin')->group(function () {

        //Admin Dashboard
        Route::get('admin-dashboard',[AdminDashboardAPIController::class,'index']);

        //Appointments
        Route::get('admin-appointments',[AdminAppointmentAPIController::class,'index']);
        Route::get('admin-appointments/filter', [AdminAppointmentAPIController::class, 'filter']);
        Route::post('admin-appointments/confirm/{id}', [AdminAppointmentAPIController::class, 'confirmAppointment']);
        Route::post('admin-appointments/cancel', [AdminAppointmentAPIController::class, 'cancelAppointment']);

        //Patients
        Route::get('patients-list',[PatientAPIController::class,'index']);
        Route::get('patient-details/{id}',[PatientAPIController::class,'show']);
        Route::post('patients/filter',[PatientAPIController::class,'filter']);

        //Settings
        Route::get('edit-settings',[AdminSettingAPIController::class,'editAdminSetting']);
        Route::post('update-settings',[AdminSettingAPIController::class,'updateAdminSetting']);
    });

});
