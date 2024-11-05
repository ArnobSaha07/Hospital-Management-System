@if(isset($row->price))
    <p class="mb-0">{{ getAdminCurrencyFormat($row->currency,$row->price) }} </p>
@else
    N/A
@endif

 
