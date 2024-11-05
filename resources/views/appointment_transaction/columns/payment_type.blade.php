@if ($row->appointment->payment_type == \App\Models\Appointment::TYPE_STRIPE)
    <span class="badge bg-light-primary">{{ __('messages.setting.stripe') }}</span>
@elseif($row->appointment->payment_type == \App\Models\Appointment::TYPE_RAZORPAY)
    <span class="badge bg-light-success">{{ __('messages.setting.razorpay') }}</span>
@elseif ($row->appointment->payment_type == \App\Models\Appointment::TYPE_PAYPAL)
    <span class="badge bg-light-primary">{{ __('messages.setting.paypal') }}</span>
@elseif($row->appointment->payment_type == \App\Models\Appointment::TYPE_CASH)
    <span class="badge bg-light-info">{{ __('messages.transaction_filter.cash') }}</span>
@elseif($row->appointment->payment_type == \App\Models\Appointment::FLUTTERWAVE)
    <span class="badge bg-light-info">{{ __('messages.flutterwave.flutterwave') }}</span>
@elseif($row->appointment->payment_type == \App\Models\Appointment::CHEQUE)
    <span class="badge bg-light-warning">{{ __('messages.cheque') }}</span>
@elseif($row->appointment->payment_type == \App\Models\Appointment::PAYSTACK)
    <span class="badge bg-light-warning">{{ __('messages.setting.paystack') }}</span>
@elseif($row->appointment->payment_type == \App\Models\Appointment::PHONEPE)
    <span class="badge bg-light-success">{{ __('messages.phonepe.phonepe') }}</span>
@else
    {{__('messages.common.n/a')}}
@endif
