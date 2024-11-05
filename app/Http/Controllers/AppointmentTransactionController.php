<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Laracasts\Flash\Flash;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Illuminate\Http\JsonResponse;
use App\Repositories\AppointmentTransactionRepository;
use Auth;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Illuminate\Http\RedirectResponse;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Unicodeveloper\Paystack\Facades\Paystack;

class AppointmentTransactionController extends AppBaseController
{
    /** @var AppointmentTransactionRepository */
    private $appointmentTransactionRepository;

    public function __construct(AppointmentTransactionRepository $appointmentTransactionRepo)
    {
        $this->appointmentTransactionRepository = $appointmentTransactionRepo;
    }

    public function  index()
    {
        return view('appointment_transaction.index');
    }

    public function createStripeSession(Request $request): JsonResponse
    {
        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $amount = $request->get('amount');
        $appointmentId = $request->get('appointment_id');
        $appointment = Appointment::find($appointmentId);

        $data = [
            'appointment_id' => $appointmentId,
            'amount' => $amount,
            'payment_mode' => $request->get('payment_type'),
        ];

        $stripeKey = Setting::whereTenantId($tenantId)
            ->where('key', '=', 'stripe_secret')
            ->first();
        if (! empty($stripeKey->value)) {
            setStripeApiKey($tenantId);
        } else {
            return $this->sendError(__('messages.new_change.provide_stripe_key'));
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $appointment->patient->patientUser->email,
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => 'Payment for Patient bill',
                        ],
                        'unit_amount' => $amount * 100,
                        'currency' => getCurrentCurrency(),
                    ],
                    'quantity' => 1,
                    'description' => 'Payment for Patient bill',
                ],
            ],
            'client_reference_id' => $appointmentId,
            'mode' => 'payment',
            'success_url' => url('appointment-stripe-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('appointment.stripe.failure', ['appointment_id' => $appointmentId]),
            'metadata' => $data,
            // 'cancel_url' => url('stripe-failed-payment?error=payment_cancelled'),
        ]);
        $result = [
            'sessionId' => $session['id'],
        ];

        return $this->sendResponse($result, __('messages.flash.session_created'));
    }

    public function appointmentStripePaymentSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (empty($sessionId)) {
            throw new UnprocessableEntityHttpException('session_id required');
        }

        $this->appointmentTransactionRepository->appointmentStripePaymentSuccess($request->all());

        Flash::success(__('messages.flash.your_payment_success'));

        return redirect(route('appointments.index'));
    }

    public function webAppointmentStripePaymentSuccess(Request $request)
    {
        $this->appointmentTransactionRepository->appointmentStripePaymentSuccess($request->all());

        Flash::success(__('messages.flash.your_payment_success'));

        if(!getLoggedInUser()->hasRole('Admin')){
            $tenant = Auth::user()->tenant_id;
            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
        }else{
            $user = Auth::user();
        }
        return redirect(route('appointment',['username' => $user->username] ));
    }

    public function appointmentRazorpayPayment(Request $request)
    {
        $result = $this->appointmentTransactionRepository->TransactionRazorpayPayment($request->all());

        return $this->sendResponse($result, 'order created');
    }

    public function appointmentRazorpayPaymentSuccess(Request $request)
    {
        $this->appointmentTransactionRepository->TransactionRazorpayPaymentSuccess($request->all());

        Flash::success(__('messages.flash.your_payment_success'));

        return redirect(route('appointments.index'));
    }

    public function paypalOnBoard(Request $request)
    {
        if (! in_array(strtoupper(getCurrentCurrency()), getPayPalSupportedCurrencies())) {

            Appointment::whereId($request->get('appointment_id'))->delete();

            return $this->sendError(__('messages.flash.currency_not_supported_paypal'));
        }

        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $amount = $request->get('amount');
        $appointmentId = $request->get('appointment_id');

        $mode = getSelectedPaymentGateway('paypal_mode');
        $clientId = getSelectedPaymentGateway('paypal_client_id');
        $clientSecret = getSelectedPaymentGateway('paypal_secret');

        config([
            'paypal.mode' => $mode,
            'paypal.sandbox.client_id' => $clientId,
            'paypal.sandbox.client_secret' => $clientSecret,
            'paypal.live.client_id' => $clientId,
            'paypal.live.client_secret' => $clientSecret,
        ]);

        $provider = new PayPalClient();
        $provider->getAccessToken();

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $appointmentId,
                    'amount' => [
                        'value' => $amount,
                        'currency_code' => getCurrentCurrency(),
                    ],
                ],
            ],
            'application_context' => [
                'cancel_url' => route('appointment.paypal.failed',['appointment_id' => $appointmentId]),
                'return_url' => route('appointment.paypal.success'),
            ],
        ];

        $order = $provider->createOrder($data);

        return response()->json(['url' => $order['links'][1]['href'], 'status' => 201]);
    }

    public function paypalSuccess(Request $request): RedirectResponse
    {

        $mode = getSelectedPaymentGateway('paypal_mode');
        $clientId = getSelectedPaymentGateway('paypal_client_id');
        $clientSecret = getSelectedPaymentGateway('paypal_secret');

        config([
            'paypal.mode' => $mode,
            'paypal.sandbox.client_id' => $clientId,
            'paypal.sandbox.client_secret' => $clientSecret,
            'paypal.live.client_id' => $clientId,
            'paypal.live.client_secret' => $clientSecret,
        ]);

        $provider = new PayPalClient;

        $provider->getAccessToken();

        $token = $request->get('token');

        $response = $provider->capturePaymentOrder($token);

        $this->appointmentTransactionRepository->paypalPaymentSuccess($response);

        Flash::success(__('messages.flash.your_payment_success'));

        return redirect(route('appointments.index'));
    }

    public function paypalFailed(Request $request): RedirectResponse
    {
        $appointmentId = $request['appointment_id'];
        if($appointmentId){
            Appointment::find($appointmentId)->delete();
        }
        Flash::error(__('messages.flash.your_payment_failed'));

        return redirect(route('appointments.index'));
    }

    public function webCreateStripeSession(Request $request): JsonResponse
    {
        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $amount = $request->get('amount');
        $appointmentId = $request->get('appointment_id');
        $appointment = Appointment::with('patient.user')->find($appointmentId);
        $data = [
            'appointment_id' => $appointmentId,
            'amount' => $amount,
            'payment_mode' => $request->get('payment_type'),
        ];

        $stripeKey = Setting::whereTenantId($tenantId)
            ->where('key', '=', 'stripe_secret')
            ->first();
        if (! empty($stripeKey->value)) {
            setStripeApiKey($tenantId);
        } else {
            return $this->sendError(__('messages.new_change.provide_stripe_key'));
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'customer_email' => $appointment->patient->patientUser->email,
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => 'Payment for Patient bill',
                        ],
                        'unit_amount' => $amount * 100,
                        'currency' => getCurrentCurrency(),
                    ],
                    'quantity' => 1,
                    'description' => 'Payment for Patient bill',
                ],
            ],
            'client_reference_id' => $appointmentId,
            'mode' => 'payment',
            'success_url' => url('web-appointment-stripe-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('web.appointment.stripe.failed', ['appointment_id' => $appointmentId]),
            'metadata' => $data,
            // 'cancel_url' => url('stripe-failed-payment?error=payment_cancelled'),
        ]);
        $result = [
            'sessionId' => $session['id'],
        ];

        return $this->sendResponse($result, __('messages.flash.session_created'));
    }

    public function webAppointmentPaypalOnBoard(Request $request)
    {
        if (! in_array(strtoupper(getCurrentCurrency()), getPayPalSupportedCurrencies())) {

            Appointment::whereId($request->get('appointment_id'))->delete();

            return $this->sendError(__('messages.flash.currency_not_supported_paypal'));
        }

        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $amount = $request->get('amount');
        $appointmentId = $request->get('appointment_id');

        $mode = getSelectedPaymentGateway('paypal_mode');
        $clientId = getSelectedPaymentGateway('paypal_client_id');
        $clientSecret = getSelectedPaymentGateway('paypal_secret');

        config([
            'paypal.mode' => $mode,
            'paypal.sandbox.client_id' => $clientId,
            'paypal.sandbox.client_secret' => $clientSecret,
            'paypal.live.client_id' => $clientId,
            'paypal.live.client_secret' => $clientSecret,
        ]);

        $provider = new PayPalClient();
        $provider->getAccessToken();

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $appointmentId,
                    'amount' => [
                        'value' => $amount,
                        'currency_code' => strtoupper(getCurrentCurrency()),
                    ],
                ],
            ],
            'application_context' => [
                'cancel_url' => route('web.appointment.paypal.failed',['appointment_id' => $appointmentId]),
                'return_url' => route('web.appointment.paypal.success'),
            ],
        ];

        $order = $provider->createOrder($data);
        return response()->json(['url' => $order['links'][1]['href'], 'status' => 201]);
    }

    public function webAppointmentPaypalSuccess(Request $request): RedirectResponse
    {

        $mode = getSelectedPaymentGateway('paypal_mode');
        $clientId = getSelectedPaymentGateway('paypal_client_id');
        $clientSecret = getSelectedPaymentGateway('paypal_secret');

        config([
            'paypal.mode' => $mode,
            'paypal.sandbox.client_id' => $clientId,
            'paypal.sandbox.client_secret' => $clientSecret,
            'paypal.live.client_id' => $clientId,
            'paypal.live.client_secret' => $clientSecret,
        ]);

        $provider = new PayPalClient;

        $provider->getAccessToken();

        $token = $request->get('token');

        $response = $provider->capturePaymentOrder($token);

        $this->appointmentTransactionRepository->paypalPaymentSuccess($response);

        Flash::success(__('messages.flash.your_payment_success'));

        if(!getLoggedInUser()->hasRole('Admin')){
            $tenant = Auth::user()->tenant_id;
            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
        }else{
            $user = Auth::user();
        }
        return redirect(route('appointment',['username' => $user->username] ));
    }

    public function webAppointmentPaypalFailed(Request $request): RedirectResponse
    {
        $appointmentId = $request['appointment_id'];
        if($appointmentId){
            Appointment::find($appointmentId)->delete();
        }

        Flash::error(__('messages.payment.payment_failed'));

        if(!getLoggedInUser()->hasRole('Admin')){
            $tenant = Auth::user()->tenant_id;
            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
        }else{
            $user = Auth::user();
        }
        return redirect(route('appointment',['username' => $user->username] ));
    }

    public function webAppointmentRazorpayPayment(Request $request)
    {
        $result = $this->appointmentTransactionRepository->TransactionRazorpayPayment($request->all());

        return $this->sendResponse($result, 'order created');
    }

    public function WebAppointmentRazorpayPaymentSuccess(Request $request)
    {
        $this->appointmentTransactionRepository->TransactionRazorpayPaymentSuccess($request->all());

        Flash::success(__('messages.flash.your_payment_success'));

        if(!getLoggedInUser()->hasRole('Admin')){
            $tenant = Auth::user()->tenant_id;
            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
        }else{
            $user = Auth::user();
        }
        return redirect(route('appointment',['username' => $user->username] ));
    }


    public function appointmentRazorPayPaymentFailed(Request $request)
    {
        $appointment = Appointment::orderBy('created_at', 'desc')->latest()->first();

        $appointment->delete();

        return $this->sendSuccess(__('messages.payment.payment_failed'));
    }

    public function appointmentStripeFailed(Request $request)
    {
        $appointmentId = $request['appointment_id'];
        if($appointmentId){
            Appointment::find($appointmentId)->delete();
        }
        Flash::error(__('messages.payment.payment_failed'));

        return redirect(route('appointments.index'));
    }

    public function webAppointmentStripeFailed(Request $request)
    {
        $appointmentId = $request['appointment_id'];
        if($appointmentId){
            Appointment::find($appointmentId)->delete();
        }
        Flash::error(__('messages.payment.payment_failed'));

        if(!getLoggedInUser()->hasRole('Admin')){
            $tenant = Auth::user()->tenant_id;
            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
        }else{
            $user = Auth::user();
        }
        return redirect(route('appointment',['username' => $user->username] ));
    }

    public function webAppointmentRazorPayPaymentFailed(Request $request)
    {
        $appointment = Appointment::orderBy('created_at', 'desc')->latest()->first();

        $appointment->delete();
        if(!getLoggedInUser()->hasRole('Admin')){
            $tenant = Auth::user()->tenant_id;
            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
        }else{
            $user = Auth::user();
        }
        return $this->sendSuccess(['message' => __('messages.payment.payment_failed'), 'url' => route('appointment',['username' => $user->username] )]);
    }

    public function setFlutterWaveConfig()
    {
        $flutterwavePublicKey = getPaymentCredentials('flutterwave_public_key');
        $flutterwaveSecretKey = getPaymentCredentials('flutterwave_secret_key');

        if(!$flutterwavePublicKey && !$flutterwaveSecretKey){
            return $this->sendError(__('messages.flutterwave.set_flutterwave_credential'));
        }

        config([
            'flutterwave.publicKey' => $flutterwavePublicKey,
            'flutterwave.secretKey' => $flutterwaveSecretKey,
        ]);
    }

    public function appointmentFlutterWavePayment(Request $request)
    {
        $input = $request->all();

        if(!in_array(strtoupper(getCurrentCurrency()), flutterWaveSupportedCurrencies())){
            return $this->sendError(__('messages.flutterwave.currency_allowed'));
        }

        $this->setFlutterWaveConfig();

        $url = $this->appointmentTransactionRepository->appointmentFlutterWavePayment($input);

        return $this->sendResponse(['url' => $url],'Flutterwave created successfully');
    }

    public function appointmentFlutterWavePaymentSuccess(Request $request)
    {
        if($request->status == 'cancelled'){

            $appointment = Appointment::orderBy('created_at', 'desc')->latest()->first();
            $appointment->delete();

            Flash::error(__('messages.new_change.payment_fail'));

            return redirect(route('appointments.index'));
        }

        $this->setFlutterWaveConfig();

        $this->appointmentTransactionRepository->flutterWaveSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('appointments.index'));
    }

    public function webFlutterWavePayment(Request $request)
    {
        $input = $request->all();

        if(!in_array(strtoupper(getCurrentCurrency()), flutterWaveSupportedCurrencies())){
            return $this->sendError(__('messages.flutterwave.currency_allowed'));
        }

        $this->setFlutterWaveConfig();

        $url = $this->appointmentTransactionRepository->webAppointmentFlutterWavePayment($input);

        return $this->sendResponse(['url' => $url],'Flutterwave created successfully');
    }

    public function webFlutterWavePaymentSuccess(Request $request)
    {
        if($request->status == 'cancelled'){

            $appointmentId = $request['appointmentId'];

            if($appointmentId){
                Appointment::find($appointmentId)->delete();
            }

            Flash::error(__('messages.payment.payment_failed'));

            if(!getLoggedInUser()->hasRole('Admin')){
                $tenant = Auth::user()->tenant_id;
                $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
            }else{
                $user = Auth::user();
            }
            return redirect(route('appointment',['username' => $user->username] ));

        }

        $this->setFlutterWaveConfig();

        $this->appointmentTransactionRepository->flutterWaveSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        if(!getLoggedInUser()->hasRole('Admin')){
            $tenant = Auth::user()->tenant_id;
            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
        }else{
            $user = Auth::user();
        }
        return redirect(route('appointment',['username' => $user->username] ));
    }

    public function phonePayInit(Request $request)
    {
        $input = $request->all();
        $currency = ['INR'];

        if(!in_array(strtoupper(getCurrentCurrency()),$currency)){
            $appointment = Appointment::orderBy('created_at', 'desc')->latest()->first();
            $appointment->delete();

            return $this->sendError(__('messages.phonepe.currency_allowed'));
        }

        $result = $this->appointmentTransactionRepository->phonePePayment($input);

        return $this->sendResponse(['url' => $result],'PhonePe created successfully');
    }

    public function appointmentPhonePePaymentSuccess(Request $request)
    {
        $this->appointmentTransactionRepository->phonePePaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('appointments.index'));
    }

    public function paystackConfig()
    {
        config(['paystack.publicKey' => getPaymentCredentials('paystack_public_key'),
            'paystack.secretKey' => getPaymentCredentials('paystack_secret_key'),
            'paystack.paymentUrl' => 'https://api.paystack.co',
        ]);
    }

    public function appointmentPaystackPayment(Request $request)
    {
        $this->paystackConfig();

        if (!in_array(strtoupper(getCurrentCurrency()),payStackSupportedCurrencies())) {
            Flash::error(__('messages.new_change.paystack_support_zar'));

            return redirect(route('appointments.index'));
        }

        $data = $request->all();
        $data['type'] = 'appointment';
        $amount = $request['data']['amount'];

        session(['appointmentPayStackData' => $request['data']['input']]);

        try {
            $request->merge([
                'email' => getLoggedInUser()->email,
                'orderID' => generateUniquePurchaseNumber(),
                'amount' => $amount * 100,
                'quantity' => 1,
                'currency' => strtoupper(getCurrentCurrency()),
                'reference' => Paystack::genTranxRef(),
                'metadata' => json_encode($data),
            ]);

            $authorizationUrl = Paystack::getAuthorizationUrl();

            return $authorizationUrl->redirectNow();
        } catch (\Exception $e) {
            session()->forget('appointmentPayStackData');
            Flash::error(__('messages.payment.payment_failed'));

            return redirect(route('appointments.index'));
        }

    }

    public function webAppointmentPaystackPayment(Request $request)
    {
        $this->paystackConfig();

        if (!in_array(strtoupper(getCurrentCurrency()),payStackSupportedCurrencies())) {
            Flash::error(__('messages.new_change.paystack_support_zar'));

            if(!getLoggedInUser()->hasRole('Admin')){
                $tenant = Auth::user()->tenant_id;
                $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
            }else{
                $user = Auth::user();
            }
            return redirect(route('appointment',['username' => $user->username] ));
        }
        $data = $request->all();
        $data['type'] = 'webAppointment';
        $amount = $request['data']['amount'];

        session(['appointmentPayStackData' => $request['data']['input']]);

        try {
            $request->merge([
                'email' => getLoggedInUser()->email,
                'orderID' => generateUniquePurchaseNumber(),
                'amount' => $amount * 100,
                'quantity' => 1,
                'currency' => strtoupper(getCurrentCurrency()),
                'reference' => Paystack::genTranxRef(),
                'metadata' => json_encode($data),
            ]);

            $authorizationUrl = Paystack::getAuthorizationUrl();

            return $authorizationUrl->redirectNow();
        } catch (\Exception $e) {
            session()->forget('appointmentPayStackData');
            Flash::error(__('messages.payment.payment_failed'));

            if(!getLoggedInUser()->hasRole('Admin')){
                $tenant = Auth::user()->tenant_id;
                $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
            }else{
                $user = Auth::user();
            }
            return redirect(route('appointment',['username' => $user->username] ));
        }

    }

    public function wenPhonePayInit(Request $request)
    {
        $input = $request->all();
        $currency = ['INR'];

        if(!in_array(strtoupper(getCurrentCurrency()),$currency)){
            $appointment = Appointment::orderBy('created_at', 'desc')->latest()->first();
            $appointment->delete();

            return $this->sendError(__('messages.phonepe.currency_allowed'));
        }

        $result = $this->appointmentTransactionRepository->webPhonePePayment($input);

        return $this->sendResponse(['url' => $result],'PhonePe created successfully');
    }

    public function webPhonePePaymentSuccess(Request $request)
    {
        $this->appointmentTransactionRepository->phonePePaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        if(!getLoggedInUser()->hasRole('Admin')){
            $tenant = Auth::user()->tenant_id;
            $user = User::whereTenantId($tenant)->whereNotNull('username')->first();
        }else{
            $user = Auth::user();
        }
        return redirect(route('appointment',['username' => $user->username] ));

    }
}
