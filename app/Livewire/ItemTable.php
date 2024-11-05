<?php

namespace App\Livewire;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ItemTable extends LivewireTableComponent
{
    public $showButtonOnHeader = true;

    public $buttonComponent = 'items.add-button';

    protected $model = Item::class;

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
            ->setDefaultSort('items.created_at', 'desc')
            ->setQueryStringStatus(false);
        $this->setThAttributes(function (Column $column) {
            if ($column->isField('id')) {
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
            if ($column->isField('name') || $column->isField('unit') || $column->isField('available_quantity')) {
                return [
                    'class' => 'p-5',
                ];
            }
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
        return [

            Column::make(__('messages.item.name'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.item.item_category'), 'itemcategory.name')
                ->searchable()
                ->sortable(),
            Column::make(__('messages.item.unit'), 'unit')
                ->view('items.columns.unit')
                ->sortable(),
            Column::make(__('messages.item.available_quantity'), 'available_quantity')
                ->view('items.columns.available_quantity')
                ->sortable(),
            Column::make(__('messages.common.action'), 'id')
                ->view('items.action'),

        ];
    }

    public function builder(): Builder
    {
        /** @var Item $query */
        return Item::with('itemcategory')->select('items.*');
    }
}
