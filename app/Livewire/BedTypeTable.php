<?php

namespace App\Livewire;

use App\Models\BedType;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Rappasoft\LaravelLivewireTables\Views\Column;

class BedTypeTable extends LivewireTableComponent
{
    use WithPagination;

    protected $model = BedType::class;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'bed_types.add-button';

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('bed_types.created_at', 'desc');
        $this->setQueryStringStatus(false);
        $this->setThAttributes(function (Column $column) {
            if ($column->isField('title')) {
                return [
                    'width' => '90%',
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
        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($columnIndex == '1') {
                return [
                    'class' => 'text-center',
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
            Column::make(__('messages.bed.bed_type'), 'title')
                ->view('bed_types.show_route')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.common.action'), 'id')
                ->view('bed_types.action'),
        ];
    }

    public function builder(): Builder
    {
        return BedType::select('bed_types.*');
    }
}
