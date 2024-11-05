@extends('layouts.auth_app')
@section('title')
    {{ __('messages.paytm') }}
@endsection
@section('content')
    <div class="d-flex flex-column flex-column-fluid align-items-center justify-content-center p-4">
        <div class="bg-white rounded-15 shadow-md px-5 px-sm-7 py-10 mx-auto" style="width:500px">
            <h1 class="text-center mb-7">{{ __('messages.payment.payment_details') }}</h1>
            <form action="{{ route('patient.make.payment') }}" method="POST"
                  enctype="multipart/form-data">
                {!! csrf_field() !!}

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <span>{{$errors->first()}}</span>
                    </div>
                @endif
                @if(session('success_msg'))
                    <div class="alert alert-success fade in alert-dismissible show">
                        {{ session('success_msg') }}
                    </div>
                @endif
                @if(session('error_msg'))
                    <div class="alert alert-danger fade in alert-dismissible show">
                        {{ session('error_msg') }}
                    </div>
                @endif
                <input type="hidden" name="ipdNumber" value="{{$ipdNumber}}">
                <input type="hidden" name="amount" value="{{$amount}}">

                <div class="mb-sm-7 mb-4">
                    <label for="email" class="form-label">
                        {{ __('messages.death_report.patient_name') }}:<span class="required"></span>
                    </label>
                    <input type="text" class="form-control" name="name"
                           value="{{ getLoggedInUser()->full_name }}" placeholder="Enter name" required
                           readonly>
                </div>

                <div class="mb-sm-7 mb-4">
                    <label for="email" class="form-label">
                        {{ __('messages.user.email') }}:<span class="required"></span>
                    </label>

                    <input type="email" class="form-control" name="email"
                           value="{{ getLoggedInUser()->email }}" placeholder="Enter email" required
                           readonly>
                </div>

                <div class="mb-sm-7 mb-4">
                    <label for="email" class="form-label">
                        {{ __('messages.user.phone') }}:<span class="required"></span>
                    </label>
                    <input type="text" class="form-control" name="mobile"
                           value=""
                           placeholder="Mobile No" required>
                </div>

                <h6>  {{ __('messages.dashboard.total_payments') }} : {{$amount}} Rs/-</h6>

                <div class="d-inline-flex mt-4 float-end">
                    <a href="{{route('patient.paytm.failed')}}">
                        <button type="button" class="btn btn-light me-5">
                            {{ __('messages.common.cancel') }}
                        </button>
                    </a>
                    <button type="submit" class="btn btn-primary ">{{ __('messages.payment.payment') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
