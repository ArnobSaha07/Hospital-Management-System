<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Module;
use App\Models\IpdBill;
use Livewire\Component;
use App\Models\Appointment;
use App\Models\LiveConsultation;
use App\Models\IpdPatientDepartment;

class PatientDashboard extends Component
{
    public $patientId;
    public $TotalAppointments;
    public $TodayAppointments;
    public $TotalMeetings;
    public $patientBill;
    public $ipdDept;
    public $IpdDueAmount;
    public $modules;

    public function mount()
    {
        $patientId = getLoggedInUser()->owner_id;
        $this->TotalAppointments = Appointment::where('patient_id',$patientId)->count();
        $this->TodayAppointments = Appointment::where('patient_id',$patientId)->whereBetween('opd_date',[Carbon::today()->startOfDay(),Carbon::today()->endOfDay()])->count();
        $this->TotalMeetings = LiveConsultation::where('patient_id',$patientId)->count();
        $this->patientBill = Bill::wherePatientId($patientId)->where('status',1)->sum('amount');
        $ipdDept = IpdPatientDepartment::wherePatientId($patientId)->pluck('id')->toArray();
        $this->IpdDueAmount = IpdBill::whereIn('ipd_patient_department_id',$ipdDept)->sum('net_payable_amount');
        $this->modules = Module::pluck('is_active', 'name')->toArray();
    }

    public function placeholder()
    {
        return view('livewire.admin-dashboard-skeleton');
    }

    public function render()
    {
        return view('livewire.patient-dashboard');
    }
}
