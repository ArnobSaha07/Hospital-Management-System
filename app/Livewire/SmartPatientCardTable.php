<?php

namespace App\Livewire;

use App\Models\SmartPatientCard;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SmartPatientCardTable extends LivewireTableComponent
{
    public $showButtonOnHeader = true;

    public $buttonComponent = 'smart-patient-card-templates.add-button';

    protected $model = SmartPatientCard::class;

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
        ->setDefaultSort('smart_patient_cards.created_at', 'desc')
        ->setQueryStringStatus(false);;
    }

    public function placeholder()
    {
        return view('livewire.listing-skeleton');
    }

    public function columns(): array
    {
        return [
            Column::make(__('messages.lunch_break.template_name'), "template_name")
                ->sortable()
                ->searchable(),
            Column::make(__('messages.lunch_break.header_color'), "header_color")
                ->view('smart-patient-card-templates.columns.header_color')
                ->sortable(),
            Column::make(__('messages.lunch_break.show_email'), "show_email")
                ->view('smart-patient-card-templates.columns.show_email')
                ->sortable(),
            Column::make(__('messages.lunch_break.show_phone'), "show_phone")
                ->view('smart-patient-card-templates.columns.show_phone')
                ->sortable(),
            Column::make(__('messages.lunch_break.show_dob'), "show_dob")
                ->view('smart-patient-card-templates.columns.show_dob')
                ->sortable(),
            Column::make(__('messages.lunch_break.show_blood_group'), "show_blood_group")
                ->view('smart-patient-card-templates.columns.show_blood_group')
                ->sortable(),
            Column::make(__('messages.lunch_break.show_address'), "show_address")
                ->view('smart-patient-card-templates.columns.show_address')
                ->sortable(),
            Column::make(__('messages.lunch_break.show_patient_unique_id'), "show_patient_unique_id")
                ->view('smart-patient-card-templates.columns.show_patient_unique_id')
                ->sortable(),
            Column::make(__('messages.common.action'), 'id')
                ->view('smart-patient-card-templates.columns.action'),
        ];
    }

    public function builder(): Builder
    {
        $query = SmartPatientCard::select('smart_patient_cards.*');

        return $query;
    }
}
