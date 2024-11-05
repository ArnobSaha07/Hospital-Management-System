<div class="d-flex justify-content-end me-5">
    @if(!empty($row->buying_price))
        <p class="cur-margin">{{ getCurrencyFormat($row->buying_price) }} </p>
    @else
    {{ __('messages.common.n/a') }}
    @endif    
</div>

