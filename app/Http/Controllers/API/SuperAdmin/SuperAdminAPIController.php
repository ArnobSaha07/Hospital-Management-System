<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\SuperAdminCurrencySetting;
use App\Models\SuperAdminSetting;
use App\Models\Transaction;
use App\Models\User;

use App\Repositories\DashboardRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuperAdminAPIController extends AppBaseController
{
    /** @var DashboardRepository */
    private $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function index()
    {
        $query = User::where('department_id', '=', User::USER_ADMIN)
            ->whereNotNull([
                'hospital_name',
                'username',
            ])->select('users.*');
        $data['users'] = $query->count();
        $data['revenue'] = Transaction::where('status', '=', Transaction::APPROVED)->sum('amount');
        $current_currency = SuperAdminSetting::where('key', '=', 'super_admin_currency')->first()->value;
        $data['currency'] = SuperAdminCurrencySetting::where('currency_code', strtoupper($current_currency))->first();
        $data['activeHospitalPlan'] = $this->dashboardRepository->getTotalActiveDeActiveHospitalPlans()['activePlansCount'];
        $data['deActiveHospitalPlan'] = $this->dashboardRepository->getTotalActiveDeActiveHospitalPlans()['deActivePlansCount'];

        return $this->sendResponse($data, 'Super Admin Dashboard retrieve Successfully');
    }

    public function incomeChart(Request $request)
    {
        $input = $request->all();
        $startDate = str_replace('/', '-', $input['start_date']);
        $endDate = str_replace('/', '-', $input['end_date']);
        $formatStartDate = Carbon::parse($startDate)->format('Y-m-d');
        $formatEndDate = Carbon::parse($endDate)->format('Y-m-d');

        $data = $this->dashboardRepository->totalFilterDay($formatStartDate, $formatEndDate);

        $chartData = [];

        foreach ($data['days'] as $index => $day) {
            $chartData[] = [
                'days' => $day,
                'income' => $data['income']['data'][$index],
            ];
        }

        return $this->sendResponse($chartData, 'Income Report retrieve successfully');
    }
}
