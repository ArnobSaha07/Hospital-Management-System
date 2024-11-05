<?php

namespace App\Livewire;

use App\Models\CallLog;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CallLogTable extends LivewireTableComponent
{
    protected $model = CallLog::class;

    public $showButtonOnHeader = true;

    public $showFilterOnHeader = true;

    public $paginationIsEnabled = true;

    public $buttonComponent = 'call_logs.add-button';

    public $FilterComponent = ['call_logs.filter-button', CallLog::CALLTYPE_ARR];

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
            ->setDefaultSort('call_logs.created_at', 'desc')
            ->setQueryStringStatus(false);
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
            Column::make(__('messages.call_log.name'), 'name')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.call_log.phone'), 'phone')
                ->view('call_logs.columns.phone')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.call_log.received_on'), 'date')
                ->view('call_logs.columns.date')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.call_log.follow_up_date'), 'follow_up_date')
                ->view('call_logs.columns.follow_up_date')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.call_log.call_type'), 'call_type')
                ->view('call_logs.columns.call_type')
                ->sortable(),
            Column::make(__('messages.common.action'), 'id')
                ->view('call_logs.action'),
        ];
    }

    public function builder(): Builder
    {
        /** @var CallLog $query */
        $query = CallLog::select('call_logs.*');

        $query->when(isset($this->statusFilter), function (Builder $q) {
            if ($this->statusFilter == 0) {
            } else {
                $q->where('call_type', $this->statusFilter);
            }
        });

        return $query;
    }
}
