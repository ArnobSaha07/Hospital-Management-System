<?php

namespace App\Http\Controllers;

use App\Models\AdvancedPayment;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Bill;
use App\Models\Doctor;
use App\Models\Enquiry;
use App\Models\IpdBill;
use App\Models\IpdPatientDepartment;
use App\Models\LiveConsultation;
use App\Models\Module;
use App\Models\NoticeBoard;
use App\Models\Nurse;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SuperAdminCurrencySetting;
use App\Models\SuperAdminSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\DashboardRepository;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends AppBaseController
{
    private $dashboardRepository;

    /**
     * Create a new controller instance.
     */
    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->middleware('auth');
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return Factory|View
     */
    public function index(): View
    {
        return view('home');
    }

    /**
     * @return Factory|View
     */
    public function dashboard(): View
    {
        return view('dashboard.index');
    }

    public function superAdminDashboard(): View
    {
        return view('super_admin.dashboard.index');
    }

    public function incomeExpenseReport(Request $request): JsonResponse
    {
        $data = $this->dashboardRepository->getIncomeExpenseReport($request->all());

        return $this->sendResponse($data, __('messages.flash.income_and_expense_retrieved'));
    }

    /**
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function featureAvailability(): View
    {
        return view('menu_feature.index');
    }

    public function incomeChart(Request $request): JsonResponse
    {
        $input = $request->all();
        $startDate = str_replace('/', '-', $input['start_date']);
        $endDate = str_replace('/', '-', $input['end_date']);
        $formatStartDate = Carbon::parse($startDate)->format('Y-m-d');
        $formatEndDate = Carbon::parse($endDate)->format('Y-m-d');

        $data = $this->dashboardRepository->totalFilterDay($formatStartDate, $formatEndDate);

        return $this->sendResponse($data, __('messages.flash.income_report_generate'));
    }

    public function patientDashboard()
    {
        return view('patients.dashboard');
    }
}
