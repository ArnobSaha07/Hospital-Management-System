<?php

namespace App\Http\Controllers;

use Laracasts\Flash\Flash;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\SubscriptionRepository;
use Unicodeveloper\Paystack\Facades\Paystack;
use App\Mail\NotifyMailSuperAdminForSubscribeHospital;

class PaystackController extends Controller
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

    public function paystackConfig()
    {
        config(['paystack.publicKey' => getSuperAdminPaymentCredentials('paystack_key'),
            'paystack.secretKey' => getSuperAdminPaymentCredentials('paystack_secret'),
            'paystack.paymentUrl' => 'https://api.paystack.co',
        ]);
    }

    public function redirectToGateway(Request $request)
    {
        $this->paystackConfig();

        $subscriptionsPricingPlan = SubscriptionPlan::findOrFail($request->get('planId'));

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

        try {
            $request->merge([
                'email' => getLoggedInUser()->email,
                'orderID' => $subscription->id,
                'amount' => ($data['amountToPay'] * 100),
                'quantity' => 1,
                'currency' => strtoupper(getCurrentCurrency()),
                'reference' => Paystack::genTranxRef(),
                'metadata' => json_encode(['subscription_id' => $subscription->id]),
            ]);
            $authorizationUrl = Paystack::getAuthorizationUrl();

            return $authorizationUrl->redirectNow();

        } catch (\Exception $e) {
            dd($e->getMessage());
            Flash::error(__('messages.new_change.payment_fail'));

            return Redirect::back()->withMessage([
                'msg' => __('messages.new_change.paystack_token_expired'), 'type' => 'error',
            ]);
        }

    }

    public function handleGatewayCallback(Request $request): RedirectResponse
    {
        $this->paystackConfig();

        $response = Paystack::getPaymentData();

        try {
            $subscriptionId = $response['data']['metadata']['subscription_id'];
            $subscriptionAmount = $response['data']['amount'] / 100;
            $transactionID = $response['data']['id'];

            Subscription::findOrFail($subscriptionId)->update(['status' => Subscription::ACTIVE]);
            // De-Active all other subscription
            Subscription::whereUserId(getLoggedInUserId())
                ->where('id', '!=', $subscriptionId)
                ->update([
                    'status' => Subscription::INACTIVE,
                ]);

            $transaction = Transaction::create([
                'transaction_id' => $transactionID,
                'payment_type' => Transaction::TYPE_PAYSTACK,
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
