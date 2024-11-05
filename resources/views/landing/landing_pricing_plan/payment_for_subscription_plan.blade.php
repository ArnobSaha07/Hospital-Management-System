@extends('landing.layouts.app')
@section('title')
    {{ __('messages.subscription_plans.payment_type') }}
@endsection
@section('page_css')
    {{--    <link href="{{asset('assets/css/landing/landing.css')}}" rel="stylesheet" type="text/css"/> --}}
    <link rel="stylesheet" href="{{ asset('web_front/css/selectize.min.css') }}">
    <link href="{{ mix('assets/css/selectize-input.css') }}" rel="stylesheet" type="text/css" />
    {{--    <link href="{{mix('landing_front/css/choose-plan.css')}}" rel="stylesheet" type="text/css"> --}}
    <link href="{{ asset('landing_front/css/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')

    @php
        $cpData = getCurrentPlanDetails();
        $planText = $cpData['isExpired']
            ? __('messages.new_change.current_expired_plan')
            : __('messages.new_change.current_plan');
        $currentPlan = $cpData['currentPlan'];
        $getCurrentPlanAdminCurrencySymbol = getAdminCurrencySymbol($currentPlan->currency);
        $currency = ['ZAR', 'USD', 'GHS', 'NGN', 'KES'];
    @endphp

    <div class="choose-payment-plan-page">
        <!-- start pchoose-payment-plan section -->
        <section class="py-100">
            <div class="container">
                @include('flash::message')

                <div class="row justify-content-center">
                    @if (currentActiveSubscription()->ends_at >= \Carbon\Carbon::now())
                        <div class="col-xxl-5 col-lg-6 mb-4">
                            <div class="card plan-card-detail h-100 card-xxl-stretch mx-lg-2">
                                <div class="card-header border-0 px-0 bg-transparent">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="fw-bolder text-primary fs-3">{{ $planText }}</span>
                                    </h3>
                                    <hr>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center py-2">
                                        <h4 class="fs-5 w-50 mb-0 me-3 fw-bolder">
                                            {{ __('messages.subscription_plans.plan_name') }}</h4>
                                        <span class="fs-5 w-50 text-muted">{{ $cpData['name'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center py-2">
                                        <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                            {{ __('messages.new_change.plan_price') }}</h4>
                                        <span class="fs-5 w-50 text-muted">
                                            <span class="mb-2">
                                                {{ $getCurrentPlanAdminCurrencySymbol }}
                                            </span>
                                            {{ number_format($currentPlan->price,2) }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center py-2">
                                        <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                            {{ __('messages.subscription_plans.start_date') }}</h4>
                                        <span class="fs-5 w-50 text-muted">{{ $cpData['startAt'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center py-2">
                                        <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                            {{ __('messages.subscription_plans.end_date') }}</h4>
                                        <span class="fs-5 w-50 text-muted">{{ $cpData['endsAt'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center py-2">
                                        <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                            {{ __('messages.subscription_plans.used_days') }}</h4>
                                        <span class="fs-5 w-50 text-muted">{{ $cpData['usedDays'] }}
                                            {{ __('messages.prescription.days') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center py-2">
                                        <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                            {{ __('messages.new_change.remaining_days') }}</h4>
                                        <span class="fs-5 w-50 text-muted">{{ $cpData['remainingDays'] }}
                                            {{ __('messages.prescription.days') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center py-2">
                                        <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                            {{ __('messages.new_change.used_balance') }}</h4>
                                        <span class="fs-5 w-50 text-muted">
                                            <span class="mb-2">
                                                {{ $getCurrentPlanAdminCurrencySymbol }}
                                            </span>
                                            {{ $cpData['usedBalance'] }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center plan-border-bottom py-2">
                                        <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                            {{ __('messages.subscription_plans.remaining_balance') }}</h4>
                                        <span class="fs-5 w-50 text-muted">
                                            <span class="mb-2">{{ $getCurrentPlanAdminCurrencySymbol }}</span>
                                            {{ $cpData['remainingBalance'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @php
                        $newPlan = getProratedPlanData($subscriptionsPricingPlan->id);
                        $getSubscriptionPlanAdminCurrencySymbol = getAdminCurrencySymbol(
                            $subscriptionsPricingPlan->currency,
                        );
                    @endphp
                    <div class="col-xxl-5 col-lg-6 mb-4">
                        <div class="card plan-card-detail h-100 card-xxl-stretch mx-lg-2">
                            <div class="card-header border-0 px-0 bg-transparent">
                                <h3 class="card-title align-items-start flex-column">
                                    <span
                                        class="fw-bolder text-primary fs-3">{{ __('messages.new_change.new_plan') }}</span>
                                </h3>
                                <hr>
                            </div>
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center py-2">
                                    <h4 class="fs-5 w-50 mb-0 me-3 fw-bolder">
                                        {{ __('messages.subscription_plans.plan_name') }}</h4>
                                    <span class="fs-5 w-50 text-muted">{{ $newPlan['name'] }}</span>
                                </div>
                                <div class="d-flex align-items-center py-2">
                                    <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                        {{ __('messages.new_change.plan_price') }}</h4>
                                    <span class="fs-5 w-50 text-muted">
                                        <span class="mb-2">
                                            {{ $getSubscriptionPlanAdminCurrencySymbol }}
                                        </span>
                                        {{ number_format($subscriptionsPricingPlan->price,2) }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center py-2">
                                    <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                        {{ __('messages.subscription_plans.start_date') }}</h4>
                                    <span class="fs-5 w-50 text-muted">{{ $newPlan['startDate'] }}</span>
                                </div>
                                <div class="d-flex align-items-center py-2">
                                    <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                        {{ __('messages.subscription_plans.end_date') }}</h4>
                                    <span class="fs-5 w-50 text-muted">{{ $newPlan['endDate'] }}</span>
                                </div>
                                <div class="d-flex align-items-center py-2">
                                    <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                        {{ __('messages.bill.total_days') }}</h4>
                                    <span class="fs-5 w-50 text-muted">{{ $newPlan['totalDays'] }}
                                        {{ __('messages.prescription.days') }}</span>
                                </div>
                                <div class="d-flex align-items-center py-2">
                                    <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                        {{ __('messages.new_change.pre_plan') }}</h4>
                                    <span class="fs-5 w-50 text-muted">
                                        <span class="mb-2">
                                            {{ $getSubscriptionPlanAdminCurrencySymbol }}
                                        </span>
                                        {{ $newPlan['remainingBalance'] }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center py-2">
                                    <h4 class="fs-5 w-50 mb-0 me-3 text-gray-800 fw-bolder">
                                        {{ __('messages.new_change.payable_amount') }}</h4>
                                    <span class="fs-5 w-50 text-muted">
                                        <span class="mb-2">
                                            {{ $getSubscriptionPlanAdminCurrencySymbol }}
                                        </span>
                                        {{ $newPlan['amountToPay'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                use App\Models\Subscription;

                if (strtolower($subscriptionsPricingPlan->currency) != 'inr') {
                    $paymentTypes = Arr::except($paymentTypes, [Subscription::TYPE_PAYTM]);
                }
                if (strtolower($subscriptionsPricingPlan->currency) != 'zar') {
                    $paymentTypes = Arr::except($paymentTypes, [Subscription::TYPE_PAYSTACK]);
                }
                ?>
                <div class="ajax-message"></div>
                <div class="col-lg-5 mt-4 mx-auto text-center">
                    <div class="{{ $newPlan['amountToPay'] <= 0 ? 'd-none' : '' }}">
                        {{ Form::select('payment_type', getSuperAdminPaymentTypes(), \App\Models\Subscription::TYPE_STRIPE, ['required', 'id' => 'paymentType', 'class' => 'payment-type', 'data-turbo' => 'false']) }}
                    </div>
                    @if ($transction)
                        <div class="mt-5 stripePayment proceed-to-payment">
                            <button type="button" class="btn btn-primary text-nowrap makePayment"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                <span>
                                    {{ __('messages.subscription_plans.pay_or_switch_plan') }}
                                </span>
                            </button>
                        </div>
                        <div class="mt-5 paypalPayment proceed-to-payment d-none">
                            <button type="button" class="btn btn-primary text-nowrap paymentByPaypal"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                <span>
                                    {{ __('messages.subscription_plans.pay_or_switch_plan') }}
                                </span>
                            </button>
                        </div>
                        <div class="mt-5 razorPayPayment proceed-to-razor-pay-payment d-none">
                            <button type="button" class="btn btn-primary text-nowrap razor_pay_payment"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                        </div>
                        <div class="mt-5 cashPayment proceed-to-cash-payment d-none">
                            <button type="button" class="btn btn-primary text-nowrap cash_payment"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                        </div>
                        @if (strtolower($subscriptionsPricingPlan->currency) == 'inr')
                            <div class="mt-5 paytmPayment proceed-to-cash-payment d-none">
                                <button type="button" class="btn btn-primary text-nowrap paymentByPaytm"
                                    data-id="{{ $subscriptionsPricingPlan->id }}"
                                    data-plan-price="{{ $subscriptionsPricingPlan->price }}" disabled>
                                    {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                            </div>
                        @endif
                        @if (in_array(strtoupper(getCurrentCurrency()), $currency))
                            <div class="mt-5 paystackPayment proceed-to-cash-payment d-none">
                                <button type="button" class="btn btn-primary text-nowrap paymentByPaystack"
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
                    @else
                        <div class="mt-5 stripePayment proceed-to-payment">
                            <button type="button" class="btn btn-primary text-nowrap makePayment"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                <span>
                                    {{ __('messages.subscription_plans.pay_or_switch_plan') }}
                                </span>
                            </button>
                        </div>
                        <div class="mt-5 paypalPayment proceed-to-payment d-none">
                            <button type="button" class="btn btn-primary text-nowrap paymentByPaypal"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                <span>
                                    {{ __('messages.subscription_plans.pay_or_switch_plan') }}
                                </span>
                            </button>
                        </div>
                        <div class="mt-5 razorPayPayment proceed-to-razor-pay-payment d-none">
                            <button type="button" class="btn btn-primary text-nowrap razor_pay_payment"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                        </div>
                        <div class="mt-5 cashPayment proceed-to-cash-payment d-none">
                            <button type="button" class="btn btn-primary text-nowrap cash_payment"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                        </div>
                        {{-- @if (strtolower($subscriptionsPricingPlan->currency) == 'inr')
                            <div class="mt-5 paytmPayment proceed-to-cash-payment d-none">
                                <button type="button" class="btn btn-primary text-nowrap paymentByPaytm"
                                    data-id="{{ $subscriptionsPricingPlan->id }}"
                                    data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                    {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                            </div>
                        @endif --}}
                        @if (in_array(strtoupper(getCurrentCurrency()), $currency))
                            <div class="mt-5 paystackPayment proceed-to-cash-payment d-none">
                                <button type="button" class="btn btn-primary text-nowrap paymentByPaystack"
                                    data-id="{{ $subscriptionsPricingPlan->id }}"
                                    data-plan-price="{{ $subscriptionsPricingPlan->price }}">
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
                            <button type="button" class="btn btn-primary text-nowrap paymentByflutterWave"
                                data-id="{{ $subscriptionsPricingPlan->id }}"
                                data-plan-price="{{ $subscriptionsPricingPlan->price }}">
                                {{ __('messages.subscription_plans.pay_or_switch_plan') }}</button>
                        </div>
                    @endif

                </div>
            </div>
        </section>
        <!-- end-plan-section -->

        <!-- start subscribe-section -->
        <section class="subscribe-section py-120 bg-light">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-6 mb-lg-0  mb-3">
                        <div class="subscribe-text">
                            <h2 class="mb-0">{{ __('messages.landing.subscribe_our_newsletter') }}</h2>
                        </div>
                    </div>
                    <div class="col-lg-6 ">
                        <div class="email-box">
                            <input type="email" class="fs-6" placeholder="Enter Email Address">
                            <button type="button"
                                class="btn btn-primary d-none d-sm-block">{{ __('messages.landing.subscribe') }}</button>
                            <button type="button"
                                class="btn btn-primary d-flex align-items-center justify-content-center px-3 d-sm-none">
                                <i class="fa-solid fa-paper-plane"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end subscribe-section -->
        {{ Form::hidden('getLoggedInUserdata', getLoggedInUser(), ['class' => 'getLoggedInUser']) }}
        {{ Form::hidden('logInUrl', url('login'), ['class' => 'logInUrl']) }}
        {{ Form::hidden('fromPricing', $fromScreen, ['class' => 'fromPricing']) }}
        {{ Form::hidden('makePaymentURL', route('purchase-subscription'), ['class' => 'makePaymentURL']) }}
        {{ Form::hidden('subscribeText', __('messages.subscription_pricing_plans.choose_plan'), ['class' => 'subscribeText']) }}
        {{ Form::hidden('toastData', json_encode(session('toast-data')), ['class' => 'toastData']) }}
        {{ Form::hidden('subscriptionPlans', route('landing-home'), ['class' => 'subscriptionPlans']) }}
        {{ Form::hidden('makeRazorpayURl', route('razorpay.purchase.subscription'), ['class' => 'makeRazorpayURl']) }}
        {{ Form::hidden('razorpayPaymentFailed', route('razorpay.failed'), ['class' => 'razorpayPaymentFailed']) }}
        {{ Form::hidden('cashPaymentUrl', route('cash.pay.success'), ['class' => 'cashPaymentUrl']) }}
        {{ Form::hidden('razorpayDataKey', config('payments.razorpay.key'), ['class' => 'razorpayDataKey']) }}
        {{--        {{ Form::hidden('razorpayDataAmount', 1, ['class' => 'razorpayDataKey']) }} --}}
        {{--        {{ Form::hidden('razorpayDataCurrency', 'INR', ['class' => 'razorpayDataCurrency']) }} --}}
        {{ Form::hidden('razorpayDataName', getAppName(), ['class' => 'razorpayDataName']) }}
        {{ Form::hidden('razorpayDataImage', asset(getLogoUrl()), ['class' => 'razorpayDataImage']) }}
        {{ Form::hidden('razorpayDataCallBackURL', route('razorpay.success'), ['class' => 'razorpayDataCallBackURL']) }}
    </div>

@endsection
@section('page_scripts')
    {{--    <script src="{{ asset('landing_front/js/jquery.toast.min.js') }}"></script> --}}
    {{--    <script src="{{ mix('assets/js/third-party.js') }}"></script> --}}
    {{--    <script src="{{ mix('assets/js/custom/custom.js') }}"></script> --}}
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var selectize = $("#paymentType").selectize();
        });
    </script>
    <script src="{{ asset('web_front/js/selectize.min.js') }}"></script>
    <script src="//js.stripe.com/v3/"></script>
    <script src="//checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        $('.payment-type').selectize();
        {{-- let getLoggedInUserdata = "{{ getLoggedInUser() }}" --}}
        {{-- let logInUrl = "{{ url('login') }}" --}}
        {{-- let fromPricing = "{{ $fromScreen }}" --}}
        {{-- let makePaymentURL = "{{ route('purchase-subscription') }}"; --}}
        {{-- let subscribeText = "{{ __('messages.subscription_pricing_plans.choose_plan') }}"; --}}
        let stripe = ''
        if (('{{ superAdminStripeApiKey() }}') !== '') {
            stripe = Stripe('{{ superAdminStripeApiKey() }}')
        }
        {{-- let toastData = JSON.parse('@json(session('toast-data'))'); --}}
        {{-- let subscriptionPlans = "{{ route('landing-home') }}"; --}}
        {{-- let makeRazorpayURl = "{{ route('razorpay.purchase.subscription') }}"; --}}
        {{-- let razorpayPaymentFailed = "{{ route('razorpay.failed') }} "; --}}
        {{-- let razorpayPaymentFailedModal = "{{ route('razorpay.failed.modal') }}"; --}}
        {{-- let cashPaymentUrl = "{{ route('cash.pay.success') }}"; --}}
        // let options = {
        //     'key': $('.razorpayDataKey').val(),
        //     'amount': 1, //  100 refers to 1
        //     'currency': 'INR',
        //     'name': $('.razorpayDataName').val(),
        //     'order_id': '',
        //     'description': '',
        //     'image': $('.razorpayDataImage').val(), // logo here
        //     'callback_url': $('.razorpayDataCallBackURL').val(),
        //     'prefill': {
        //         'email': '', // recipient email here
        //         'name': '', // recipient name here
        //         'contact': '', // recipient phone here
        //     },
        //     'readonly': {
        //         'name': 'true',
        //         'email': 'true',
        //         'contact': 'true',
        //     },
        //     'modal': {
        //         'ondismiss': function () {
        //             $.ajax({
        //                 type: 'POST',
        //                 url: $('.razorpayPaymentFailed').val(),
        //                 success: function (result) {
        //                     if (result.url) {
        //                         $.toast({
        //                             heading: 'Success',
        //                             icon: 'Success',
        //                             bgColor: '#7603f3',
        //                             textColor: '#ffffff',
        //                             text: 'Payment not completed.',
        //                             position: 'top-right',
        //                             stack: false,
        //                         })
        //                         setTimeout(function () {
        //                             window.location.href = result.url
        //                         }, 3000)
        //                     }
        //                 },
        //                 error: function (result) {
        //                     displayErrorMessage(result.responseJSON.message)
        //                 },
        //             })
        //         },
        //     },
        // }
    </script>
    {{--    <script src="{{ mix('assets/js/subscriptions/subscription.js') }}"></script> --}}
    {{--    <script src="{{ mix('assets/js/subscriptions/payment-message.js') }}"></script> --}}
@endsection
