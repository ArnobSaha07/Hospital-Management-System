<div class="d-flex">
    @if(!empty($row->standard_charge))
        <span>{{ getCurrencyFormat($row->standard_charge) }}</span>
    @else
        {{ __('messages.common.n/a') }}
    @endif
</div>


