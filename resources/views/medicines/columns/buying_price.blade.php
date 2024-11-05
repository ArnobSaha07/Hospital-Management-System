<div class="d-flex justify-content-end">
@if($row->buying_price)
        <p class="cur-margin"> {{getCurrencyFormat($row->buying_price)}}
@else
        {{__('messages.common.n/a')}}
@endif
</div>
