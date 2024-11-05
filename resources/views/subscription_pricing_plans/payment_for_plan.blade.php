@extends('layouts.app')
@section('title')
    {{ __('messages.subscription_plans.payment_type') }}
@endsection
@section('page_css')
    <link href="{{ asset('landing_front/css/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('header_toolbar')
    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between mb-5">
            <div class="mb-0"></div>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ url()->previous() }}" class="btn btn-outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        @include('flash::message')
        <div class="d-flex flex-column">
            <div class="card">
                @php
                    $cpData = getCurrentPlanDetails();
                    $planText = $cpData['isExpired']
                        ? __('messages.new_change.current_expired_plan')
                        : __('messages.new_change.current_plan');
                    $currentPlan = $cpData['currentPlan'];
                    $currency = ['ZAR', 'USD', 'GHS', 'NGN', 'KES'];
                @endphp
                <div class="card-body p-lg-10">
                    <div class="row">
                        @if (currentActiveSubscription()->ends_at >= \Carbon\Carbon::now())
                            <div class="col-md-6 col-12 mb-md-0 mb-10">
                                <div class="card p-5 me-md-2">
                                    <div class="card-header  px-0">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="text-primary mb-1 me-0">{{ $planText }}</span>
                                        </h3>
                                    </div>
                                    <div class="card-body py-3 px-0">
                                        <div class="d-flex align-items-center plan-border-bottom py-2">
                                            <h4 class="plan-data mb-0 me-5">
                                                {{ __('messages.subscription_plans.plan_name') }}</h4>
                                            <span class="text-muted  mt-1">{{ $cpData['name'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center plan-border-bottom py-2">
                                            <h4 class="plan-data mb-0 me-5">{{ __('messages.new_change.plan_price') }}</h4>
                                            <span class="text-muted  mt-1">
                                                <span class="mb-2">
                                                    {{ getAdminCurrencySymbol($currentPlan->currency) }}
                                                </span>
                                                {{ number_format($currentPlan->price, 2) }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center plan-border-bottom py-2">
                                            <h4 class="plan-data mb-0 me-5">
                                                {{ __('messages.subscription_plans.start_date') }}</h4>
                                            <span class="text-muted mt-1">{{ $cpData['startAt'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center plan-border-bottom py-2">
                                            <h4 class="plan-data mb-0 me-5">
                                                {{ __('messages.subscription_plans.end_date') }}</h4>
                                            <span class="text-muted mt-1">{{ $cpData['endsAt'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center plan-border-bottom py-2">
                                            <h4 class="plan-data mb-0 me-5">
                                                {{ __('messages.subscription_plans.used_days') }}</h4>
                                            <span class="text-muted mt-1">{{ $cpData['usedDays'] }}
                                                {{ __('messages.prescription.days') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center plan-border-bottom py-2">
                                            <h4 class="plan-data mb-0 me-5">{{ __('messages.new_change.remaining_days') }}
                                            </h4>
                                            <span class="text-muted mt-1">{{ $cpData['remainingDays'] }}
                                                {{ __('messages.prescription.days') }}</span>
                                        </div>
                                        <div class="d-flex align-items-center plan-border-bottom py-2">
                                            <h4 class="plan-data mb-0 me-5">{{ __('messages.new_change.used_balance') }}
                                            </h4>
                                            <span class="text-muted  mt-1">
                                                <span class="mb-2">
                                                    {{ getAdminCurrencySymbol($currentPlan->currency) }}
                                                </span>
                                                {{ $cpData['usedBalance'] }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center plan-border-bottom py-2">
                                            <h4 class="plan-data mb-0 me-5">
                                                {{ __('messages.subscription_plans.remaining_balance') }}</h4>
                                            <span class="text-muted  mt-1">
                                                <span
                                                    class="mb-2">{{ getAdminCurrencySymbol($currentPlan->currency) }}</span>
                                                {{ $cpData['remainingBalance'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @php
                            $newPlan = getProratedPlanData($subscriptionsPricingPlan->id);
                        @endphp
                        <div class="col-md-6 col-12">
                            <div class="card p-5 ms-md-2">
                                <div class="card-header px-0">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span
                                            class="text-primary mb-1 me-0">{{ __('messages.new_change.new_plan') }}</span>
                                    </h3>
                                </div>
                                <div class="card-body py-3 px-0">
                                    <div class="d-flex align-items-center plan-border-bottom py-2">
                                        <h4 class="plan-data mb-0 me-5">{{ __('messages.subscription_plans.plan_name') }}
                                        </h4>
                                        <span class="text-muted mt-1">{{ $newPlan['name'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center plan-border-bottom py-2">
                                        <h4 class="plan-data mb-0 me-5">{{ __('messages.new_change.plan_price') }}</h4>
                                        <span class="text-muted  mt-1">
                                            <span class="mb-2">
                                                {{ getAdminCurrencySymbol($subscriptionsPricingPlan->currency) }}
                                            </span>
                                            {{ number_format($subscriptionsPricingPlan->price, 2) }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center plan-border-bottom py-2">
                                        <h4 class="plan-data mb-0 me-5">{{ __('messages.subscription_plans.start_date') }}
                                        </h4>
                                        <span class="text-muted mt-1">{{ $newPlan['startDate'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center plan-border-bottom py-2">
                                        <h4 class="plan-data mb-0 me-5">{{ __('messages.subscription_plans.end_date') }}
                                        </h4>
                                        <span class="text-muted mt-1">{{ $newPlan['endDate'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center plan-border-bottom py-2">
                                        <h4 class="plan-data mb-0 me-5">{{ __('messages.bill.total_days') }}</h4>
                                        <span class="text-muted  mt-1">{{ $newPlan['totalDays'] }}
                                            {{ __('messages.prescription.days') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center plan-border-bottom py-2">
                                        <h4 class="plan-data mb-0 me-5">{{ __('messages.new_change.pre_plan') }}</h4>
                                        <span class="text-muted  mt-1">
                                            {{ getAdminCurrencySymbol($subscriptionsPricingPlan->currency) }}
                                            {{ $newPlan['remainingBalance'] }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center plan-border-bottom py-2">
                                        <h4 class="plan-data mb-0 me-5">{{ __('messages.new_change.payable_amount') }}</h4>
                                        <span class="text-muted mt-1">
                                            {{ getAdminCurrencySymbol($subscriptionsPricingPlan->currency) }}
                                            {{ $newPlan['amountToPay'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($transction)
                        <div class="row justify-content-center">
                            <div
                                class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 d-flex justify-content-center align-items-center mt-5 plan-controls">
                                {{-- <div class="mt-5 me-3  w-50{{ $newPlan['amountToPay'] <= 0 ? 'd-none' : '' }}">
                                    {{ Form::select('payment_type', $paymentTypes, \App\Models\Subscription::TYPE_STRIPE, ['class' => 'form-select','required', 'id' => 'paymentType', 'data-control' => 'select2','placeholder'=>__("Select Payment Gateway")]) }}
                                </div> --}}
                                <div class="mt-5 stripePayment proceed-to-payment">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block makePayment {{ $newPlan['amountToPay'] <= 0 ? '' : 'disabled' }}"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 paypalPayment proceed-to-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentByPaypal"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 razorPayPayment proceed-to-razor-pay-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block razor_pay_payment"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 cashPayment proceed-cash-payment d-none">
                                    <button type="button" class="btn btn-primary rounded-pill mx-auto d-block cash_payment"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                @if (strtolower($subscriptionsPricingPlan->currency) == 'inr')
                                    <div class="mt-5 paytmPayment proceed-to-payment d-none">
                                        <button type="button"
                                            class="btn btn-primary rounded-pill mx-auto d-block paymentByPaytm"
                                            data-id="{{ $subscriptionsPricingPlan->id }}"
                                            data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                            {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                    </div>
                                @endif
                                @if (strtolower($subscriptionsPricingPlan->currency) == 'inr')
                                    <div class="mt-5 phonePePayment proceed-to-phonepe-payment d-none">
                                        <button type="button"
                                            class="btn btn-primary rounded-pill mx-auto d-block paymentByPhonePe"
                                            data-id="{{ $subscriptionsPricingPlan->id }}"
                                            data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                            {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                    </div>
                                @endif
                                <div class="mt-5 flutterWavePayment proceed-to-flutterWave-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentByflutterWave"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                @if (in_array(strtoupper(getCurrentCurrency()), $currency))
                                    <div class="mt-5 paystackPayment proceed-to-payment d-none">
                                        <button type="button"
                                            class="btn btn-primary rounded-pill mx-auto d-block paymentByPaystack"
                                            data-id="{{ $subscriptionsPricingPlan->id }}"
                                            data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                            {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="row justify-content-center">
                            <div
                                class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 d-flex justify-content-center align-items-center mt-5 plan-controls">
                                <div class="mt-5 me-3 w-50 {{ $newPlan['amountToPay'] <= 0 ? 'd-none' : '' }}">
                                    {{ Form::select('payment_type', getSuperAdminPaymentTypes(), null, ['class' => 'form-select', 'required', 'id' => 'paymentType', 'data-control' => 'select2', 'placeholder' => __('messages.new_change.select_payment_gateway')]) }}
                                </div>
                                <div
                                    class="mt-5 stripePayment proceed-to-payment {{ $newPlan['amountToPay'] <= 0 ? '' : 'd-none' }}">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block makePayment"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 paypalPayment proceed-to-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentByPaypal"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                <div class="mt-5 razorPayPayment proceed-to-razor-pay-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block razor_pay_payment"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                @if (strtolower($subscriptionsPricingPlan->currency) == 'inr')
                                    <div class="mt-5 phonePePayment proceed-to-phonepe-payment d-none">
                                        <button type="button"
                                            class="btn btn-primary rounded-pill mx-auto d-block paymentByPhonePe"
                                            data-id="{{ $subscriptionsPricingPlan->id }}"
                                            data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                            {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                    </div>
                                @endif
                                <div class="mt-5 flutterWavePayment proceed-to-flutterWave-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block paymentByflutterWave"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div>
                                {{-- <div class="mt-5 cashPayment proceed-cash-payment d-none">
                                    <button type="button"
                                        class="btn btn-primary rounded-pill mx-auto d-block cash_payment"
                                        data-id="{{ $subscriptionsPricingPlan->id }}"
                                        data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                        {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                </div> --}}
                                @if (strtolower($subscriptionsPricingPlan->currency) == 'inr')
                                    <div class="mt-5 paytmPayment proceed-to-payment d-none">
                                        <button type="button"
                                            class="btn btn-primary rounded-pill mx-auto d-block paymentByPaytm"
                                            data-id="{{ $subscriptionsPricingPlan->id }}"
                                            data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                            {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                    </div>
                                @endif
                                @if (in_array(strtoupper(getCurrentCurrency()), $currency))
                                    <div class="mt-5 paystackPayment proceed-to-payment d-none">
                                        <button type="button"
                                            class="btn btn-primary rounded-pill mx-auto d-block paymentByPaystack"
                                            data-id="{{ $subscriptionsPricingPlan->id }}"
                                            data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                            {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="row justify-content-center align-items-center">
                        <div
                            class="col-12 d-flex flex-column justify-content-center align-items-center mt-5 plan-controls">
                            <form class="manuallyPaymentForm" type="post" enctype="multipart/form-data">
                                <div class="mb-3 d-none manuallyPayAttachment me-5 pe-5" io-image-input="true">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <label for="exampleInputImage"
                                                        class="form-label">{{ __('messages.email.attachment') }}
                                                        :-</label>
                                                    <div class="d-block">
                                                        <div class="image-picker">
                                                            <div class="image previewImage" id="exampleInputImage"
                                                                style="background-image: url('{{ asset(getLogoUrl()) }}')">
                                                            </div>
                                                            <span class="picker-edit rounded-circle text-gray-500 fs-small"
                                                                data-bs-toggle="tooltip" data-placement="top"
                                                                data-bs-original-title="Choose Attachment">
                                                                <label>
                                                                    <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                                                    <input type="file" id="manual_payment_attachment"
                                                                        name="attachment" class="image-upload d-none"
                                                                        accept="image/*" />
                                                                </label>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9">
                                                    <label for="exampleInputImage"
                                                        class="form-label">{{ __('messages.document.notes') }}
                                                        :-</label>
                                                    <span data-bs-toggle="tooltip"
                                                        class="{{ $manualIntro ?? 'd-none' }} manualIntro"
                                                        id="planTooltip" data-placement="top"
                                                        data-bs-original-title="{{ $manualIntro ?? '' }}">
                                                        <i class="fas fa-circle-info ml-1"></i>
                                                    </span>
                                                    {{ Form::hidden('manual_intro', $manualIntro ?? null, ['class' => 'manual-intro']) }}
                                                    {{ Form::textarea('notes', null, ['class' => 'form-control notes', 'placeholder' => __('messages.document.notes'), 'rows' => '5']) }}
                                                </div>
                                            </div>
                                            {{ Form::hidden('plan_id', $subscriptionsPricingPlan->id) }}
                                            {{ Form::hidden('from_pricing', null, ['class' => 'fromPricing']) }}
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="mt-5 cashPayment proceed-cash-payment d-none">
                                                <button type="button"
                                                    class="btn btn-primary rounded-pill mx-auto d-block cash_payment"
                                                    data-id="{{ $subscriptionsPricingPlan->id }}"
                                                    data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                                    {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::hidden('getLoggedInUserdata', getLoggedInUser(), ['class' => 'getLoggedInUser']) }}
    {{ Form::hidden('logInUrl', url('login'), ['class' => 'logInUrl']) }}
    {{ Form::hidden('makePaymentURL', route('purchase-subscription'), ['class' => 'makePaymentURL']) }}
    {{ Form::hidden('subscribeText', __('messages.subscription_pricing_plans.choose_plan'), ['class' => 'subscribeText']) }}
    {{ Form::hidden('toastData', json_encode(session('toast-data')), ['class' => 'toastData']) }}
    {{ Form::hidden('subscriptionPlans', route('subscription.pricing.plans.index'), ['class' => 'subscriptionPlans']) }}
    {{ Form::hidden('makeRazorpayURl', route('razorpay.purchase.subscription'), ['class' => 'makeRazorpayURl']) }}
    {{ Form::hidden('razorpayPaymentFailed', route('razorpay.failed'), ['class' => 'razorpayPaymentFailed']) }}
    {{ Form::hidden('cashPaymentUrl', route('cash.pay.success'), ['class' => 'cashPaymentUrl']) }}

    {{--        @if (config('services.stripe.key')) --}}
    {{--            {{ Form::hidden('stripeData', Stripe(config('services.stripe.key'))), ['class' => 'stripeData'] }} --}}
    {{--        @endif --}}
    {{ Form::hidden('razorpayDataKey', config('payments.razorpay.key'), ['class' => 'razorpayDataKey']) }}
    {{ Form::hidden('razorpayDataAmount', 1, ['class' => 'razorpayDataKey']) }}
    {{ Form::hidden('razorpayDataCurrency', 'INR', ['class' => 'razorpayDataCurrency']) }}
    {{ Form::hidden('razorpayDataName', getAppName(), ['class' => 'razorpayDataName']) }}
    {{ Form::hidden('razorpayDataImage', asset(getLogoUrl()), ['class' => 'razorpayDataImage']) }}
    {{ Form::hidden('razorpayDataCallBackURL', route('razorpay.success'), ['class' => 'razorpayDataCallBackURL']) }}

@endsection
@section('page_scripts')
    <script src="{{ asset('landing_front/js/jquery.toast.min.js') }}"></script>
@endsection
@section('scripts')
    <script src="//js.stripe.com/v3/"></script>
    <script src="//checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        {{-- let makePaymentURL = "{{ route('purchase-subscription') }}" --}}
        {{-- let subscribeText = "{{ __('messages.subscription_pricing_plans.choose_plan') }}" --}}
        let stripe = ''
        if (!isEmpty(('{{ superAdminStripeApiKey() }}'))) {
            stripe = Stripe('{{ superAdminStripeApiKey() }}')
        }

        {{-- let subscriptionPlans = "{{ route('subscription.pricing.plans.index') }}"; --}}
        {{-- let toastData = JSON.parse('@json(session('toast-data'))'); --}}
        {{-- let makeRazorpayURl = "{{ route('razorpay.purchase.subscription') }}"; --}}
        {{-- let razorpayPaymentFailed = "{{ route('razorpay.failed') }} "; --}}
        {{-- let razorpayPaymentFailedModal = "{{ route('razorpay.failed.modal') }}"; --}}
        {{-- let cashPaymentUrl = "{{ route('cash.pay.success') }}"; --}}
        {{-- let options = { --}}
        {{--    'key': "{{ config('payments.razorpay.key') }}", --}}
        {{--    'amount': 1, //  100 refers to 1 --}}
        {{--    'currency': 'INR', --}}
        {{--    'name': "{{getAppName()}}", --}}
        {{--    'order_id': '', --}}
        {{--    'description': '', --}}
        {{--    'image': '{{ getLogoUrl() }}', // logo here --}}
        {{--    'callback_url': "{{ route('razorpay.success') }}", --}}
        {{--    'prefill': { --}}
        {{--        'email': '', // recipient email here --}}
        {{--        'name': '', // recipient name here --}}
        {{--        'contact': '', // recipient phone here --}}
        {{--    }, --}}
        {{--    'readonly': { --}}
        {{--        'name': 'true', --}}
        {{--        'email': 'true', --}}
        {{--        'contact': 'true', --}}
        {{--    }, --}}
        {{--    'modal': { --}}
        {{--        'ondismiss': function () { --}}
        {{--            $.ajax({ --}}
        {{--                type: 'POST', --}}
        {{--                url: $('.razorpayPaymentFailed').val(), --}}
        {{--                success: function (result) { --}}
        {{--                    if (result.url) { --}}
        {{--                        window.location.href = result.url; --}}
        {{--                    } --}}
        {{--                }, --}}
        {{--                error: function (result) { --}}
        {{--                    displayErrorMessage(result.responseJSON.message) --}}
        {{--                }, --}}
        {{--            }); --}}
        {{--        }, --}}
        {{--    }, --}}
        {{-- }; --}}
    </script>
    {{--    <script src="{{ mix('assets/js/subscriptions/subscription.js') }}"></script> --}}
    {{--    <script src="{{ mix('assets/js/subscriptions/payment-message.js') }}"></script> --}}
@endsection
