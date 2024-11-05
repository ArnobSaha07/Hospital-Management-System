<?php

namespace App\Livewire;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ScheduleTable extends LivewireTableComponent
{
    protected $model = Schedule::class;

    public $showButtonOnHeader = true;

    public $buttonComponent = 'schedules.add-button';

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
            ->setDefaultSort('schedules.created_at', 'desc')
            ->setQueryStringStatus(false);
        $this->setThAttributes(function (Column $column) {
            return [
                'class' => 'w-0',
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
            Column::make(__('messages.case.doctor'), 'doctor.doctorUser.first_name')
                ->view('schedules.templates.columns.doctor_name')
                ->searchable(
                    function (Builder $query, $direction) {
                        return $query->whereHas('doctor.user', function (Builder $q) use ($direction) {
                            $q->whereRaw("TRIM(CONCAT(first_name,' ',last_name,' ')) like '%{$direction}%'");
                        });
                    }
                )
                ->sortable(
                    function (Builder $query, $direction) {
                        return $query->orderBy(User::select('first_name')->whereColumn('id', 'doctor.user_id'), $direction);
                    }
                ),
            Column::make(__('messages.case.doctor'), 'doctor_id')
                ->hideIf('doctor_id'),
            Column::make(__('messages.case.doctor'), 'doctor.doctorUser.email')
                ->hideIf('doctor.doctorUser.email')
                ->searchable(),
            Column::make(__('messages.schedule.per_patient_time'), 'per_patient_time')
                ->view('schedules.templates.columns.per_patient_time')
                ->sortable()
                ->searchable(),
            Column::make(__('messages.common.action'), 'id')->view('schedules.action'),
        ];
    }

    public function builder(): Builder
    {
        /** @var Schedule $query */
        $query = Schedule::with(['doctor','doctor.user']);

        /** @var User $user */
        $user = Auth::user();
        if ($user->hasRole('Doctor')) {
            $query->where('doctor_id', $user->owner_id);
        }

        return $query;
    }
}
