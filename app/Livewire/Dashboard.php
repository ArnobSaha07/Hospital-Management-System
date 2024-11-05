<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\SuperAdminSetting;
use Illuminate\Support\Facades\App;
use App\Models\SuperAdminCurrencySetting;
use App\Repositories\DashboardRepository;

class Dashboard extends Component
{
    public $query;
    public $users;
    public $revenue;
    public $current_currency;
    public $currency;
    public $dashboardRepository;
    public $activeHospitalPlan;
    public $deActiveHospitalPlan;

    public function mount()
    {
            $query = User::where('department_id', '=', User::USER_ADMIN)
            ->whereNotNull([
                'hospital_name',
                'username',
            ])->select('users.*');
        $this->users = $query->count();
        $this->revenue = Transaction::where('status', '=', Transaction::APPROVED)->sum('amount');
        $current_currency = SuperAdminSetting::where('key', '=', 'super_admin_currency')->first()->value;
        $this->currency= SuperAdminCurrencySetting::where('currency_code', strtoupper($current_currency))->first();
        $dashboardRepository = App::make(DashboardRepository::class);
        $this->activeHospitalPlan = $dashboardRepository->getTotalActiveDeActiveHospitalPlans()['activePlansCount'];
        $this->deActiveHospitalPlan= $dashboardRepository->getTotalActiveDeActiveHospitalPlans()['deActivePlansCount'];
    }

    public function placeholder()
    {
        return view('livewire.admin-dashboard-skeleton');
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
