<div class="d-flex justify-content-end">
@if(!empty($row->total))
        <p class="cur-margin">  {{ getCurrencyFormat($row->total) }}</p>
@else
        N/A
@endif
</div>
