<?php

namespace App\Livewire;

use App\Models\DoctorDepartment;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DoctorDepartmentTable extends LivewireTableComponent
{
    public $showButtonOnHeader = true;

    public $buttonComponent = 'doctor_departments.add-button';

    protected $model = DoctorDepartment::class;

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
            ->setDefaultSort('doctor_departments.created_at', 'desc')
            ->setQueryStringStatus(false);
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

            Column::make(__('messages.appointment.doctor_department'), 'title')
                ->view('doctor_departments.templates.columns.title')
                ->sortable()->searchable(),
            Column::make(__('messages.common.action'), 'id')->view('doctor_departments.action'),

        ];
    }

    public function builder(): Builder
    {
        return DoctorDepartment::select('doctor_departments.*');
    }
}
