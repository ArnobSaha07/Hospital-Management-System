<div class="d-flex justify-content-end">
    @if($row->amount)
        {{ getCurrencyFormat($row->amount )}}
    @else
        {{__('messages.common.n/a')}}
    @endif    
</div>

