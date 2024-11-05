<?php

namespace App\Http\Controllers;

use App\Mail\NotifyMailSuperAdminForSubscribeHospital;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Repositories\SubscriptionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends AppBaseController
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * PaypalController constructor.
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @return array|JsonResponse
     */
    public function onBoard(Request $request)
    {
        $subscriptionsPricingPlan = SubscriptionPlan::findOrFail($request->get('planId'));
        if (
            $subscriptionsPricingPlan->currency != null && ! in_array(
                strtoupper($subscriptionsPricingPlan->currency),
                getPayPalSupportedCurrencies()
            )
        ) {
            Flash::error(__('messages.flash.currency_not_supported_paypal'));

            if (session('from_pricing') == 'landing.home') {
                return response()->json(['url' => route('landing-home')]);
            } elseif (session('from_pricing') == 'landing.about.us') {
                return response()->json(['url' => route('landing.about.us')]);
            } elseif (session('from_pricing') == 'landing.services') {
                return response()->json(['url' => route('landing.services')]);
            } elseif (session('from_pricing') == 'landing.pricing') {
                return response()->json(['url' => route('landing.pricing')]);
            } else {
                return response()->json(['url' => route('subscription.pricing.plans.index')]);
            }
        }

        $data = $this->subscriptionRepository->manageSubscription($request->get('planId'));

        if (! isset($data['plan'])) { // 0 amount plan or try to switch the plan if it is in trial mode
            // returning from here if the plan is free.
            if (isset($data['status']) && $data['status'] == true) {
                return $this->sendSuccess($data['subscriptionPlan']->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'));
            } else {
                if (isset($data['status']) && $data['status'] == false) {
                    return $this->sendError(__('messages.flash.cannot_switch'));
                }
            }
        }

        $subscriptionsPricingPlan = $data['plan'];
        $subscription = $data['subscription'];

        $paypalKey = getSuperAdminSettingKeyValue('paypal_key');
        $paypalSecret = getSuperAdminSettingKeyValue('paypal_secret');
        if (isset($paypalKey) && ! is_null($paypalKey) && isset($paypalSecret) && ! is_null($paypalSecret)) {
            config([
                'paypal.mode' => getSuperAdminSettingKeyValue('paypal_mode'),
                'paypal.sandbox.client_id' => getSuperAdminSettingKeyValue('paypal_key'),
                'paypal.sandbox.client_secret' => getSuperAdminSettingKeyValue('paypal_secret'),
                'paypal.live.client_id' => getSuperAdminSettingKeyValue('paypal_key'),
                'paypal.live.client_secret' => getSuperAdminSettingKeyValue('paypal_secret'),
            ]);

        }
        $provider = new PayPalClient;

        $provider->getAccessToken();

        //        $clientId = config('payments.paypal.client_id');
        //        $clientSecret = config('payments.paypal.client_secret');
        //        $mode = config('payments.paypal.mode');
        //
        //        if ($mode == 'live') {
        //            $environment = new ProductionEnvironment($clientId, $clientSecret);
        //        } else {
        //            $environment = new SandboxEnvironment($clientId, $clientSecret);
        //        }

        //        $client = new PayPalHttpClient($environment);

        //        $request = new OrdersCreateRequest();
        //        $request->prefer('return=representation');

        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $subscription->id,
                    'amount' => [
                        'value' => $data['amountToPay'],
                        'currency_code' => $subscriptionsPricingPlan->currency,
                    ],
                ],
            ],
            'application_context' => [
                'cancel_url' => route('paypal.failed'),
                'return_url' => route('paypal.success'),
            ],
        ];

        //        $order = $client->execute($request);

        $order = $provider->createOrder($data);
        // dd($order);

        session(['payment_type' => request()->get('payment_type')]);

        return response()->json(['link' => $order['links'][1]['href'], 'status' => 201]);

        //        return response()->json($order);
    }

    public function failed(): RedirectResponse
    {
        $subscription = session('subscription_plan_id');
        $subscriptionPlan = Subscription::findOrFail($subscription);
        $subscriptionPlan->delete();

        Flash::error(__('messages.flash.unable_to_process'));
        $toastData = [
            'toastType' => 'error',
            'toastMessage' => __('messages.flash.unable_to_process'),
        ];

        if (session('from_pricing') == 'landing.home') {
            return redirect(route('landing-home'))->with('toast-data', $toastData);
        } elseif (session('from_pricing') == 'landing.about.us') {
            return redirect(route('landing.about.us'))->with('toast-data', $toastData);
        } elseif (session('from_pricing') == 'landing.services') {
            return redirect(route('landing.services'))->with('toast-data', $toastData);
        } elseif (session('from_pricing') == 'landing.pricing') {
            return redirect(route('landing.pricing'))->with('toast-data', $toastData);
        } else {
            return redirect(route('subscription.pricing.plans.index'));
        }
    }

    public function success(Request $request): RedirectResponse
    {
        //        $clientId = config('payments.paypal.client_id');
        //        $clientSecret = config('payments.paypal.client_secret');
        //        $mode = config('payments.paypal.mode');
        //
        //        if ($mode == 'live') {
        //            $environment = new ProductionEnvironment($clientId, $clientSecret);
        //        } else {
        //            $environment = new SandboxEnvironment($clientId, $clientSecret);
        //        }
        //        $client = new PayPalHttpClient($environment);

        $paypalKey = getSuperAdminSettingKeyValue('paypal_key');
        $paypalSecret = getSuperAdminSettingKeyValue('paypal_secret');
        if (isset($paypalKey) && ! is_null($paypalKey) && isset($paypalSecret) && ! is_null($paypalSecret)) {
            config([
                'paypal.mode' => getSuperAdminSettingKeyValue('paypal_mode'),
                'paypal.sandbox.client_id' => getSuperAdminSettingKeyValue('paypal_key'),
                'paypal.sandbox.client_secret' => getSuperAdminSettingKeyValue('paypal_secret'),
                'paypal.live.client_id' => getSuperAdminSettingKeyValue('paypal_key'),
                'paypal.live.client_secret' => getSuperAdminSettingKeyValue('paypal_secret'),
            ]);

        }

        $provider = new PayPalClient; // To use express checkout.

        $provider->getAccessToken();

        $token = $request->get('token');

        $orderInfo = $provider->showOrderDetails($token);

        // Here, OrdersCaptureRequest() creates a POST request to /v2/checkout/orders
        //        $request = new OrdersCaptureRequest($request->get('token'));
        //        $request->prefer('return=representation');
        try {
            // Call API with your client and get a response for your call

            //            $response = $client->execute($request);

            $response = $provider->capturePaymentOrder($token);

            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            //            $subscriptionId = $response->result->purchase_units[0]->reference_id;
            //            $subscriptionAmount = $response->result->purchase_units[0]->amount->value;
            //            $transactionID = $response->result->id;     // $response->result->id gives the orderId of the order created above

            $subscriptionId = $response['purchase_units'][0]['reference_id'];
            $subscriptionAmount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $transactionID = $response['purchase_units'][0]['payments']['captures'][0]['id'];

            Subscription::findOrFail($subscriptionId)->update(['status' => Subscription::ACTIVE]);
            // De-Active all other subscription
            Subscription::whereUserId(getLoggedInUserId())
                ->where('id', '!=', $subscriptionId)
                ->update([
                    'status' => Subscription::INACTIVE,
                ]);

            $transaction = Transaction::create([
                'transaction_id' => $transactionID,
                'payment_type' => session('payment_type'),
                'amount' => $subscriptionAmount,
                'user_id' => getLoggedInUserId(),
                'status' => Subscription::ACTIVE,
                'meta' => json_encode($response),
            ]);

            // updating the transaction id on the subscription table
            $subscription = Subscription::with('subscriptionPlan')->findOrFail($subscriptionId);
            $subscription->update(['transaction_id' => $transaction->id]);

            Flash::success($subscription->subscriptionPlan->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'));
            $toastData = [
                'toastType' => 'success',
                'toastMessage' => $subscription->subscriptionPlan->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'),
            ];

            $mailData = [
                'amount' => $subscriptionAmount,
                'user_name' => getLoggedInUser()->full_name,
                'plan_name' => $subscription->subscriptionPlan->name,
                'start_date' => $subscription->starts_at,
                'end_date' => $subscription->ends_at,
            ];

            Mail::to(getLoggedInUser()->email)
            ->send(new NotifyMailSuperAdminForSubscribeHospital('emails.hospital_subscription_mail',
                __('messages.new_change.subscription_mail'),
                $mailData));

            if (session('from_pricing') == 'landing.home') {
                return redirect(route('landing-home'))->with('toast-data', $toastData);
            } elseif (session('from_pricing') == 'landing.about.us') {
                return redirect(route('landing.about.us'))->with('toast-data', $toastData);
            } elseif (session('from_pricing') == 'landing.services') {
                return redirect(route('landing.services'))->with('toast-data', $toastData);
            } elseif (session('from_pricing') == 'landing.pricing') {
                return redirect(route('landing.pricing'))->with('toast-data', $toastData);
            } else {
                return redirect(route('subscription.pricing.plans.index'));
            }
        } catch (HttpException $ex) {
            echo $ex->statusCode;
            print_r($ex->getMessage());
        }
    }
}
