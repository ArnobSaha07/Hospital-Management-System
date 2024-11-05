<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\CustomField;

class CustomFieldTable extends LivewireTableComponent
{
    protected $model = CustomField::class;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'custom_fields.add_button';

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

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
            ->setDefaultSort('custom_fields.created_at', 'desc')
            ->setQueryStringStatus(false);

        $this->setThAttributes(function (Column $column) {
            if ($column->isField('id')) {
                return [
                    'class' => 'd-flex justify-content-center',
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
            Column::make(__('messages.custom_field.module_name'), 'module_name')
                ->view('custom_fields.column.module_name')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.custom_field.field_type'), 'field_type')
                ->view('custom_fields.column.field_type')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.custom_field.field_name'), 'field_name')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.custom_field.value'), 'values')
                ->view('custom_fields.column.value')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.common.action'), 'id')
                ->view('custom_fields.action'),
        ];
    }

    public function builder(): Builder
    {
        return CustomField::select('custom_fields.*');
    }
}
