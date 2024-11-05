@if(isset($row->transactionSubscription->subscriptionPlan))
    <p class="mb-0">{{ getAdminCurrencyFormat($row->transactionSubscription->subscriptionPlan->currency,$row->amount) }} </p>
@else
    <p class="mb-0">{{ '$'. number_format($row->amount,2)}}</p>
@endif
