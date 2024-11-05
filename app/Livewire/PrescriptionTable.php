<?php

namespace App\Livewire;

use App\Models\Doctor;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PrescriptionTable extends LivewireTableComponent
{
    protected $model = Prescription::class;

    public $showButtonOnHeader = true;

    public $showFilterOnHeader = true;

    public $buttonComponent = 'prescriptions.add-button';

    public $FilterComponent = ['prescriptions.filter-button', Prescription::STATUS_ARR];

    public $statusFilter;

    protected $listeners = ['refresh' => '$refresh', 'changeFilter', 'resetPage'];

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
        $this->setPrimaryKey('id');
        $this->setDefaultSort('prescriptions.created_at', 'desc')
            ->setQueryStringStatus(false);

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->isField('status')) {
                return [
                    'class' => 'p-5',
                ];
            }

            return [];
        });
    }

    public function changeFilter($statusFilter)
    {
        $this->resetPage($this->getComputedPageName());
        $this->statusFilter = $statusFilter;
        $this->setBuilder($this->builder());
    }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.prescription.patient'), 'patient.patientUser.first_name')
                ->view('prescriptions.columns.patient_name')
                ->sortable()
                ->searchable(function(Builder $query, $direction){
                    $query->whereHas('patient.user', function (Builder $q) use ($direction) {
                        $q->whereRaw("TRIM(CONCAT(first_name,' ',last_name,' ')) like '%{$direction}%'");
                    });
                }),
            Column::make(__('messages.prescription.patient'), 'patient_id')->hideIf(1),
            Column::make('email','patient.patientUser.email')
                ->searchable()
                ->hideIf(1),
            Column::make(__('messages.doctor_opd_charge.doctor'), 'doctor.doctorUser.first_name')
                ->view('prescriptions.columns.doctor_name')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.doctor_opd_charge.doctor'), 'doctor_id')->hideIf(1),
            Column::make(__('messages.prescription.patient'), 'patient_id')->hideIf(1),
            Column::make('email','doctor.doctorUser.email')->searchable()->hideIf(1),
            Column::make('last_name','doctor.doctorUser.last_name')->searchable()->hideIf(1),
            Column::make(__('messages.new_change.added_at'), 'medical_history')
                ->view('prescriptions.columns.medical_history')
                ->sortable(),
            Column::make(__('messages.common.status'), 'status')
                ->view('prescriptions.columns.status'),
            Column::make(__('messages.common.action'), 'id')
                ->view('prescriptions.action'),
        ];
    }

    public function builder(): Builder
    {
        /** @var Prescription $query */
        if (! getLoggedinDoctor()) {
            $query = Prescription::select('prescriptions.*')->with('patient', 'doctor');
        } else {
            $doctorId = Doctor::where('user_id', getLoggedInUserId())->first();
            $query = Prescription::select('prescriptions.*')->with('patient', 'doctor')->where('doctor_id',
                $doctorId->id);
        }
        $query->when(isset($this->statusFilter), function (Builder $q) {
            if ($this->statusFilter == 2) {
            } else {
                $q->where('prescriptions.status', $this->statusFilter);
            }
        });

        return $query;
    }
}
