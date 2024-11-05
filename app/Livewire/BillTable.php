<?php

namespace App\Livewire;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Rappasoft\LaravelLivewireTables\Views\Column;

class BillTable extends LivewireTableComponent
{
    use WithPagination;

    protected $model = Bill::class;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'bills.add-button';

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
            ->setDefaultSort('bills.created_at', 'desc')
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

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if (! getLoggedinPatient()) {
                if ($columnIndex == '7') {
                    return [
                        'style' => 'text-align:center',
                    ];
                }
            }else{
                if ($columnIndex == '6') {
                    return [
                        'style' => 'text-align:center',
                    ];
                }
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
            $this->showButtonOnHeader = true;
            $actionButton = Column::make(__('messages.patient_diagnosis_test.action'),
                'id')->view('bills.action');
            $paymentMethod = Column::make(__('messages.subscription_plans.payment_method'), 'bill_id')
            ->view('patients.patient_bill.payment_method')->hideIf(1);
        } else {
            $this->showButtonOnHeader = false;
            $actionButton = Column::make(__('messages.patient_diagnosis_test.action'),
                'id')->view('bills.action')->hideIf(1);
                $paymentMethod = Column::make(__('messages.subscription_plans.payment_method'), 'bill_id')
            ->view('patients.patient_bill.payment_method');
        }

        return [
            Column::make(__('messages.bill.bill_id'), 'patient.patientUser.email')
                ->hideIf('patient.patientUser.email')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.bill.bill_id'), 'bill_id')
                ->view('bills.columns.bill_id')
                ->sortable()
                ->searchable(),
            $paymentMethod,
            Column::make(__('messages.bill.patient'), 'patient.patientUser.first_name')
                ->view('bills.columns.patient')
                ->searchable()
                ->sortable(),
            Column::make('last_name', 'patient.patientUser.last_name')
                ->searchable()
                ->hideIf(1),
            Column::make(__('messages.user.status'), 'status')
                ->view('patients.patient_bill.status')
                ->sortable(),
            Column::make(__('messages.bill.bill_date'), 'bill_date')
                ->view('bills.columns.bill_date')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.bill.amount'), 'amount')
                ->view('bills.columns.amount')
                ->sortable()
                ->searchable(),
            $actionButton,
        ];
    }

    public function builder(): Builder
    {
        $query = Bill::whereHas('patient.patientUser')->with(['patient.patientUser.media','manualPayment'])->select('bills.*');

        /** @var User $user */
        $user = Auth::user();
        if ($user->hasRole('Patient')) {
            $query->where('patient_id', $user->owner_id);
        }

        return $query;
    }
}
