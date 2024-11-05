<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\OpdPrescription;

class OpdPrescriptionTable extends LivewireTableComponent
{
    protected $model = OpdPrescription::class;

    public $opdPrescriptionId;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'opd_prescriptions.add-button';

    protected $listeners = ['refresh' => '$refresh', 'changeFilter', 'resetPage'];

    // public function resetPage($pageName = 'page')
    // {
    //     $rowsPropertyData = $this->getRows()->toArray();
    //     $prevPageNum = $rowsPropertyData['current_page'] - 1;
    //     $prevPageNum = $prevPageNum > 0 ? $prevPageNum : 1;
    //     $pageNum = count($rowsPropertyData['data']) > 0 ? $rowsPropertyData['current_page'] : $prevPageNum;

    //     $this->setPage($pageNum, $pageName);
    // }

    public function mount(int $opdPrescriptionId)
    {
        $this->opdPrescriptionId = $opdPrescriptionId;
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('opd_prescriptions.created_at', 'desc')
            ->setQueryStringStatus(false);
        $this->setThAttributes(function (Column $column) {
            if ($column->isField('id')) {
                return [
                    'class' => 'text-center',
                ];
            }
            if ($column->isField('created_at')) {
                return [
                    'width' => '80%',
                ];
            }

            return [];
        });
        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->isField('id')) {
                return [
                    'class' => 'text-center',
                ];
            }
            if ($column->isField('opd_patient_department_id') || $column->isField('created_at')) {
                return [
                    'class' => 'pt-5',
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
        if (! getLoggedinPatient()) {
            $actionButton = Column::make(__('messages.common.action'), 'id')
                ->view('opd_prescriptions.columns.action');
        } else {
            $actionButton = Column::make(__('messages.common.action'), 'id')
                ->view('opd_prescriptions.columns.action');
        }

        return [
            Column::make(__('messages.common.created_on'), 'created_at')
                ->view('opd_prescriptions.columns.created_at')
                ->sortable()
                ->searchable(),
            $actionButton,
        ];
    }

    public function builder(): Builder
    {
        return OpdPrescription::with('patient')->where('opd_patient_department_id', $this->opdPrescriptionId)
            ->select('opd_prescriptions.*');
    }
}
