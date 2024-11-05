<?php

namespace App\Livewire;

use App\Models\CaseHandler;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CaseHandlerTable extends LivewireTableComponent
{
    protected $model = CaseHandler::class;

    public $showButtonOnHeader = true;

    public $showFilterOnHeader = true;

    public $buttonComponent = 'case_handlers.add-button';

    public $FilterComponent = ['case_handlers.filter-button', CaseHandler::STATUS_ARR];

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
        $this->setPrimaryKey('id')
            ->setDefaultSort('case_handlers.created_at', 'desc')
            ->setQueryStringStatus(false);
        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->isField('phone') || $column->isField('qualification') || $column->isField('dob') || $column->isField('status')) {
                return [
                    'class' => 'pt-5',
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
            Column::make(__('messages.common.user_details'), 'user.first_name')
                ->view('case_handlers.columns.name')
                ->searchable(function (Builder $query, $direction) {
                    $query->whereRaw("TRIM(CONCAT(first_name,' ',last_name,' ')) like '%{$direction}%'");
                })
                ->sortable(),
            Column::make(__('messages.common.status'), 'user.email')
                ->searchable()
                ->hideIf(1),
            Column::make(__('messages.user.phone'), 'user_id')
                ->hideIf('user_id')
                ->sortable(),
            Column::make(__('messages.user.phone'), 'user.phone')
                ->view('case_handlers.columns.phone')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.user.qualification'), 'user.qualification')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.nurse.birth_date'), 'user.dob')
                ->view('case_handlers.columns.dob')
                ->sortable(),
            Column::make(__('messages.common.status'), 'user.status')
                ->view('case_handlers.columns.status'),
            Column::make(__('messages.common.action'), 'id')
                ->view('case_handlers.action'),
        ];
    }

    public function builder(): Builder
    {
        /** @var CaseHandler $query */
        $query = CaseHandler::select('user.*')->with('user');
        $query->when(isset($this->statusFilter), function (Builder $q) {
            if ($this->statusFilter == 2) {
            } else {
                $q->where('status', $this->statusFilter);
            }
        });

        return $query;
    }
}
