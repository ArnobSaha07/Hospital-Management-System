<?php

namespace App\Livewire;

use App\Models\IpdPatientDepartment;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class IpdPatientTable extends LivewireTableComponent
{
    public $showButtonOnHeader = false;

    public $showFilterOnHeader = false;

    public $paginationIsEnabled = true;

    protected $model = IpdPatientDepartment::class;

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
        $this->setPrimaryKey('id')
            ->setDefaultSort('ipd_patient_departments.created_at', 'desc')
            ->setQueryStringStatus(false);

            $this->setThAttributes(function (Column $column) {
                if ($column->getTitle() == __('messages.bill.total_amount')) {
                    return [
                        'class' => 'price-column',
                    ];
                }

                return [];
            });
    }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.ipd_patient.ipd_number'), 'ipd_number')
                ->view('ipd_patient_list.columns.ipd_number')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.ipd_patient.doctor_id'), 'doctor.doctorUser.first_name')
                ->view('ipd_patient_list.columns.doctor')
                ->sortable()
                ->searchable(),
            Column::make('email', 'doctor.doctorUser.email')->searchable()->hideIf(1),
            Column::make('last_name', 'doctor.doctorUser.last_name')->searchable()->hideIf(1),
            Column::make(__('messages.ipd_patient.admission_date'), 'admission_date')
                ->view('ipd_patient_list.columns.admission_date')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.ipd_patient.bed_id'), 'bed.name')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.bill.total_amount'), 'bill.total_payments')
                ->view('ipd_patient_departments.columns.total_payment')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.lunch_break.discharge'), 'is_discharge')
                ->view('ipd_patient_departments.columns.discharge_status')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.ipd_patient.bill_status'), 'bill_status')
                ->view('ipd_patient_departments.columns.bill_status'),
            Column::make(__('messages.ipd_patient.doctor_id'), 'doctor.doctorUser.last_name')
                ->hideIf('doctor_id'),
        ];
    }

    public function builder(): Builder
    {
        /** @var IpdPatientDepartment $query */
        $query = IpdPatientDepartment::with(['patient.patientUser', 'doctor.doctorUser', 'bed', 'bedAssign'])
            ->where('patient_id', getLoggedInUser()->owner_id)->select('ipd_patient_departments.*');

        return $query;
    }
}
