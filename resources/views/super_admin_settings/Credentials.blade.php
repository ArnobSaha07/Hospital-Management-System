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
                    {{ Form::open(['route' => 'super-admin-payment-gateway.update', 'class' => 'form', 'id' => 'adminPaymentForm']) }}
                    <div class="row">
                        {{-- STRIPE --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.stripe') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="stripe_enable" class="form-check-input stripe-enable"
                                    value="1" {{ !empty($setting['stripe_enable']) == '1' ? 'checked' : '' }}
                                    id="stripeEnableAdmin">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="stripe-div-admin col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('stripe_key', __('messages.setting.stripe_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('stripe_key', !empty($setting['stripe_key']) ? $setting['stripe_key'] : null, ['class' => 'form-control', 'id' => 'StripeAdminKey', 'placeholder' => __('messages.setting.stripe_key')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('stripe_secret', __('messages.setting.stripe_secret') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('stripe_secret', !empty($setting['stripe_secret']) ? $setting['stripe_secret'] : null, ['class' => 'form-control', 'id' => 'StripeAdminSecret', 'placeholder' => __('messages.setting.stripe_secret')]) }}
                                </div>
                            </div>
                        </div>




                        {{-- PAYPAL --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.paypal') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="paypal_enable" class="form-check-input paypal-enable"
                                    value="1" {{ !empty($setting['paypal_enable']) == '1' ? 'checked' : '' }}
                                    id="paypalEnableAdmin">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="paypal-div-admin col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paypal_client_id', __('messages.setting.paypal_client_id') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paypal_key', !empty($setting['paypal_key']) ? $setting['paypal_key'] : null, ['class' => 'form-control', 'id' => 'paypalKeyAdmin', 'placeholder' => __('messages.setting.paypal_client_id')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paypal_secret', __('messages.setting.paypal_secret') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paypal_secret', !empty($setting['paypal_secret']) ? $setting['paypal_secret'] : null, ['class' => 'form-control', 'id' => 'paypalSecretAdmin', 'placeholder' => __('messages.setting.paypal_secret')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paypal_mode', __('messages.setting.paypal_mode') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paypal_mode', !empty($setting['paypal_mode']) ? $setting['paypal_mode'] : null, ['class' => 'form-control', 'id' => 'paypalModeAdmin', 'placeholder' => __('messages.setting.paypal_mode')]) }}
                                </div>
                            </div>
                        </div>
                        {{-- Razorpay --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.razorpay') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="razorpay_enable" class="form-check-input razorpay_enable"
                                    value="1" {{ !empty($setting['razorpay_enable']) == '1' ? 'checked' : '' }}
                                    id="razorpayEnableAdmin">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="razorpay-div-admin col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">

                                    {{ Form::label('razorpay_key', __('messages.setting.razorpay_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('razorpay_key', !empty($setting['razorpay_key']) ? $setting['razorpay_key'] : null, ['class' => 'form-control', 'id' => 'razorpayKeyAdmin', 'placeholder' => __('messages.setting.razorpay_key')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('razorpay_secret', __('messages.setting.razorpay_secret') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('razorpay_secret', !empty($setting['razorpay_key']) ? $setting['razorpay_secret'] : null, ['class' => 'form-control', 'id' => 'razorpaySecretAdmin', 'placeholder' => __('messages.setting.razorpay_secret')]) }}
                                </div>
                            </div>
                        </div>

                        {{-- Paystack --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.setting.paystack') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="paystack_enable" class="form-check-input paystack-enable"
                                    value="1" {{ !empty($setting['paystack_enable']) == '1' ? 'checked' : '' }}
                                    id="paystackEnableAdmin">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="paystack-div-admin col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paystack_key', __('messages.setting.paystack_public_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paystack_key', !empty($setting['paystack_key']) ? $setting['paystack_key'] : null, ['class' => 'form-control', 'id' => 'paystackAdminKey', 'placeholder' => __('messages.setting.paystack_public_key')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('paystack_secret', __('messages.setting.paystack_secret_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('paystack_secret', !empty($setting['paystack_secret']) ? $setting['paystack_secret'] : null, ['class' => 'form-control', 'id' => 'paystackAdminSecret', 'placeholder' => __('messages.setting.paystack_secret_key')]) }}
                                </div>
                            </div>
                        </div>

                        {{-- phonepe --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.phonepe.phonepe') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="phonepe_enable" class="form-check-input phonepe-enable"
                                    value="1" {{ !empty($setting['phonepe_enable']) == '1' ? 'checked' : '' }}
                                    id="phonepeEnableAdmin">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="phonepe-div-admin col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_merchant_id', __('messages.phonepe.phonepe_merchant_id') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('phonepe_merchant_id', !empty($setting['phonepe_merchant_id']) ? $setting['phonepe_merchant_id'] : null, ['class' => 'form-control', 'id' => 'phonepeMerchantId', 'placeholder' => __('messages.phonepe.phonepe_merchant_id')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_merchant_user_id', __('messages.phonepe.phonepe_merchant_user_id') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('phonepe_merchant_user_id', !empty($setting['phonepe_merchant_user_id']) ? $setting['phonepe_merchant_user_id'] : null, ['class' => 'form-control', 'id' => 'phonepeMerchantUserId', 'placeholder' => __('messages.phonepe.phonepe_merchant_user_id')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_env', __('messages.phonepe.phonepe_env') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('phonepe_env', !empty($setting['phonepe_env']) ? $setting['phonepe_env'] : null, ['class' => 'form-control', 'id' => 'phonepeEnv', 'placeholder' => __('messages.phonepe.phonepe_merchant_user_id')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_salt_key', __('messages.phonepe.phonepe_salt_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('phonepe_salt_key', !empty($setting['phonepe_salt_key']) ? $setting['phonepe_salt_key'] : null, ['class' => 'form-control', 'id' => 'phonepeSaltKey', 'placeholder' => __('messages.phonepe.phonepe_salt_key')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_salt_index', __('messages.phonepe.phonepe_salt_index') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('phonepe_salt_index', !empty($setting['phonepe_salt_index']) ? $setting['phonepe_salt_index'] : null, ['class' => 'form-control', 'id' => 'phonepeSaltIndex', 'placeholder' => __('messages.phonepe.phonepe_salt_index')]) }}
                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('phonepe_merchant_transaction_id', __('messages.phonepe.phonepe_merchant_transaction_id') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('phonepe_merchant_transaction_id', !empty($setting['phonepe_merchant_transaction_id']) ? $setting['phonepe_merchant_transaction_id'] : null, ['class' => 'form-control', 'id' => 'phonepeMerchantTransactionId', 'placeholder' => __('messages.phonepe.phonepe_merchant_transaction_id')]) }}
                                </div>
                            </div>
                        </div>

                        {{-- FlutterWave --}}
                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.flutterwave.flutterwave') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="flutterwave_enable" class="form-check-input flutterwave-enable"
                                    value="1" {{ !empty($setting['flutterwave_enable']) == '1' ? 'checked' : '' }}
                                    id="flutterwaveEnableAdmin">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                        <div class="flutterwave-div-admin col-12">
                            <div class="row">
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('flutterwave_key', __('messages.flutterwave.flutterwave_public_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('flutterwave_key', !empty($setting['flutterwave_key']) ? $setting['flutterwave_key'] : null, ['class' => 'form-control', 'id' => 'flutterwaveAdminKey', 'placeholder' => __('messages.flutterwave.flutterwave_public_key')]) }}

                                </div>
                                <div class="form-group col-sm-6 mb-5">
                                    {{ Form::label('flutterwave_secret', __('messages.flutterwave.flutterwave_secret_key') . ':', ['class' => 'form-label required']) }}
                                    {{ Form::text('flutterwave_secret', !empty($setting['flutterwave_secret']) ? $setting['flutterwave_secret'] : null, ['class' => 'form-control', 'id' => 'flutterwaveAdminSecret', 'placeholder' => __('messages.flutterwave.flutterwave_secret_key')]) }}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex align-items-center">
                            <span class="form-label my-3">{{ __('messages.transaction_filter.manual') . ' :' }}</span>
                            <label class="form-check form-switch form-switch-sm ms-3">
                                <input type="checkbox" name="razorpay_enable" class="form-check-input" value="1"
                                    checked disabled id="manualPay">
                                <span class="custom-switch-indicator"></span>
                            </label>
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
{{--    <script src="{{ mix('assets/js/settings/credentials.js') }}"></script> --}}
