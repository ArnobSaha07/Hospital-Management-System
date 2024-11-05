<?php

namespace App\Livewire;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AdminSubscriptionTable extends LivewireTableComponent
{
    protected $model = Subscription::class;

    public $showFilterOnHeader = true;

    public $FilterComponent = ['subscription.columns.filter', Subscription::STATUS_ARR, Subscription::PLAN_EXPIRE_ARR];

    public $showButtonOnHeader = false;

    public $paginationIsEnabled = true;

    protected $listeners = ['refresh' => '$refresh', 'changeFilter', 'resetPage', 'changeExpireFilter'];

    public $statusFilter;

    public $expireStatusFilter;

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
        ->setDefaultSort('subscriptions.created_at', 'desc')
        ->setQueryStringStatus(false);

        $this->setThAttributes(function (Column $column) {
            if ($column->isField('plan_amount')) {
                return [
                    'class' => 'price-column',
                ];
            }
            if ($column->isField('starts_at')) {
                return [
                    'class' => 'price-sec-column',
                ];
            }
            if ($column->isField('ends_at')) {
                return [
                    'class' => 'price-sec-column',
                ];
            }

            return [];
        });

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($columnIndex == '2') {
                return [
                    'style' => 'text-align:right',
                ];
            }
            if ($columnIndex == '3') {
                return [
                    'style' => 'text-align:center',
                ];
            }
            if ($columnIndex == '4') {
                return [
                    'style' => 'text-align:center',
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

    public function changeExpireFilter($expireStatusFilter)
    {
        $this->resetPage($this->getComputedPageName());
        $this->expireStatusFilter = $expireStatusFilter;
        $this->setBuilder($this->builder());
    }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }
    
    public function columns(): array
    {
        return [
            Column::make(__('messages.hospitals_list.hospital_name'), 'user.first_name')
                ->sortable()
                ->searchable(fn (Builder $query, $searchTerm) => $query->with('user')->whereHas('user', function (Builder $q) use ($searchTerm) {
                    $q->where('first_name', $searchTerm)
                        ->orWhere('first_name', 'like', '%'.$searchTerm.'%');
                }))
                ->view('subscription.columns.hospital_name'),
            Column::make(__('messages.subscription_plans.plan_name'), 'subscription_plan_id')
                ->view('subscription.columns.plan_name'),
            Column::make(__('messages.subscription_plans.amount'), 'plan_amount')
                ->sortable()
                ->searchable()
                ->view('subscription.columns.plan_amount'),
            Column::make(__('messages.subscription_plans.start_date'), 'starts_at')
                ->sortable()
                ->searchable()
                ->view('subscription.columns.start_date'),
            Column::make(__('messages.subscription_plans.end_date'), 'ends_at')
                ->sortable()
                ->searchable()
                ->view('subscription.columns.end_date'),
            Column::make(__('messages.subscription_plans.frequency'), 'plan_frequency')
                ->searchable()
                ->view('subscription.columns.frequency'),
            Column::make(__('messages.common.status'), 'status')
                ->view('subscription.columns.status'),
            Column::make(__('messages.common.action'), 'id')
                ->view('subscription.columns.action'),
        ];
    }

    public function builder(): Builder
    {
        $now = Carbon::now();
        $query = Subscription::with(['subscriptionPlan', 'user'])
            ->select('subscriptions.*');

        $query->when(isset($this->statusFilter), function (Builder $q) {
            if($this->statusFilter == ""){}
            if($this->statusFilter == 0){
                $q->where('subscriptions.status', Subscription::INACTIVE);
            }
            if($this->statusFilter == 1){
                $q->where('subscriptions.status', Subscription::ACTIVE);
            }
        });
        $query->when(isset($this->expireStatusFilter), function (Builder $q) use ($now){
            if($this->expireStatusFilter == ""){}
            if($this->expireStatusFilter == 0){
                $q->where('ends_at','<',$now);
            }
            if($this->expireStatusFilter == 1){
                $q->where('ends_at','>',$now);
            }
        });

        return $query;
    }
}
