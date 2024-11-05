<?php

namespace App\Livewire;

use App\Models\Doctor;
use App\Models\InvestigationReport;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class InvestigationTable extends LivewireTableComponent
{
    protected $model = InvestigationReport::class;

    public $showFilterOnHeader = true;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'investigation_reports.add-button';

    public $FilterComponent = ['investigation_reports.filter-button', InvestigationReport::STATUS_ARR];

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

    public function changeFilter($statusFilter)
    {
        $this->resetPage($this->getComputedPageName());
        $this->statusFilter = $statusFilter;
        $this->setBuilder($this->builder());
    }

    public function placeholder()
    {
        if(getLoggedinPatient()){
            return view('livewire.report-skeleton');
        }
        return view('livewire.listing-skeleton');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('investigation_reports.created_at', 'desc')
            ->setQueryStringStatus(false);
        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->isField('title') || $column->isField('short_name') || $column->isField('status') || $column->isField('created_at')) {
                return [
                    'class' => 'pt-5',
                ];
            }

            return [];
        });
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

    public function columns(): array
    {
        $patientColumn = [];
        if(!getLoggedinPatient()){
            $patientColumn = Column::make(__('messages.common.user_details'), 'patient.patientUser.email')
            ->view('investigation_reports.columns.patient_name')
            ->searchable(function(Builder $query, $direction){
                $query->whereHas('patient.user', function (Builder $q) use ($direction) {
                    $q->whereRaw("TRIM(CONCAT(first_name,' ',last_name,' ')) like '%{$direction}%'");
                });
            })
            ->sortable();
        }
        return [
            $patientColumn,
            Column::make(__('messages.common.user_details'), 'patient.patientUser.first_name')
                ->hideIf('patient.user.first_name')
                ->searchable()
                ->sortable(),
            Column::make('email', 'patient.patientUser.email')->searchable()->hideIf(1),
            Column::make(__('messages.investigation_report.title'), 'title')
            ->sortable(),
            Column::make(__('messages.investigation_report.date'), 'date')
                ->view('investigation_reports.columns.date')
                ->sortable(),
            Column::make(__('messages.common.status'), 'status')
                ->view('investigation_reports.columns.status')
                ->sortable(),
            Column::make(__('messages.incomes.attachment'), 'created_at')
                ->view('investigation_reports.columns.attachment'),
            Column::make(__('messages.common.action'), 'id')
                ->view('investigation_reports.action'),

        ];
    }

    public function builder(): Builder
    {
        /** @var InvestigationReport $media */
        if (! getLoggedinDoctor()) {
            $query = InvestigationReport::select('investigation_reports.*')->with('media', 'patient','doctor');
        } else {
            $doctorId = Doctor::where('user_id', getLoggedInUserId())->first();
            $query = InvestigationReport::query()->select('investigation_reports.*')->with('media', 'patient',
                'doctor')->where('doctor_id', $doctorId->id);
        }
        if(getLoggedinPatient()){
            $patientId = Patient::where('user_id', getLoggedInUserId())->first();
            $query = InvestigationReport::query()->select('investigation_reports.*')->with('media', 'patient',
                'doctor')->where('patient_id', $patientId->id);
        }
        $query->when(isset($this->statusFilter), function (Builder $q) {
            if ($this->statusFilter == 2) {
            } else {
                $q->where('investigation_reports.status', $this->statusFilter);
            }
        });

        return $query;
    }
}
