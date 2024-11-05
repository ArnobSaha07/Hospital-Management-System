@if ($row->payment_type == \App\Models\PurchaseMedicine::PURCHASE_MEDICINE_STRIPE)
    <span class="badge bg-light-primary">{{ __('messages.transaction_filter.stripe') }}</span>
@elseif($row->payment_type == \App\Models\PurchaseMedicine::PURCHASE_MEDICINE_RAZORPAY)
    <span class="badge bg-light-success">{{ __('messages.transaction_filter.razorpay') }}</span>
@elseif($row->payment_type == \App\Models\PurchaseMedicine::PURCHASE_MEDICINE_PAYSTACK)
    <span class="badge bg-light-success">{{ __('messages.transaction_filter.paystack') }}</span>
@elseif($row->payment_type == \App\Models\PurchaseMedicine::PURCHASE_MEDICINE_CASH)
    <span class="badge bg-light-info">{{ __('messages.transaction_filter.cash') }}</span>
@elseif($row->payment_type == \App\Models\PurchaseMedicine::PURCHASE_MEDICINE_PHONEPE)
    <span class="badge bg-light-primary">{{ __('messages.phonepe.phonepe') }}</span>
@elseif($row->payment_type == \App\Models\PurchaseMedicine::PURCHASE_MEDICINE_CHEQUE)
    <span class="badge bg-light-info">{{ __('messages.transaction_filter.cheque') }}</span>
@elseif($row->payment_type == \App\Models\PurchaseMedicine::PURCHASE_MEDICINE_FLUTTERWAVE)
    <span class="badge bg-light-info">{{ __('messages.flutterwave.flutterwave') }}</span>
@endif
