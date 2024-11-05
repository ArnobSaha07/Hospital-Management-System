<?php

namespace App\Livewire;

use App\Models\HospitalType;
use Rappasoft\LaravelLivewireTables\Views\Column;

class HospitalTypeTable extends LivewireTableComponent
{
    protected $model = HospitalType::class;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'hospital_type.add-button';

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc')
            ->setQueryStringStatus(false);

        $this->setThAttributes(function (Column $column) {
            if ($column->isField('id')) {
                return [
                    'class' => 'd-flex align-items-center me-5',
                ];
            }

            return [
                'class' => 'w-100',
            ];
        });
    }
    
    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.user.name'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.common.action'), 'id')
                ->view('hospital_type.action'),
        ];
    }
}
