<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DocumentTable extends LivewireTableComponent
{
    use WithPagination;

    protected $model = Document::class;

    public $showButtonOnHeader = true;

    public $showFilterOnHeader = false;

    public $buttonComponent = 'documents.add-button';

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setDefaultSort('documents.created_at', 'desc');
        $this->setQueryStringStatus(false);
        $this->setThAttributes(function (Column $column) {
            if ($column->isField('created_at')) {
                return [
                    'class' => 'text-center',
                    'style' => 'padding-right:20px !important',
                ];
            }
            return [
                'class' => 'w-50',
            ];
        });

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->isField('id') || $column->isField('name')) {
                return [
                    'class' => 'p-5',
                ];
            }
            if ($column->isField('created_at')) {
                return [
                    'style' => 'padding-right:20px !important',
                ];
            }

            return [];
        });
    }

    // public function resetPage($pageName = 'page')
    // {
    //     $rowsPropertyData = $this->getRows()->toArray();
    //     $prevPageNum = $rowsPropertyData['current_page'] - 1;
    //     $prevPageNum = $prevPageNum > 0 ? $prevPageNum : 1;
    //     $pageNum = count($rowsPropertyData['data']) > 0 ? $rowsPropertyData['current_page'] : $prevPageNum;

    //     $this->setPage($pageNum, $pageName);
    // }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.file_name'), 'id')
                ->view('documents.columns.file_name'),
            Column::make(__('messages.document.document_type'), 'documentType.name')
                ->view('documents.columns.document_type')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.document.patient'), 'patient.patientUser.first_name')
                ->view('documents.columns.patient')
                ->searchable()
                ->sortable(),
            Column::make('last_name', 'patient.patientUser.last_name')->searchable()->hideIf(1),
            Column::make('email', 'patient.patientUser.email')->searchable()->hideIf(1),
            Column::make(__('messages.common.action'), 'created_at')
                ->view('documents.action-button'),
        ];
    }

    public function builder(): Builder
    {
        if (! getLoggedinPatient()) {
            $query = Document::whereHas('patient.patientUser')->with('documentType', 'patient.patientUser','media')->select('documents.*');
        } else {
            $patientId = Patient::where('user_id', getLoggedInUserId())->first();
            $query = Document::whereHas('patient.patientUser')->with('documentType', 'patient.patientUser',
                'media')->select('documents.*')->where('patient_id', $patientId->id);
        }

        return $query;
    }
}
