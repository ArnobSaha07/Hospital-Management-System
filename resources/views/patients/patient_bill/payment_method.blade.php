@php
    $manualPayment = App\Models\BillTransaction::where('bill_id', $row->id)
        ->latest()
        ->first();
@endphp

@if ($row->status == 0 || empty($row->status))
    {{ Form::select('payment_type', getBillPaymentType(), null, ['class' => 'form-select make-bill-payment select2Selector','data-id' => $row->id, 'data-control' => 'select2', 'required','placeholder'=> __('messages.ipd_payments.payment_mode')]) }}
@else
    <span class="badge bg-light-primary">{{ App\Models\BillTransaction::PAYMENT_TYPES[$manualPayment->payment_type] }}</span>
@endif
