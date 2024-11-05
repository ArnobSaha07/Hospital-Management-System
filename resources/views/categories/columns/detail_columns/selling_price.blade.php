<div class="d-flex justify-content-end">
    @if(!empty($row->selling_price))
        <p class="cur-margin">{{ getCurrencyFormat($row->selling_price) }} </p>
    @else
    {{ __('messages.common.n/a') }}
    @endif    
</div>

