<div class="d-flex align-items-center justify-content-end mt-2">
    @if(!empty($row->amount))
        <p class="cur-margin"> {{ getCurrencyFormat($row->amount) }}</p>
    @else
       {{__('messages.common.n/a')}}
    @endif
</div>

