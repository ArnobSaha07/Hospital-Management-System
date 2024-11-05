<?php

namespace App\Http\Controllers;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Http\Requests\CreatePaytmDetailRequest;
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

class PaytmController extends AppBaseController
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

    // display a form for payment

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function initiate(Request $request)
    {
        $input = $request->all();

        session(['from_pricing' => $request->get('from_pricing')]);

        $planId = $request->get('planId');

        $subscriptionsPricingPlan = SubscriptionPlan::findOrFail($planId);

        if (strtolower($subscriptionsPricingPlan->currency) != 'inr') {
            Flash::error(__('messages.new_change.paytm_support_indian'));

            return redirect(route('subscription.pricing.plans.index'));
        }

        $isPaytm = 1;

        $amountToPay = $this->subscriptionRepository->manageSubscription($planId, $isPaytm);

        return view('paytm.index', compact('amountToPay', 'planId'));
    }

    public function payment(CreatePaytmDetailRequest $request)
    {
        $input = $request->all();

        $paytm = 2;
        $data = $this->subscriptionRepository->manageSubscription($request->get('planId'), $paytm);

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

        $subscription = $data['subscription'];

        $payment = PaytmWallet::with('receive');

        $payment->prepare([
            'order' => $subscription->id, // 1 should be your any data id
            'user' => getLoggedInUserId(), // any user id
            'mobile_number' => $input['mobile'],
            'email' => getLoggedInUser()->email, // your user email address
            'amount' => $data['amountToPay'],
            'callback_url' => route('paytm.callback'), // callback URL
        ]);

        return $payment->receive();

    }

    /**
     * Obtain the payment information.
     */
    public function paymentCallback(): RedirectResponse
    {
        $paytmPaymentTransaction = PaytmWallet::with('receive');
        $response = $paytmPaymentTransaction->response();
        $subscriptionId = $paytmPaymentTransaction->getOrderId();
        $paytmPaymentTransaction->getTransactionId();

        if ($paytmPaymentTransaction->isSuccessful()) {

            try {

                Subscription::findOrFail($subscriptionId)->update(['status' => Subscription::ACTIVE]);

                // De-Active all other subscription
                Subscription::whereUserId(getLoggedInUserId())
                    ->where('id', '!=', $subscriptionId)
                    ->update([
                        'status' => Subscription::INACTIVE,
                    ]);

                $transaction = Transaction::create([
                    'transaction_id' => $response['TXNID'],
                    'payment_type' => Subscription::TYPE_PAYTM,
                    'amount' => $response['TXNAMOUNT'],
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
                    'amount' => $response['TXNAMOUNT'],
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

        } else {
            if ($paytmPaymentTransaction->isFailed()) {

                $subscription = session('subscription_plan_id');
                if ($subscription) {
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

            }
        }

        return redirect(route('subscription.pricing.plans.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function failed(): RedirectResponse
    {
        $subscription = session('subscription_plan_id');
        if ($subscription) {
            $subscriptionPlan = Subscription::find($subscription);
            if ($subscriptionPlan) {
                $subscriptionPlan->delete();
            }

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

        return redirect(route('subscription.pricing.plans.index'));
    }
}
