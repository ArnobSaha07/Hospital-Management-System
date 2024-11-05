@if(!empty($row->net_salary))
    <p class="cur-margin text-end">{{ getCurrencyFormat($row->net_salary) }} </p>
@else
    <p class="text-end">N/A</p>
@endif
