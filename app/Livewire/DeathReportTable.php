<?php

namespace App\Livewire;

use App\Models\DeathReport;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DeathReportTable extends LivewireTableComponent
{
    protected $model = DeathReport::class;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'death_reports.add-button';

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
        $this->setDefaultSort('death_reports.created_at', 'desc');
        $this->setQueryStringStatus(false);
        $this->setThAttributes(function (Column $column) {
            if ($column->isField('id')) {
                return [
                    'class' => 'text-center',
                    'style' => 'padding-right:10px !important',
                ];
            }
            return [];
        });
    }

    public function placeholder()
    {
        if(getLoggedinPatient()){
            return view('livewire.report-skeleton');
        }
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        $patientColumn = [];
        if(!getLoggedinPatient()){
            $patientColumn =Column::make(__('messages.case.patient'), 'patient.patientUser.first_name')
            ->view('death_reports.columns.patient_name')
            ->searchable()
            ->sortable();
        }
        return [
            Column::make(__('messages.death_report.case_id'), 'patient_id')
                ->hideIf('patient.patientUser.email')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.death_report.case_id'), 'doctor_id')
                ->hideIf('doctor.doctorUser.email')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.death_report.case_id'), 'patient.patientUser.email')
                ->hideIf('patient.user.email')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.death_report.case_id'), 'doctor.doctorUser.email')
                ->hideIf('doctor.user.email')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.death_report.case_id'), 'case_id')
                ->view('death_reports.columns.case_id')
                ->searchable()
                ->sortable(),
            $patientColumn,
            Column::make('last_name','patient.patientUser.last_name')->searchable()->hideIf(1),
            Column::make(__('messages.case.doctor'), 'doctor.doctorUser.first_name')
                ->view('death_reports.columns.doctor_name')
                ->searchable()
                ->sortable(),
            Column::make('last_name','doctor.doctorUser.last_name')->searchable()->hideIf(1),
            Column::make(__('messages.death_report.date'), 'date')
                ->view('death_reports.columns.date')
                ->sortable(),
            Column::make(__('messages.common.action'), 'id')
                ->view('death_reports.action'),

        ];
    }

    public function builder(): Builder
    {
        $query = DeathReport::with('patient', 'doctor', 'caseFromDeathReport');

        if (getLoggedinDoctor()) {
            $doctorId = Doctor::where('user_id', getLoggedInUserId())->first();
            $query = $query->where('doctor_id', $doctorId->id);
        }

        if (getLoggedinPatient()) {
            $patientId = Patient::where('user_id', getLoggedInUserId())->first();
            $query = $query->where('patient_id', $patientId->id);
        }

        return $query;
    }
}
