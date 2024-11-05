<?php

namespace App\Livewire;

use App\Models\MedicineBill;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MedicineBillTable extends LivewireTableComponent
{
    public $showButtonOnHeader = true;

    public $buttonComponent = 'medicine-bills.add-button';

    protected $listeners = ['refresh' => '$refresh', 'changeFilter', 'resetPage'];

    protected $model = MedicineBill::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('medicine_bills.created_at', 'desc')
            ->setQueryStringStatus(false);

    }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.medicine_bills.bill_number'), 'bill_number')
                ->sortable()->searchable()->view('medicine-bills.columns.bill_id'),
            Column::make(__('messages.case.date'), 'created_at')
                ->sortable()->searchable()->view('medicine-bills.columns.bill_date'),
            Column::make(__('messages.invoice.patient'), 'patient_id')->hideIf(1),
            Column::make('email','patient.patientUser.email')->searchable()->hideIf(1),
            Column::make('last_name','patient.patientUser.last_name')->searchable()->hideIf(1),
            Column::make(__('messages.invoice.patient'), 'patient.patientUser.first_name')
                ->sortable()->searchable()->view('medicine-bills.columns.patient'),
            Column::make(__('messages.case.doctor'), 'doctor_id')->hideIf(1),
            Column::make('email','doctor.doctorUser.email')->searchable()->hideIf(1),
            Column::make('last_name','doctor.doctorUser.last_name')->searchable()->hideIf(1),
            Column::make(__('messages.case.doctor'), 'doctor.doctorUser.first_name')
                ->sortable()->searchable()->view('medicine-bills.columns.doctor'),
            Column::make(__('messages.ipd_payments.payment_mode'), 'payment_type')
                ->sortable()->searchable()->view('medicine-bills.columns.discount'),
            Column::make(__('messages.purchase_medicine.net_amount'), 'net_amount')
                ->sortable()->searchable()->view('medicine-bills.columns.amount'),
            Column::make(__('messages.medicine_bills.payment_status'), 'payment_status')
                ->sortable()->searchable()->view('medicine-bills.columns.payment_status'),
            Column::make(__('messages.common.action'), 'id')
                ->view('medicine-bills.columns.action'),
        ];
    }
    function builder(): Builder
    {
        /** @var MedicineBill $query */
        return MedicineBill::with(['patient','patient.user','doctor.user']);
    }
}
