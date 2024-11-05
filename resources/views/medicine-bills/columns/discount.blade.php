@if ($row->payment_type == \App\Models\MedicineBill::MEDICINE_BILL_STRIPE)
    <span class="badge bg-light-primary">{{ __('messages.transaction_filter.stripe') }}</span>
@elseif($row->payment_type == \App\Models\MedicineBill::MEDICINE_BILL_RAZORPAY)
    <span class="badge bg-light-success">{{ __('messages.transaction_filter.razorpay') }}</span>
@elseif($row->payment_type == \App\Models\MedicineBill::MEDICINE_BILL_PAYSTACK)
    <span class="badge bg-light-success">{{ __('messages.transaction_filter.paystack') }}</span>
@elseif($row->payment_type == \App\Models\MedicineBill::MEDICINE_BILL_CASH)
    <span class="badge bg-light-info">{{ __('messages.transaction_filter.cash') }}</span>
@elseif($row->payment_type == \App\Models\MedicineBill::MEDICINE_BILL_PHONEPE)
    <span class="badge bg-light-info">{{ __('messages.phonepe.phonepe') }}</span>
@elseif($row->payment_type == \App\Models\MedicineBill::MEDICINE_BILL_CHEQUE)
    <span class="badge bg-light-info">{{ __('messages.transaction_filter.cheque') }}</span>
@elseif($row->payment_type == \App\Models\MedicineBill::MEDICINE_BILL_FLUTTERWAVE)
    <span class="badge bg-light-info">{{ __('messages.flutterwave.flutterwave') }}</span>
@endif
