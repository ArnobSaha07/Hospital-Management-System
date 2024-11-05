<?php

namespace App\Livewire;

use App\Models\Medicine;
use App\Models\MedicineBill;
use App\Models\SaleMedicine;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UsedMedicineTable extends LivewireTableComponent
{
    use WithPagination;

    public $showFilterOnHeader = false;

    public $showButtonOnHeader = false;

    protected $model = MedicineBill::class;

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('sale_medicines.created_at', 'desc')
            ->setQueryStringStatus(false);
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
            Column::make('Id', 'medicine_bill_id')
                ->sortable()->hideIf(1),
            Column::make(__('messages.medicines'), 'medicine_id')
                ->sortable(function(Builder $query, $direction) {
                    return $query->orderBy(Medicine::select('name')->whereColumn('id', 'sale_medicines.medicine_id'), $direction);
                })->searchable(
                    function (Builder $query, $direction) {
                        return $query->whereHas('medicine', function (Builder $q) use ($direction) {
                            $q->whereRaw("name like '%{$direction}%'");
                        });
                    }
                )->view('used-medicine.columns.medicine'),
            Column::make(__('messages.used_medicine.used_quantity'), 'sale_quantity')
                ->sortable()->searchable()->view('used-medicine.columns.quantity'),
            Column::make('Model id', 'medicineBill.model_id')
                ->sortable()->hideIf(1),
            Column::make(__('messages.used_medicine.used_at'), 'medicineBill.model_type')
                ->sortable()->searchable()->view('used-medicine.columns.used_at'),
            Column::make(__('messages.investigation_report.date'), 'created_at')
                ->sortable()->searchable()->view('used-medicine.columns.date'),

        ];
    }

    public function builder(): Builder
    {
        return SaleMedicine::with('medicineBill')->whereHas('medicineBill', function (Builder $q) {
            $q->where('payment_status', true);
        });
    }
}
