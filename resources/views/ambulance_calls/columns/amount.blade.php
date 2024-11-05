<div class="d-flex justify-content-end">
@if($row->amount)
        <p class="mt-3">{{ getCurrencyFormat($row->amount) }}</p>
@else
       {{__('messages.common.n/a')}}
@endif
</div>
