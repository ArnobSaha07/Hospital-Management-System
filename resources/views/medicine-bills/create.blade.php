@extends('layouts.app')
@section('title')
    {{ __('messages.medicine_bills.add_medicine_bill') }}
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-7">
            <h1 class="mb-0">@yield('title')</h1>
            <a href="javascript:void(0)" class="btn btn-primary me-3 ms-auto add-patient-modal">{{ __('messages.new_patient') }}</a>
            <a href="{{ route('medicine-bills.index') }}"
               class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="row">
                <div class="col-12">
                    @include('layouts.errors')
                    @include('flash::message')
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    {{Form::hidden('uniqueId',2,['id'=>'medicineUniqueId'])}}
                    {{Form::hidden('associateMedicines',json_encode($medicineList),['class'=>'associatePurchaseMedicines'])}}
                    {{Form::hidden('medicineCategories',json_encode($medicineCategoriesList),['id'=>'showMedicineCategoriesMedicineBill'])}}

                    {{ Form::open(['id' => 'CreateMedicineBillForm']) }}
                    @include('medicine-bills.medicine-table')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    @include('medicine-bills.templates.templates')
    @include('medicine-bills.add_patient_modal')
    {{--  {{Form::hidden('billSaveUrl',route('bills.store'),['id'=>'createBillSaveUrl','class'=>'billSaveUrl'])}}
    {{Form::hidden('billUrl',route('bills.index'),['id'=>'createBillUrl','class'=>'billUrl'])}}
    {{Form::hidden('associateMedicines',json_encode($associateMedicines),['id'=>'createBillAssociateMedicines','class'=>'associateMedicines'])}}
    {{Form::hidden('uniqueId',2,['id'=>'createBillUniqueId','class'=>'uniqueId'])}}
    {{Form::hidden('patientAdmissionDetailUrl',url('patient-admission-details'),['id'=>'createBillPatientAdmissionDetailUrl','class'=>'patientAdmissionDetailUrl'])}}
    {{Form::hidden('isCreate',true,['id'=>'createBillIsCreate','class'=>'isCreate'])}}
    {{Form::hidden('isEdit',false,['id'=>'createBillIsEdit','class'=>'isEdit'])}}  --}}
@endsection
    {{--   assets/js/moment.min.js  --}}
    {{--    assets/js/bills/new.js --}}
    {{--    assets/js/custom/input_price_format.js --}}
@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    let options = {
        'key': "{{ getPaymentCredentials('razorpay_key') }}",
        'amount': 0,
        'currency': "{{ strtoupper(getCurrentCurrency()) }}",
        'name': "{{ getAppName() }}",
        'order_id': '',
        'description': '',
        'image': "{{ asset(getLogoUrl()) }}",
        'callback_url': "{{ route('medicine.bill.razorpay.success') }}",
        'prefill': {
            'billID': '',
        },
        'theme': {
            'color': '#FF8E4B',
        },
        'modal': {
            'ondismiss': function() {
                $.ajax({
                    type: 'POST',
                    url: route('medicine.bill.razorpay.failed'),
                    data: $("#CreateMedicineBillForm").serialize(),
                    success: function(result) {
                        if (result.success) {
                            displayErrorMessage(result.message);
                            setTimeout(function() {
                                window.location.href = route('medicine-bills.index');
                            }, 1500)
                        }
                    },
                    error: function(result) {
                        displayErrorMessage(result.responseJSON.message)
                    },
                });
            },
        }
    }

    let stripe = '';
    @if (getPaymentCredentials('stripe_key'))
        stripe = Stripe("{{ getPaymentCredentials('stripe_key') }}");
    @endif
</script>
@endsection
