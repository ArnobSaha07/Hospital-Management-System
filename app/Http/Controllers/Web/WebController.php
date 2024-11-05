<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AdvancedPayment;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Bill;
use App\Models\CustomField;
use App\Models\Doctor;
use App\Models\DoctorDepartment;
use App\Models\Document;
use App\Models\FrontService;
use App\Models\FrontSetting;
use App\Models\HospitalSchedule;
use App\Models\Invoice;
use App\Models\NoticeBoard;
use App\Models\Nurse;
use App\Models\Patient;
use App\Models\PatientAdmission;
use App\Models\PatientCase;
use App\Models\Schedule;
use App\Models\ScheduleDay;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\VaccinatedPatients;
use App\Models\Vaccination;
use App\Repositories\AdvancedPaymentRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\PatientRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class WebController extends Controller
{
    /** @var AppointmentRepository */
    private $appointmentRepository;

    /** @var PatientRepository */
    private $patientRepository;

    public function __construct(AppointmentRepository $appointmentRepository, PatientRepository $patientRepo)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->patientRepository = $patientRepo;
    }

    /**
     * @return Factory|View
     */
    public function index(): View
    {
        $totalbeds = Bed::count();
        $totalDoctorNurses = Doctor::count() + Nurse::count();
        $totalPatient = Patient::count();
        $doctorsDepartments = DoctorDepartment::take(6)->toBase()->get();
        $doctorAppointments = Doctor::withCount('appointments')->with('department', 'doctorUser')->whereHas('User',
            function (Builder $query) {
                $query->where('status', User::ACTIVE);
            })->distinct()->take(6)->orderByDesc('appointments_count')->get();
        $todayNotice = NoticeBoard::whereDate('created_at', Carbon::today()->toDateTimeString())->latest()->first();
        $testimonials = Testimonial::with('media')->get();
        $doctors = Doctor::with([
            'user' => function ($q) {
                $q->select('id', 'first_name', 'last_name');
            },
        ])->get();
        $frontSetting = FrontSetting::whereType(FrontSetting::HOME_PAGE)->pluck('value', 'key')->toArray();
        $frontServices = FrontService::all()->take(8);

        return view('web.home.index',
            compact('doctorsDepartments', 'doctors', 'todayNotice', 'testimonials', 'totalbeds',
                'totalDoctorNurses', 'totalPatient', 'doctorAppointments', 'frontSetting', 'frontServices'));
    }

    /**
     * @return Factory|View
     */
    public function demo()
    {
        return \view('web.demo.index');
    }

    /**
     * @return Factory|View
     */
    public function modulesOfHms()
    {
        return \view('web.modules_of_hms.index');
    }

    /**
     * @return bool
     */
    public function changeLanguage(Request $request)
    {
        Session::put('languageName', $request->input('languageName'));

        return true;
    }

    /**
     * @return bool
     */
    public function languageChangeName(Request $request)
    {
        Session::put('languageChangeName', $request->input('languageName'));

        return true;
    }

    /**
     * @return Application|Factory|View
     */
    public function aboutUs(): View
    {
        $frontSetting = FrontSetting::whereType(FrontSetting::ABOUT_US)->pluck('value', 'key')->toArray();
        $totalbeds = Bed::count();
        $totalDoctorNurses = Doctor::count() + Nurse::count();
        $totalPatient = Patient::count();
        $testimonials = Testimonial::with('media')->get();
        $doctors = Doctor::withCount(['appointments', 'patients'])->with('department', 'doctorUser')->whereHas('doctorUser',
            function (Builder $query) {
                $query->where('status', User::ACTIVE);
            })->distinct()->take(4)->orderByDesc('appointments_count')->get();

        return view('web.home.about_us',
            compact('frontSetting', 'totalbeds', 'totalDoctorNurses', 'totalPatient', 'testimonials', 'doctors'));
    }

    public function appointmentFromOther(Request $request, $user): RedirectResponse
    {
        $data = $request->all();

        return redirect()->route('appointment', $user)->with(['data' => $data]);
    }

    /**
     * @return Application|Factory|View
     */
    public function appointment(Request $request)
    {
        $departments = $this->appointmentRepository->getDoctorDepartments();
        $doctors = $this->appointmentRepository->getDoctorLists();
        $customField = CustomField::where('module_name', CustomField::Appointment)->get()->toArray();
        if(getLoggedInUser()){
            $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
            $stripeKeyValue = Setting::whereTenantId($tenantId)->where('key', '=', 'stripe_key')->first();
            if(isset($stripeKeyValue->value) && !empty($stripeKeyValue)){
                $stripeKey = $stripeKeyValue->value;
            }else{
                $stripeKey = null;
            }
            return view('web.home.appointment', compact('departments', 'doctors', 'stripeKey', 'customField'));
        }else{
            $stripeKey = null;
            return redirect(route('login'));
        }

        return view('web.home.appointment', compact('departments', 'doctors', 'stripeKey', 'customField'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function services(): View
    {
        $frontServices = FrontService::paginate(8);

        return view('web.home.services', compact('frontServices'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function doctors(): View
    {
        $doctors = Doctor::withCount(['appointments', 'patients'])->with('department', 'doctorUser')->whereHas('doctorUser',
            function (Builder $query) {
                $query->where('status', User::ACTIVE);
            })->distinct()->orderByDesc('appointments_count')->paginate(8);

        return view('web.home.doctors', compact('doctors'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function termsOfService(): View
    {
        $frontSetting = FrontSetting::whereType(FrontSetting::HOME_PAGE)->pluck('value', 'key')->toArray();

        return view('web.home.terms-of-service', compact('frontSetting'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function privacyPolicy(): View
    {
        $frontSetting = FrontSetting::whereType(FrontSetting::HOME_PAGE)->pluck('value', 'key')->toArray();

        return view('web.home.privacy-policy', compact('frontSetting'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function workingHours(): View
    {
        $hospitalSchedules = HospitalSchedule::all()->sortBy('day_of_week');
        $weekDay = HospitalSchedule::WEEKDAY_FULL_NAME;
        $doctors = Doctor::with('doctorUser')->get();

        return view('web.home.working-hours', compact('hospitalSchedules', 'weekDay', 'doctors'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function testimonials(): View
    {
        $testimonials = Testimonial::with('media')->paginate(6);

        return view('web.home.testimonials', compact('testimonials'));
    }

    public function setLanguage(Request $request): RedirectResponse
    {
        Session::put('languageName', $request['languageName']);
        App::setLocale(session('languageName'));

        return redirect()->back();
    }

    public function patientDetails($userName,$uniqueCode)
    {
        $data = [];
        $patient = Patient::with(['patientUser','address','SmartCardTemplate'])->where('patient_unique_id', $uniqueCode)->first();
        $data = $this->patientRepository->getPatientAssociatedData($patient->id);
        $advancedPaymentRepo = App::make(AdvancedPaymentRepository::class);
        $patients = $advancedPaymentRepo->getPatients();
        $user = Auth::user();
        // if (!empty($user) && $user->hasRole('Doctor')) {
        //     $vaccinationPatients = getPatientsList($user->owner_id);
        // } else {
        //     $vaccinationPatients = Patient::getActivePatientNames();
        // }
        // $vaccinations = Vaccination::toBase()->pluck('name', 'id')->toArray();
        // natcasesort($vaccinations);
        $data['patientCases'] = PatientCase::with('doctor')->where('patient_id', $patient->id)->get();
        $data['patientAdmissions'] = PatientAdmission::with('patient.patientUser','doctor.doctorUser', 'package', 'insurance')
            ->where('patient_id', $patient->id)->get();
        $data['appointments'] = Appointment::with('doctor.doctorUser','doctor.department')->where('patient_id', $patient->id)->get();
        $data['bills'] = Bill::where('patient_id', $patient->id)->get();
        $data['invoices'] = Invoice::where('patient_id', $patient->id)->get();
        $data['advancePayments'] = AdvancedPayment::where('patient_id',  $patient->id)->get();
        $data['documents'] = Document::with('documentType')->where('patient_id', $patient->id)->get();
        $data['vaccinations'] = VaccinatedPatients::with('vaccination')->where('patient_id', $patient->id)->get();

        return view('web.home.smart-patient-card-details', compact('data'));
    }

    public function doctorDetails($userName,$id)
    {
        $doctorDetails = Doctor::with(['cases.patient.patientUser', 'patients.patientUser', 'schedules', 'payrolls', 'doctorUser',
            'address', 'appointments.doctor.doctorUser', 'appointments.patient.patientUser', 'appointments.department',
        ])->where('id',$id)->first();
        $schedules = ScheduleDay::where('doctor_id', $id)->get();

        return view('web.home.doctor-details',compact('doctorDetails','schedules','userName'));
    }
}
