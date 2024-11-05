<?php

namespace App\Livewire;

use Livewire\WithPagination;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;

class PatientCardTable extends LivewireTableComponent
{
    use WithPagination;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'smart-patient-cards.add-button';

    protected $model = Patient::class;

    protected $listeners = ['refresh' => '$refresh','resetPage'];

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
            ->setDefaultSort('patients.created_at', 'desc')
            ->setQueryStringStatus(false);
            $this->setThAttributes(function (Column $column) {
                if ($column->isField('id')) {
                    return [
                        'class' => 'text-center',
                        'style' => 'padding-right:20px !important',
                    ];
                }
                return [];
            });
        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->isField('id')) {
                return [
                    'class' => 'text-center',
                    'style' => 'padding-right:20px !important',
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
        if(!getLoggedinPatient()){
            $patient = Column::make(__('messages.patients'), 'patientUser.first_name')
            ->view('patients.columns.patient')
            ->sortable()->searchable(function (Builder $query, $direction){
                $query->whereHas('patientUser', function (Builder $q) use ($direction) {
                    $q->whereRaw("TRIM(CONCAT(first_name,' ',last_name,' ')) like '%{$direction}%'");
                });
            });
        }else{
            $patient = Column::make(__('messages.patients'), 'patientUser.first_name')
            ->hideIf(1);
        }
        return [
            $patient,
            Column::make('email', 'patientUser.email')
                ->searchable()
                ->hideIf(1),
            Column::make(__('messages.lunch_break.patient_unique_id'), 'patient_unique_id')
                ->sortable()->searchable()
                ->view('smart-patient-cards.columns.patient-unique-id'),
            Column::make(__('messages.lunch_break.template_name'), 'SmartCardTemplate.template_name')
                ->sortable()->searchable()
                ->view('smart-patient-cards.columns.template_name'),
            Column::make(__('messages.common.action'), 'id')
                ->view('smart-patient-cards.columns.action'),
        ];
    }

    public function builder(): Builder
    {
        $query = Patient::with(['patientUser.media','SmartCardTemplate'])->whereNotNull('template_id')->whereHas('patientUser')->select('patients.*');

        if(getLoggedinPatient()){
            $query = $query->where('user_id', getLoggedInUserId());
        }

        return $query;
    }
}
