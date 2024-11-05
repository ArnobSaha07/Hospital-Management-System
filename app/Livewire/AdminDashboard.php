<?php

namespace App\Livewire;

use App\Models\Bed;
use App\Models\Bill;
use App\Models\Nurse;
use App\Models\Doctor;
use App\Models\Module;
use App\Models\Enquiry;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Setting;
use Livewire\Component;
use App\Models\NoticeBoard;
use App\Models\AdvancedPayment;

class AdminDashboard extends Component
{
    public $invoiceAmount;
    public $billAmount ;
    public $paymentAmount;
    public $advancePaymentAmount;
    public $doctors;
    public $patients;
    public $nurses;
    public $availableBeds;
    public $noticeBoards;
    public $enquiries;
    public $currency;
    public $modules;

    public function mount()
    {
        $this->invoiceAmount = totalAmount();
        $this->billAmount = Bill::sum('amount');
        $this->paymentAmount = Payment::sum('amount');
        $this->advancePaymentAmount = AdvancedPayment::sum('amount');
        $this->doctors = Doctor::count();
        $this->patients = Patient::count();
        $this->nurses = Nurse::count();
        $this->availableBeds = Bed::whereIsAvailable(1)->count();
        $this->noticeBoards = NoticeBoard::take(5)->orderBy('id', 'DESC')->get();
        $this->enquiries = Enquiry::where('status', 0)->latest()->take(5)->get();
        $this->currency = Setting::CURRENCIES;
        $this->modules = Module::pluck('is_active', 'name')->toArray();
    }

    public function placeholder()
    {
        return view('livewire.admin-dashboard-skeleton');
    }

    public function render()
    {
        return view('livewire.admin-dashboard');
    }
}
