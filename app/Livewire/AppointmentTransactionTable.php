<?php

namespace App\Livewire;

use App\Models\AppointmentTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AppointmentTransactionTable extends LivewireTableComponent
{
    protected $model = AppointmentTransaction::class;

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

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
        $this->setDefaultSort('appointment_transactions.created_at', 'desc');
        $this->setQueryStringStatus(false);
        $this->setPrimaryKey('id');
        $this->setQueryStringStatus(false);
    }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        return[
            Column::make(__('messages.case.patient'),'appointment.patient.patientUser.first_name')
                ->view('appointment_transaction.columns.patient_name')
                ->sortable()
                ->searchable(function(Builder $query, $direction){
                        $query->whereHas('appointment.patient.user', function (Builder $q) use ($direction) {
                            $q->whereRaw("TRIM(CONCAT(first_name,' ',last_name,' ')) like '%{$direction}%'");
                        });
                    }),
            Column::make('email', 'appointment.patient.patientUser.email')->searchable()->hideIf(1),
            Column::make(__('messages.case.doctor'),'appointment.doctor.doctorUser.first_name')
                ->view('appointment_transaction.columns.doctor_name')
                ->sortable()
                ->searchable(),
            Column::make('last_name', 'appointment.doctor.doctorUser.last_name')->searchable()->hideIf(1),
            Column::make('email','appointment.doctor.doctorUser.email')->searchable()->hideIf(1),
            Column::make(__('messages.opd_patient.appointment_date'),'appointment.opd_date')
                ->view('appointment_transaction.columns.appointment_date')
                ->sortable(),
            Column::make(__('messages.purchase_medicine.payment_mode'),'appointment.payment_type')
                ->view('appointment_transaction.columns.payment_type')
                ->sortable(),
            Column::make(__('messages.ambulance_call.amount'),'appointment.doctor.appointment_charge')
                ->view('appointment_transaction.columns.amount')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.common.created_at'),'created_at')
                ->view('appointment_transaction.columns.create_at')
                ->sortable()
                ->searchable(),
        ];
    }

    public function builder(): Builder
    {
        $query = AppointmentTransaction::with('appointment')->select('appointment_transactions.*');

        if(! getLoggedinDoctor()) {
            if(getLoggedinPatient()){
                $patientId = auth()->user()->patient->id;
                $query->whereHas('appointment', function ($q) use ($patientId) {
                    $q->where('patient_id', $patientId);
                });
            }
        }else{
            $doctorId = getLoggedInUser()->owner_id;
            $query->whereHas('appointment', function ($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId);
            });

        }

        return $query;
    }
}
