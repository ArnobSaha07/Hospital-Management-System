<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Laracasts\Flash\Flash;
use Stripe\Exception\ApiErrorException;

/**
 * Class SubscriptionController
 */
class SubscriptionController extends AppBaseController
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepo;

    public function __construct(SubscriptionRepository $subscriptionRepo)
    {
        $this->subscriptionRepo = $subscriptionRepo;
    }

    /**
     * @return mixed
     *
     * @throws ApiErrorException
     */
    public function purchaseSubscription(Request $request)
    {
        $subscriptionPlanId = $request->get('plan_id');
        $result = $this->subscriptionRepo->purchaseSubscriptionForStripe($subscriptionPlanId);

        // returning from here if the plan is free.
        if (isset($result['status']) && $result['status'] == true) {
            return $this->sendSuccess($result['subscriptionPlan']->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'));
        } else {
            if (isset($result['status']) && $result['status'] == false) {
                return $this->sendError(__('messages.flash.cannot_switch'));
            }
        }

        return $this->sendResponse($result, __('messages.flash.session_created'));
    }

    /**
     * @return Application|RedirectResponse|Redirector
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function paymentSuccess(Request $request): RedirectResponse
    {
        /** @var SubscriptionRepository $subscriptionRepo */
        $subscriptionRepo = app(SubscriptionRepository::class);
        $subscription = $subscriptionRepo->paymentUpdate($request);
        Flash::success($subscription->subscriptionPlan->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'));
        $toastData = [
            'toastType' => 'success',
            'toastMessage' => $subscription->subscriptionPlan->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'),
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

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function handleFailedPayment(): RedirectResponse
    {
        $subscriptionPlanId = session('subscription_plan_id');
        /** @var SubscriptionRepository $subscriptionRepo */
        $subscriptionRepo = app(SubscriptionRepository::class);
        $subscriptionRepo->paymentFailed($subscriptionPlanId);
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

    public function phonePayInit(Request $request)
    {
        $input = $request->all();
        $currency = ['INR'];

        if(!in_array(strtoupper(getCurrentCurrency()),$currency)){
            return $this->sendError(__('messages.phonepe.currency_allowed'));
        }

        $result = $this->subscriptionRepo->phonePePayment($input);

        return $this->sendResponse(['url' => $result],'PhonePe created successfully');
    }

    public function subscriptionPhonePePaymentSuccess(Request $request)
    {
        $subscription = $this->subscriptionRepo->phonePePaymentSuccess($request->all());

        Flash::success($subscription->subscriptionPlan->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'));

        if (session('from_pricing') == 'landing.home') {
            return redirect(route('landing-home'));
        } elseif (session('from_pricing') == 'landing.about.us') {
            return redirect(route('landing.about.us'));
        } elseif (session('from_pricing') == 'landing.services') {
            return redirect(route('landing.services'));
        } elseif (session('from_pricing') == 'landing.pricing') {
            return redirect(route('landing.pricing'));
        } else {
            return redirect(route('subscription.pricing.plans.index'));
        }
    }

    public function setFlutterWaveConfig()
    {
        $flutterwavePublicKey = getSuperAdminPaymentCredentials('flutterwave_key');
        $flutterwaveSecretKey = getSuperAdminPaymentCredentials('flutterwave_secret');

        if(!$flutterwavePublicKey && !$flutterwaveSecretKey){
            return $this->sendError(__('messages.flutterwave.set_flutterwave_credential'));
        }

        config([
            'flutterwave.publicKey' => $flutterwavePublicKey,
            'flutterwave.secretKey' => $flutterwaveSecretKey,
        ]);
    }

    public function flutterWavePayment(Request $request)
    {
        $input = $request->all();

        if(!in_array(strtoupper(getCurrentCurrency()), flutterWaveSupportedCurrencies())){
            return $this->sendError(__('messages.flutterwave.currency_allowed'));
        }

        $this->setFlutterWaveConfig();

        $result = $this->subscriptionRepo->flutterWavePayment($input);

        return $this->sendResponse(['url' => $result],'Flutterwave created successfully');
    }

    public function flutterWavePaymentSuccess(Request $request)
    {
        if($request->status == 'cancelled'){

            $subscriptionPlan = Subscription::findOrFail($request->subscriptionId);
            $subscriptionPlan->delete();

            Flash::error(__('messages.new_change.payment_fail'));

            if (session('from_pricing') == 'landing.home') {
                return redirect(route('landing-home'));
            } elseif (session('from_pricing') == 'landing.about.us') {
                return redirect(route('landing.about.us'));
            } elseif (session('from_pricing') == 'landing.services') {
                return redirect(route('landing.services'));
            } elseif (session('from_pricing') == 'landing.pricing') {
                return redirect(route('landing.pricing'));
            } else {
                return redirect(route('subscription.pricing.plans.index'));
            }
        }

        $this->setFlutterWaveConfig();

        $subscription = $this->subscriptionRepo->flutterWaveSuccess($request->all());

        Flash::success($subscription->subscriptionPlan->name.' '.__('messages.subscription_pricing_plans.has_been_subscribed'));

        if (session('from_pricing') == 'landing.home') {
            return redirect(route('landing-home'));
        } elseif (session('from_pricing') == 'landing.about.us') {
            return redirect(route('landing.about.us'));
        } elseif (session('from_pricing') == 'landing.services') {
            return redirect(route('landing.services'));
        } elseif (session('from_pricing') == 'landing.pricing') {
            return redirect(route('landing.pricing'));
        } else {
            return redirect(route('subscription.pricing.plans.index'));
        }
    }
}
