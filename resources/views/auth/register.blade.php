<?php
$style = 'style=background-image:url('.asset('assets/img/progress-hd.png').')';
$settingValue = getSuperAdminSettingValue();
App::setLocale(session('languageName'));
?>
@extends('layouts.auth_app')

@section('title')
    {{ __('auth.registration.registration') }}
@endsection
@section('css')
    <link href="{{ asset('backend/css/fonts.css') }}" rel="stylesheet" type="text/css"/>
   {{-- <link rel="stylesheet" href="{{ asset('node_modules/intl-tel-input/build/css/intlTelInput.css') }}"> --}}
@endsection
@section('content')
    <!--begin::Authentication - Sign-up -->

    <ul class="nav nav-pills language-option" style="justify-content: flex-end; cursor: pointer">
        <li class="nav-item dropdown me-5">
            <a class="btn btn-primary w-150px mb-5 indicator m-3 dropdown-toggle"
               data-bs-toggle="dropdown" href="javascript:void(0)" role="button"
               aria-expanded="false">{{ getCurrentLanguageName() }}</a>
            <ul class="dropdown-menu w-150px">
                @foreach(getLanguages() as $key => $value)
                    <li class="{{(checkLanguageSession() == $key) ? 'active' : '' }}"><a
                                class="dropdown-item  px-5 language-select {{(checkLanguageSession() == $key) ? 'bg-primary text-white' : 'text-dark' }}"
                                data-id="{{$key}}">{{$value}}</a>
                    </li>
                @endforeach
            </ul>
        </li>
    </ul>

    <div class="d-flex flex-column flex-column-fluid align-items-center justify-content-center p-4">
        <div class="col-12 text-center">
            <a href="{{ route('landing-home') }}" data-turbo="false" class="image mb-7 mb-sm-10">
                <img alt="Logo" src="{{ asset($settingValue['app_logo']['value']) }}" class="img-fluid logo-fix-size">
            </a>
        </div>
        <div class="bg-theme-white rounded-15 shadow-md width-540 px-5 px-sm-7 py-10 mx-auto">
            @include('flash::message')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h1 class="text-center mb-7">{{__('auth.registration.hospital_registration')}}</h1>
            <form method="post" action="{{ url('/register') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-sm-7 mb-4">
                        <label for="formInputName" class="form-label">{{__('auth.hospital_name')}}
                            <span class="required"></span>
                        </label>
                        <input type="text" class="form-control" id="formInputName"
                               name="hospital_name" value="{{ old('hospital_name') }}"
                               placeholder="{{__('auth.registration.enter_hospital_name')}}" pattern="^[a-zA-Z0-9 ]+$"
                               title="Hospital Name Not Allowed Special Character" required>
                    </div>
                    <div class="col-md-6 mb-sm-7 mb-4">
                        <label class="form-label" for="hospitalSlug">{{__('auth.hospital_slug')}}
                            <span class="required"></span>
                        </label>
                        <input type="text" class="form-control" id="hospitalSlug"
                               name="username" value="{{ old('username') }}"
                               placeholder="{{__('auth.registration.enter_username')}}" pattern="^\S[a-zA-Z0-9]+$"
                               title="Hospital Slug must be alphanumeric and having exact 12 characters in length"
                               required
                               min="12" maxlength="12">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-sm-7 mb-4">
                        <label class="form-label" for="formInputEmail">{{__('auth.email')}}:
                            <span class="required"></span>
                        </label>
                        <input type="email" class="form-control" id="formInputEmail"
                               name="email" value="{{ old('email') }}" placeholder="{{__('auth.login.enter_email')}}" required>
                    </div>
                    <div class="col-md-6 mb-sm-7 mb-4">
                        {{ Form::label('phone',__('messages.user.phone').(':'), ['class' => 'form-label']) }}
                        <br>
                        {{ Form::tel('phone', null, ['class' => 'form-control w-100 iti phoneNumber','id' => 'phoneNumber', 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
                        {{ Form::hidden('prefix_code',null,['id'=>'prefix_code', 'class' => 'prefix_code']) }}
                        <span class="text-success d-none fw-400 fs-small mt-2 valid-msg">✓ &nbsp; {{__('messages.valid')}}</span>
                        <span class="text-danger d-none fw-400 fs-small mt-2 error-msg"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-sm-7 mb-4">
                        <label for="formInputPassword" class="form-label">
                            {{__('auth.password')}}:<span class="required"></span>
                        </label>
                        <input type="password" class="form-control" id="formInputPassword"
                               name="password" placeholder="{{ __('auth.registration.enter_password') }}" required
                               aria-describedby="password">
                    </div>
                    <div class="col-md-6 mb-sm-7 mb-4">
                        <label for="formInputConfirmPassword" class="form-label">
                            {{__('auth.confirm_password')}}:<span class="required"></span>
                        </label>
                        <input type="password" class="form-control" id="formInputConfirmPassword"
                               aria-describedby="confirmPassword" name="password_confirmation"
                               placeholder="{{ __('auth.registration.enter_confirm_password') }}" required>
                    </div>
                </div>
                <div class="col-xl-12 mt-2 d-flex justify-content-center">
                    @if(getSuperAdminSettingKeyValue('enable_google_recaptcha'))
                            <div class="form-group mb-4">
                                <div class="g-recaptcha" data-sitekey="{{getSuperAdminSettingKeyValue('google_captcha_key')}}">
                                </div>
                            </div>
                    @endif
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">{{__('auth.submit')}}</button>
                </div>
                <div class="d-flex align-items-center mt-4">
                    <span class="text-gray-700 me-2">{{__('auth.already_user')}}</span>
                    <a href="{{ route('login') }}" class="link-info fs-6 text-decoration-none">
                        {{__('auth.sign_in')}}
                    </a>
                </div>
            </form>
        </div>
    </div>
    <!--end::Authentication - Sign-up-->
@endsection
@section('scripts')
    <script>
        let utilsScript = "{{asset('assets/js/int-tel/js/utils.min.js')}}";
        let isEdit = false;
        let onRegister = true;
        if ($('#g-recaptcha').length) {
            grecaptcha.render('g-recaptcha', {
                'sitekey': $('#adminRecaptcha').val(),
            })
        }

    </script>

    <script src='https://www.google.com/recaptcha/api.js'></script>
   {{-- <script src="{{ asset('node_modules/intl-tel-input/build/js/intlTelInput.js') }}"></script> --}}
   <script>
            $(document).ready(function() {

if (!$('.phoneNumber').length) {
    return false
}

let input = document.querySelector('.phoneNumber'),
    errorMsg = document.querySelector('.error-msg'),
    validMsg = document.querySelector('.valid-msg')

let errorMap = [
    $('.invalidNumber').val(),
    $('.invalidCountryNumber').val(),
    $('.tooShort').val(),
    $('.tooLong').val(),
    $('.invalidNumber').val(),
]

// initialise plugin
let intl = window.intlTelInput(input, {
    initialCountry: 'auto',
    separateDialCode: true,
    geoIpLookup: function(success, failure) {
        $.get('https://ipinfo.io', function() {}, 'jsonp').always(function(resp) {
            var countryCode = (resp && resp.country) ?
                resp.country :
                ''
            success(countryCode)
        })
    },
    utilsScript: $('.utilsScript').val(),
})

let reset = function() {
    input.classList.remove('error')
    errorMsg.innerHTML = ''
    errorMsg.classList.add('d-none')
    validMsg.classList.add('d-none')
}

input.addEventListener('blur', function() {
    reset()
    if (input.value.trim()) {
        if (intl.isValidNumber()) {
            validMsg.classList.remove('d-none')
        } else {
            input.classList.add('error')
            var errorCode = intl.getValidationError()
            errorMsg.innerHTML = errorMap[errorCode] || $('.invalidNumber').val()
            errorMsg.classList.remove('d-none')
        }
    }
})

// on keyup / change flag: reset
input.addEventListener('change', reset)
input.addEventListener('keyup', reset)
const phoneNo = $('.phoneNo').val()
if (typeof phoneNo != 'undefined' && phoneNo !== '') {
    setTimeout(function() {
        $('.phoneNumber').trigger('change')
    }, 500)
}

$(document).on('blur keyup change countrychange', function() {
    if (typeof phoneNo != 'undefined' && phoneNo !== '') {
        intl.setNumber('+' + phoneNo)
        phoneNo = ''
    }
    let getCode = intl.selectedCountryData['dialCode']
    $('.prefix_code').val(getCode)
})

if ($('.isEdit').val()) {
    let getCode = intl.selectedCountryData['dialCode']
    $('.prefix_code').val(getCode)
}

$('.phoneNumber').focus()
$('.phoneNumber').blur()
let getPhoneNumber = $('.phoneNumber').val()
let removeSpacePhoneNumber = getPhoneNumber.replace(/\s/g, '')
$('.phoneNumber').val(removeSpacePhoneNumber)
});
   </script>
@endsection
