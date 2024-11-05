<div class="d-flex align-items-center mt-2">
    @if ($row->is_discharge == 1)
        <span class="badge bg-light-success">{{ __('messages.ipd_patient.discharged') }}</span>
    @else
        <span class="badge bg-light-danger">{{ __('messages.lunch_break.not_dischared') }}</span>
    @endif
</div>
