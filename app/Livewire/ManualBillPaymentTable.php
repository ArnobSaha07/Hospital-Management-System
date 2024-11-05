<?php

namespace App\Livewire;

use App\Models\Bill;
use App\Models\BillTransaction;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ManualBillPaymentTable extends LivewireTableComponent
{
    use WithPagination;

    protected $model = Bill::class;

    public $showFilterOnHeader = false;

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
            ->setDefaultSort('bill_transactions.created_at', 'desc')
            ->setSortingPillsStatus(false)
            ->setQueryStringStatus(false);

        $this->setThAttributes(function (Column $column) {
            if ($column->isField('amount')) {
                return [
                    'class' => 'price-column',
                    'style' => 'padding-right:25px !important',
                ];
            }
            if ($column->isField('id')) {
                return [
                    'class' => 'text-center',
                ];
            }

            return [];
        });

        // $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
        //     if ($columnIndex == '6') {
        //         return [
        //             'style' => 'text-align:center',
        //         ];
        //     }

        //     return [];
        // });
    }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.bill.patient'), 'bill.patient.patientUser.first_name')
                ->view('bills.columns.transaction_patient')
                ->searchable(function(Builder $query, $direction) {
                    $query->whereHas('bill.patient.user', function ($q) use ($direction) {
                        $q->whereRaw("TRIM(CONCAT(first_name,' ',last_name,' ')) like '%{$direction}%'");
                    });
                })
                ->sortable(),
            Column::make(__('messages.bill.bill_id'), 'bill.patient.patientUser.email')
                ->hideIf('bill.patient.patientUser.email')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.subscription.payment_approved'), 'is_manual_payment')
                ->view('bills.columns.is_manual_payment'),
            Column::make(__('messages.user.status'), 'status')
                ->view('bills.columns.transaction_status')
                ->sortable(),
            Column::make(__('messages.subscription_plans.transaction_date'), 'created_at')
                ->view('bills.columns.transaction_date')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.bill.amount'), 'amount')
                ->view('bills.columns.amount')
                ->sortable()
                ->searchable(),
        ];
    }

    public function builder(): Builder
    {
        $query = BillTransaction::whereHas('bill.patient.patientUser')->with(['bill.patient.patientUser.media'])->select('bill_transactions.*');

        return $query;
    }
}
