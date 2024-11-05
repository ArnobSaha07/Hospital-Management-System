<div class="d-flex align-items-center">
    @if (!empty($row->values))
        {{ $row->values }}
    @else
        {{ __('messages.common.n/a') }}
    @endif
</div>
