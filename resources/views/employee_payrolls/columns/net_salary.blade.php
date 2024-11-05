@if(!empty($row->net_salary))
    <p class="cur-margin mt-3">{{ getCurrencyFormat($row->net_salary) }}</p>
@else
    N/A
@endif
