@if (!empty($row->patient_unique_id))
    <span class="badge bg-light-info">
        {{ $row->patient_unique_id }}
    </span>
@else
    {{ __('messages.common.n/a') }}
@endif
