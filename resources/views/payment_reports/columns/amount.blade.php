<div class="mt-2">
    @if(!empty($row->amount))
        <p class="cur-margin text-end me-5">{{ getCurrencyFormat($row->amount) }}</p>
    @else
        N/A
    @endif
</div>

