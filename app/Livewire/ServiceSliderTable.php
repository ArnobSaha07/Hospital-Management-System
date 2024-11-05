<?php

namespace App\Livewire;

use App\Models\ServiceSlider;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ServiceSliderTable extends LivewireTableComponent
{
    protected $model = ServiceSlider::class;

    public $showFilterOnHeader = false;

    public $showButtonOnHeader = true;

    public $paginationIsEnabled = true;

    public $buttonComponent = 'landing.service_slider.add-button';

    protected $listeners = ['refresh' => '$refresh', 'resetPage'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setDefaultSort('service_sliders.updated_at', 'desc')
            ->setQueryStringStatus(false)
            ->setSearchVisibilityDisabled();

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($columnIndex == '1') {
                return [
                    'class' => 'text-center',
                    'width' => '8%',
                ];
            }

            return [];
        });
    }

    public function placeholder()
    {
        return view('livewire.skeleton');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.landing_cms.image'), 'id')
                ->view('landing.service_slider.columns.image'),
            Column::make(__('messages.common.action'), 'id')
                ->view('landing.service_slider.columns.action'),
        ];
    }

    public function builder(): Builder
    {
        return ServiceSlider::select('service_sliders.*');
    }
}
