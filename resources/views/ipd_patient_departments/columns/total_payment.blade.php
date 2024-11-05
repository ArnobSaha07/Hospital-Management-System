<div class="d-flex justify-content-end pe-25">
    @if(!empty($row->bill->total_payments))
        {{ getCurrencyFormat($row->bill->total_payments) }}
    @else
        {{ getCurrencyFormat(0) }}
    @endif
</div>
