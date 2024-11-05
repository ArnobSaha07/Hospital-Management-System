@extends('layouts.app')
@section('title')
    {{ __('messages.appointment.new_appointment') }}
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-7">
            <h1 class="mb-3">@yield('title')</h1>
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            <div class="row">
                <div class="col-12">
                    @include('layouts.errors')
                    <div class="alert alert-danger hide" id="createAppointmentErrorsBox"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-12">
                    {{ Form::open(['id' => 'appointmentForm']) }}

                    @include('appointments.fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
        @include('appointments.templates.appointment_slot')
        {{ Form::hidden('doctorDepartmentUrl', url('doctors-list'), ['class' => 'doctorDepartmentUrl']) }}
        {{ Form::hidden('doctorScheduleList', url('doctor-schedule-list'), ['class' => 'doctorScheduleList']) }}
        {{ Form::hidden('appointmentSaveUrl', route('appointments.store'), ['id' => 'saveAppointmentURLID']) }}
        {{ Form::hidden('appointmentIndexPage', route('appointments.index'), ['class' => 'appointmentIndexPage']) }}
        {{ Form::hidden('isEdit', false, ['class' => 'isEdit']) }}
        {{ Form::hidden('isCreate', true, ['class' => 'isCreate']) }}
        {{ Form::hidden('getBookingSlot', route('get.booking.slot'), ['class' => 'getBookingSlot']) }}
        {{ Form::hidden('stripeConfigKey', $stripeKey, ['id' => 'stripeConfigKey']) }}
        {{-- {{ Form::hidden('webRazorpayDataKey', getSelectedPaymentGateway('razorpay_key'), ['class' => 'webRazorpayDataKey']) }} --}}
        {{-- {{ Form::hidden('webRazorpayDataName', getAppName(), ['class' => 'webRazorpayDataName']) }} --}}
        {{-- {{ Form::hidden('webRazorpayDataImage', asset(getLogoUrl()), ['class' => 'webRazorpayDataImage']) }} --}}
        {{ Form::hidden('webRazorpayDataCallBackURL', route('appointment.razorpay.success'), ['class' => 'razorpayDataCallBackURL']) }}
        {{ Form::hidden('webRazorpayPaymentFailed', route('patient.razorpay.failed'), ['class' => 'webPatientRazorpayPaymentFailed']) }}
    </div>
@endsection
@section('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        let options = {
            'key': "{{ getSelectedPaymentGateway('razorpay_key') }}",
            'amount': 0, //  100 refers to 1
            'currency': 'INR',
            'name': "{{ getAppName() }}",
            'order_id': '',
            'description': '',
            'image': "{{ asset(getLogoUrl()) }}", // logo here
            'callback_url': "{{ route('appointment.razorpay.success') }}",
            'prefill': {
                'appointment_id': '',
                'amount': '',
                'payment_type': '',
            },
            'readonly': {
                'appointment_id': 'true',
                'amount': 'true',
                'payment_type': 'true',
            },
            'modal': {
                'ondismiss': function() {
                    $.ajax({
                        type: 'POST',
                        url: route('appointment.razorpay.failed'),
                        data: $("#appointmentForm").serialize(),
                        success: function(result) {
                            if (result.success) {
                                console.log(result);
                                displayErrorMessage(result.message);
                                setTimeout(function () {
                                    window.location.href = $(".appointmentIndexPage").val();
                                }, 2000);
                            }
                        },
                        error: function(result) {
                            displayErrorMessage(result.responseJSON.message)
                        },
                    });
                },
            },
        }
        // console.log(options);
    </script>
@endsection
{{-- let doctorDepartmentUrl = "{{ url('doctors-list') }}"; --}}
{{-- let doctorScheduleList = "{{ url('doctor-schedule-list') }}"; --}}
{{-- let appointmentSaveUrl = "{{ route('appointments.store') }}"; --}}
{{--        let appointmentIndexPage = "{{ route('appointments.index') }}"; --}}
{{-- //         let isEdit = false; --}}
{{-- //         let isCreate = true; --}}
{{--        let getBookingSlot = "{{ route('get.booking.slot') }}"; --}}
{{--    <script src="{{ asset('backend/js/moment-round/moment-round.js') }}"></script> --}}
{{--    <script src="{{mix('assets/js/appointments/create-edit.js')}}"></script> --}}
