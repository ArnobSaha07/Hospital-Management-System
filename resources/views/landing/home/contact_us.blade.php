@extends('landing.layouts.app')
@section('title')
    Contact Us
@endsection
@section('page_css')
    {{--    <link href="{{asset('assets/css/landing/landing.css')}}" rel="stylesheet" type="text/css"/> --}}
    {{--    <link href="{{mix('landing_front/css/contact.css')}}" rel="stylesheet" type="text/css"> --}}
    <link href="{{ asset('landing_front/css/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @php
        $settingValue = getSuperAdminSettingValue();
    @endphp

    <div class="contact-page">
        <!-- start hero section -->
        <section class="hero-section pt-120 bg-light">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 text-lg-start text-center mb-lg-0
                            mb-md-5 mb-sm-4 mb-3">
                        <div class="hero-content">
                            <h1 class="mb-0">
                                {{ __('messages.contact_us') }}
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol
                                    class="breadcrumb
                                        justify-content-lg-start
                                        justify-content-center mb-lg-0 pb-lg-4">
                                    <li class="breadcrumb-item"><a href="{{ route('landing-home') }}"
                                            class="fs-18">{{ __('messages.landing.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item text-cyan
                                            fs-18"
                                        aria-current="page">{{ __('messages.contact_us') }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-lg-6 text-lg-end text-center">
                        <img src="{{ asset('landing_front/images/about-hero-img.png') }}" alt="HMS-Sass"
                            class="img-fluid" loading="lazy"/>
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        <!-- start form-section -->
        <section class="form-section py-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="section-heading">
                            <h2 class="mb-3">{{ __('messages.contact_us') }}</h2>
                            <p class="mb-0">{{ __('messages.web_contact.get_in_touch') }}</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form p-60">
                    <div class="row flex-column-reverse flex-lg-row">
                        <div class="col-lg-6">
                            <div class="form">
                                <form id="superAdminContactEnquiryForm" method="POST" class="row">
                                    @method('POST')
                                    @csrf
                                    <div class="ajax-message-contact"></div>
                                    <div class="form-group col-md-6">
                                        <input id="firstName" type="text" name="first_name"
                                            class="form-control mb-md-4 mb-3
                                                px-md-4 py-sm-3 px-3 py-2 f-s-6"
                                            placeholder="{{ __('messages.web_appointment.enter_your_first_name') }}"
                                            required="required" data-error="First name is required.">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input id="lastName" type="text" name="last_name"
                                            class="form-control mb-md-4 mb-3
                                                px-md-4 py-sm-3 px-3 py-2 f-s-6"
                                            placeholder="{{ __('messages.web_appointment.enter_your_last_name') }}"
                                            required="required" data-error="Lastname is required.">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input id="email" type="email" name="email"
                                            class="form-control mb-md-4 mb-3
                                                px-md-4 py-sm-3 px-3 py-2 f-s-6"
                                            placeholder="{{ __('messages.web_contact.enter_your_email') }}"
                                            required="required" data-error="Valid email is required.">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="form-group col-md-6 mb-6">
                                        <input type="tel"
                                            class="form-control phoneNumber mb-md-4 mb-3
                                                   py-sm-3 py-2 f-s-6 {{ $errors->has('contact_no') ? 'is-invalid' : '' }}"
                                            id="phoneNumber" name="phone" value="{{ old('phone') }}"required
                                            data-error="{{ __('messages.web_contact.please_enter_your_phone_number') }}"
                                            onkeyup='if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")'>
                                        <div class="help-block with-errors"></div>
                                        {{ Form::hidden('prefix_code', null, ['class' => 'prefix_code']) }}
                                        <span class="text-green d-none fw-400 fs-small mt-2 valid-msg" id="valid-msg">✓ &nbsp;
                                            {{ __('messages.valid') }}</span>
                                        <span class="text-danger d-none fw-400 fs-small mt-2 error-msg" id="error-msg"></span>
                                    </div>
                                    <div class="form-group col-12">
                                        <textarea id="message" name="message"
                                            class="form-control
                                                px-md-4 py-sm-3 px-3 py-2 f-s-6"
                                            placeholder="{{ __('messages.web_contact.write_your_message') }}" rows="3" required="required"
                                            data-error="Please,leave us a message."></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        @if (getSuperAdminSettingKeyValue('enable_google_recaptcha'))
                                            <div class="form-group mb-4 captcha-customize">
                                                <div class="g-recaptcha" id="g-recaptcha"
                                                    data-sitekey="{{ getSuperAdminSettingKeyValue('google_captcha_key') }}"
                                                    data-callback="verifyRecaptchaCallback"
                                                    data-expired-callback="expiredRecaptchaCallback">
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-sm-8">
                                        <button type="submit" class="btn btn-secondary">
                                            {{ __('messages.web_contact.send_message') }}
                                        </button>
                                    </div>
                                    {{ Form::hidden('superAdminCaptchaSetting', getSuperAdminSettingKeyValue('enable_google_recaptcha'), ['id' => 'superAdminCaptcha']) }}
                                    {{ Form::hidden('superAdminEnquiryStore', route('super.admin.enquiry.store'), ['id' => 'superAdminEnquiryStore']) }}
                                    {{ Form::hidden('superAdminEnquiryGRecaptcha', getSuperAdminSettingKeyValue('google_captcha_key'), ['id' => 'superAdminEnquiryGRecaptcha']) }}
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-right bg-cyan p-60">
                                <div class="form-info">
                                    <div
                                        class="d-flex align-items-center
                                            mb-md-4 mb-3">
                                        <div
                                            class="icon bg-white d-flex
                                                justify-content-center
                                                align-items-center me-md-4
                                                me-3">
                                            <i
                                                class="fa-solid
                                                    fa-location-dot
                                                    text-secondary d-flex
                                                    justify-content-center
                                                    align-items-center"></i>
                                        </div>
                                        <div class="desc">
                                            <h3 class="text-white mb-0">{{ __('messages.common.address') }}</h3>
                                            <p class="text-white mb-0">
                                                {{ $settingValue['address']['value'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center
                                            mb-md-4 mb-3">
                                        <div
                                            class="icon bg-white d-flex
                                                justify-content-center
                                                align-items-center me-md-4
                                                me-3">
                                            <i
                                                class="fa-solid fa-at
                                                    text-secondary d-flex
                                                    justify-content-center
                                                    align-items-center"></i>
                                        </div>
                                        <div class="desc">
                                            <h3 class="text-white mb-0">{{ __('messages.enquiry.email') }}</h3>
                                            <a href="mailto:{{ $settingValue['email']['value'] }}" class="text-white">
                                                {{ $settingValue['email']['value'] }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div
                                            class="icon bg-white d-flex
                                                justify-content-center
                                                align-items-center me-md-4
                                                me-3">
                                            <i
                                                class="fa-solid fa-phone
                                                    text-secondary d-flex
                                                    justify-content-center
                                                    align-items-center"></i>
                                        </div>
                                        <div class="desc">
                                            <h3 class="text-white mb-0">{{ __('messages.case.phone') }}</h3>
                                            <a href="tel:{{ $settingValue['phone']['value'] }}"
                                                class="text-white">{{ $settingValue['phone']['value'] }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        @if(getSuperAdminSettingKeyValue('enable_google_recaptcha'))
            {{ Form::hidden('isAdminGoogleCaptchaEnabled', getSuperAdminSettingKeyValue('enable_google_recaptcha'), ['class' => 'isAdminGoogleCaptchaEnabled']) }}
        @endif
        <!-- end form-section -->
        {{ Form::hidden('utilsScript', asset('assets/js/int-tel/js/utils.min.js'), ['class' => 'utilsScript']) }}

        <!-- start subscribe-section -->
        @include('landing.home.subscribe_section')
        <!-- end subscribe-section -->

    </div>
@endsection
@section('scripts')
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        $(document).ready(function() {
            var phoneNo = $('.phoneNo').val();
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
