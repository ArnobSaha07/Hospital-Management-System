@if ($row->payment_type == 1)
    <a data-id="{{ $row->payment_type }}"
        class="badge bg-light-primary text-decoration-none">{{ __('messages.setting.stripe') }}</a>
@elseif ($row->payment_type == 2)
    <a data-id="{{ $row->payment_type }}"
        class="badge bg-light-success text-decoration-none">{{ __('messages.setting.paypal') }}</a>
@elseif ($row->payment_type == 3)
    <a data-id="{{ $row->payment_type }}"
        class="badge bg-light-info text-decoration-none">{{ __('messages.setting.razorpay') }}</a>
@elseif ($row->payment_type == 4)
    <a data-id="{{ $row->payment_type }}" class="badge bg-light-primary text-decoration-none">Cash</a>
@elseif ($row->payment_type == 5)
    <a data-id="{{ $row->payment_type }}"
        class="badge bg-light-warning text-decoration-none">{{ __('messages.paytm') }}</a>
@elseif ($row->payment_type == 6)
    <a data-id="{{ $row->payment_type }}"
        class="badge bg-light-primary text-decoration-none">{{ __('messages.setting.paystack') }}</a>
@elseif ($row->payment_type == 7)
    <a data-id="{{ $row->payment_type }}"
        class="badge bg-light-success text-decoration-none">{{ __('messages.phonepe.phonepe') }}</a>
@elseif ($row->payment_type == 8)
    <a data-id="{{ $row->payment_type }}"
        class="badge bg-light-info text-decoration-none">{{ __('messages.flutterwave.flutterwave') }}</a>
@else
   {{__('messages.common.n/a')}}
@endif
