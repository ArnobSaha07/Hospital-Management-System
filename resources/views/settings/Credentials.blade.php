@extends('layouts.app')
@section('title')
    {{ __('messages.setting.payment_gateway') }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column">
            @include('flash::message')
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => 'payment-gateway.store', 'id' => 'UserCredentialsSettings', 'class' => 'form']) }}
                    <div class="row">
                        {{-- STRIPE --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.stripe') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="stripe_enable" class="form-check-input stripe-enable"
                                    value="1" {{ !empty($setting['stripe_enable']) == '1' ? 'checked' : '' }}
                                    id="stripeEnable">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="stripe-div d-none col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('stripe_key', __('messages.setting.stripe_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('stripe_key', !empty($setting['stripe_key']) ? $setting['stripe_key'] : null, ['class' => 'form-control', 'id' => 'stripeKey', 'placeholder' => __('messages.setting.stripe_key')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('stripe_secret', __('messages.setting.stripe_secret') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('stripe_secret', !empty($setting['stripe_secret']) ? $setting['stripe_secret'] : null, ['class' => 'form-control', 'id' => 'stripeSecret', 'placeholder' => __('messages.setting.stripe_secret')]) }}
                                </div>
                            </div>
                        </div>




                        {{--                                 PAYPAL --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.paypal') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="paypal_enable" class="form-check-input paypal-enable"
                                    value="1" {{ !empty($setting['paypal_enable']) == '1' ? 'checked' : '' }}
                                    id="paypalEnable">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="paypal-div d-none  col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paypal_client_id', __('messages.setting.paypal_client_id') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paypal_client_id', !empty($setting['paypal_client_id']) ? $setting['paypal_client_id'] : null, ['class' => 'form-control', 'id' => 'paypalKey', 'placeholder' => __('messages.setting.paypal_client_id')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paypal_secret', __('messages.setting.paypal_secret') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paypal_secret', !empty($setting['paypal_secret']) ? $setting['paypal_secret'] : null, ['class' => 'form-control', 'id' => 'paypalSecret', 'placeholder' => __('messages.setting.paypal_secret')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paypal_mode', __('messages.setting.paypal_mode') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paypal_mode', !empty($setting['paypal_mode']) ? $setting['paypal_mode'] : null, ['class' => 'form-control', 'id' => 'paypalMode', 'placeholder' => __('messages.setting.paypal_mode')]) }}
                                </div>
                            </div>
                        </div>
                        {{--                         Razorpay --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.razorpay') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="razorpay_enable" class="form-check-input razorpay_enable"
                                    value="1" {{ !empty($setting['razorpay_enable']) == '1' ? 'checked' : '' }}
                                    id="razorpayEnable">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="razorpay-div d-none col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">

                                    {{ Form::label('razorpay_key', __('messages.setting.razorpay_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('razorpay_key', !empty($setting['razorpay_key']) ? $setting['razorpay_key'] : null, ['class' => 'form-control', 'id' => 'razorpayKey', 'placeholder' => __('messages.setting.razorpay_key')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('razorpay_secret', __('messages.setting.razorpay_secret') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('razorpay_secret', !empty($setting['razorpay_key']) ? $setting['razorpay_secret'] : null, ['class' => 'form-control', 'id' => 'razorpaySecret', 'placeholder' => __('messages.setting.razorpay_secret')]) }}
                                </div>
                            </div>
                        </div>

                        {{--                        Paytm --}}

                        {{-- <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.paytm') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="paytm_enable" class="form-check-input paytm_enable"
                                    value="1" {{ !empty($setting['paytm_enable']) == '1' ? 'checked' : '' }}
                                    id="paytmEnable">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="paytm-div d-none col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">

                                    {{ Form::label('paytm_merchant_id', __('messages.setting.paytm_merchant_id') . ':', ['class' => 'form-label']) }}
                                       {{ Form::text('paytm_merchant_id', !empty($setting['paytm_merchant_key']) ? $setting['paytm_merchant_id'] : null, ['class' => 'form-control', 'id' => 'paytmMerchantId', 'placeholder' => __('messages.setting.paytm_merchant_id')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paytm_merchant_key', __('messages.setting.paytm_merchant_key') . ':', ['class' => 'form-label']) }}
                                    {{ Form::text('paytm_merchant_key', !empty($setting['paytm_merchant_key']) ? $setting['paytm_merchant_key'] : null, ['class' => 'form-control', 'id' => 'paytmMerchantKey', 'placeholder' => __('messages.setting.paytm_merchant_key')]) }}
                                </div>
                            </div>
                        </div> --}}

                        {{--                        Paystack --}}

                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.paystack') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="paystack_enable" class="form-check-input paystack_enable"
                                    value="1" {{ !empty($setting['paystack_enable']) == '1' ? 'checked' : '' }}
                                    id="paystackEnable">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="paystack-div d-none col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">

                                    {{ Form::label('paystack_public_key', __('messages.setting.paystack_public_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paystack_public_key', !empty($setting['paystack_public_key']) ? $setting['paystack_public_key'] : null, ['class' => 'form-control', 'id' => 'paystackPublicKey', 'placeholder' => __('messages.setting.paystack_public_key')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paystack_secret_key', __('messages.setting.paystack_secret_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paystack_secret_key', !empty($setting['paystack_secret_key']) ? $setting['paystack_secret_key'] : null, ['class' => 'form-control', 'id' => 'paystackSecretKey', 'placeholder' => __('messages.setting.paystack_secret_key')]) }}
                                </div>
                            </div>
                        </div>

                        {{-- PhonePe --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.phonepe.phonepe') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="phone_pe_enable" class="form-check-input phone_pe_enable" value="1"
                                    {{ !empty($setting['phone_pe_enable']) == '1' ? 'checked' : '' }} id="phonePeEnable">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="d-none col-12 phonepe-div">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_merchant_id', __('messages.phonepe.phonepe_merchant_id') . ':', ['class' => 'form-label mb-3 required']) }}
                                    {{ Form::text('phonepe_merchant_id', $setting['phonepe_merchant_id'] ?? null, ['class' => 'form-control  phonepe_merchant_id ', 'placeholder' => __('messages.phonepe.phonepe_merchant_id')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_merchant_user_id', __('messages.phonepe.phonepe_merchant_user_id') . ':', ['class' => 'form-label mb-3 required']) }}
                                    {{ Form::text('phonepe_merchant_user_id', $setting['phonepe_merchant_user_id'] ?? null, ['class' => 'form-control phonepe_merchant_user_id ', 'placeholder' => __('messages.phonepe.phonepe_merchant_user_id')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_env', __('messages.phonepe.phonepe_env') . ':', ['class' => 'form-label mb-3 required']) }}
                                    {{ Form::text('phonepe_env', $setting['phonepe_env'] ?? null, ['class' => 'form-control  phonepe_env ', 'placeholder' => __('messages.phonepe.phonepe_env')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_salt_key', __('messages.phonepe.phonepe_salt_key') . ':', ['class' => 'form-label mb-3 required']) }}
                                    {{ Form::text('phonepe_salt_key', $setting['phonepe_salt_key'] ?? null, ['class' => 'form-control phonepe_salt_key ', 'placeholder' => __('messages.phonepe.phonepe_salt_key')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_salt_index', __('messages.phonepe.phonepe_salt_index') . ':', ['class' => 'form-label mb-3 required']) }}
                                    {{ Form::text('phonepe_salt_index', $setting['phonepe_salt_index'] ?? null, ['class' => 'form-control  phonepe_salt_index ', 'placeholder' => __('messages.phonepe.phonepe_salt_index')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_merchant_transaction_id', __('messages.phonepe.phonepe_merchant_transaction_id') . ':', ['class' => 'form-label mb-3 required']) }}
                                    {{ Form::text('phonepe_merchant_transaction_id', $setting['phonepe_merchant_transaction_id'] ?? null, ['class' => 'form-control phonepe_merchant_transaction_id ', 'placeholder' => __('messages.phonepe.phonepe_merchant_transaction_id')]) }}
                                </div>
                            </div>
                        </div>

                        {{-- FlutterWave--}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.flutterwave.flutterwave'). ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="flutterwave_enable" class="form-check-input flutterwave_enable"
                                       value="1"
                                       {{ !empty($setting['flutterwave_enable'])  == '1' ? 'checked' : ''}}
                                       id="flutterWaveEnable">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="flutterWave-div d-none col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('flutterwave_public_key', __('messages.flutterwave.flutterwave_public_key').':', ['class' => 'form-label required']) }}
                                    {{ Form::text('flutterwave_public_key',(!empty($setting['flutterwave_public_key'])) ? $setting['flutterwave_public_key'] : null, ['class' => 'form-control flutterwave_public_key', 'placeholder' => __('messages.flutterwave.flutterwave_public_key')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('flutterwave_secret_key', __('messages.flutterwave.flutterwave_secret_key').':', ['class' => 'form-label required']) }}
                                    {{ Form::text('flutterwave_secret_key',!empty($setting['flutterwave_secret_key']) ? $setting['flutterwave_secret_key']: null, ['class' => 'form-control flutterwave_secret_key', 'placeholder' => __('messages.flutterwave.flutterwave_secret_key')]) }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"
                                id="userCredentialSettingBtn">{{ __('messages.common.save') }}</button>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
