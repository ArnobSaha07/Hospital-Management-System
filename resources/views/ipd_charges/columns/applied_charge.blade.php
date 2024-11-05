<div class="d-flex">
    @if(!empty($row->applied_charge))
        <span>{{ getCurrencyFormat($row->applied_charge) }}</span>
    @else
        {{ __('messages.common.n/a') }}
    @endif
</div>

