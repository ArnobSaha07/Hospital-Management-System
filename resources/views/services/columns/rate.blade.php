<div class="d-flex justify-content-end">
@if($row->rate)
        <p class="cur-margin">{{ getCurrencyFormat($row->rate) }}</p>
@else
        N/A
@endif
</div>
