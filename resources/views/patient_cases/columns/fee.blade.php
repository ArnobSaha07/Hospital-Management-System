<div class="d-flex align-items-center justify-content-end mt-3">
    @if(!empty($row->fee))
        <p class="cur-margin">{{ getCurrencyFormat($row->fee) }} </p>
    @else
        N/A
    @endif
</div>

