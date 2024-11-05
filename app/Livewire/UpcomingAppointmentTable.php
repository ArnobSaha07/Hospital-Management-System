<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UpcomingAppointmentTable extends LivewireTableComponent
{
    use WithPagination;

    protected $model = Appointment::class;

    public $showButtonOnHeader = false;

    public $showFilterOnHeader = false;

    public $buttonComponent = 'appointments.add-button';

    protected $listeners = ['refresh' => '$refresh'];

    // public function resetPage($pageName = 'page')
    // {
    //     $rowsPropertyData = $this->getRows()->toArray();
    //     $prevPageNum = $rowsPropertyData['current_page'] - 1;
    //     $prevPageNum = $prevPageNum > 0 ? $prevPageNum : 1;
    //     $pageNum = count($rowsPropertyData['data']) > 0 ? $rowsPropertyData['current_page'] : $prevPageNum;

    //     $this->setPage($pageNum, $pageName);
    // }

    public function configure(): void
    {
        $this->setDefaultSort('appointments.created_at', 'desc');
        $this->setQueryStringStatus(false);
        $this->setPrimaryKey('id');
        $this->setSearchVisibilityDisabled();
    }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {

        if(getLoggedInUser()->hasRole('Patient')){
            $data = Column::make(__('messages.appointment.doctor_department'), 'doctor.department.title')
            ->view('patients.patient_appointment.department')
            ->sortable()->searchable();
        }else{
            $data = Column::make(__('messages.appointment.doctor_department'), 'doctor.department.title')->hideIf(1);
        }
        return [
            Column::make(__('messages.case.patient'), 'patient.patientUser.email')
                ->hideIf('patient.patientUser.email'),
            Column::make(__('messages.case.patient'), 'doctor.doctorUser.email')
                ->hideIf('doctor.doctorUser.email'),
            Column::make(__('messages.case.patient'), 'patient.patientUser.first_name')
                ->view('appointments.columns.patient_name')
                ->sortable(),
            Column::make(__('messages.case.doctor'), 'doctor.doctorUser.first_name')
                ->view('appointments.columns.doctor_name')
                ->sortable(),
            Column::make(__('messages.case.patient'), 'patient_id')->hideIf(1),
            Column::make(__('messages.case.doctor'), 'doctor_id')->hideIf(1),
            $data,
            Column::make(__('messages.appointment.date'), 'opd_date')
                ->view('appointments.columns.date')
                ->sortable(),
        ];
    }

    public function builder(): Builder
    {
        $now = Carbon::today();
        $sixDays = $now->copy()->addDays(6);

        if(getLoggedInUser()->hasRole('Patient')){
            $query = Appointment::with(['patient.user', 'doctor.user'])->where('patient_id',getLoggedInUser()->owner_id)->whereBetween('opd_date',[$now, $sixDays])->select('appointments.*');
        }else{
            $query = Appointment::with(['patient.user', 'doctor.user'])->whereBetween('opd_date',[$now, $sixDays])->select('appointments.*');
        }

        return $query;
    }
}
