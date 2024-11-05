<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IpdPatientDepartment;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\StripeRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use View;

class PatientStripeController extends AppBaseController
{
    /**
     * @var StripeRepository
     */
    private $stripeRepository;

    public function __construct(StripeRepository $stripeRepository)
    {
        $this->stripeRepository = $stripeRepository;
    }

    /**
     * @throws ApiErrorException
     */
    public function createSession(Request $request): JsonResponse
    {
        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $amount = $request->get('amount');
        $ipdNumber = $request->get('ipdNumber');
        $ipdPaientId = IpdPatientDepartment::whereIpdNumber($ipdNumber)->first()->id;

        $user = $request->user();
        $userEmail = $user->email;
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
            'customer_email' => $userEmail,
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => 'BILL OF PATIENT with IPD #'.$ipdNumber,
                        ],
                        'unit_amount' => in_array(strtoupper(getCurrentCurrency()), zeroDecimalCurrencies()) ? $amount  :  $amount * 100,
                        'currency' => strtoupper(getCurrentCurrency()),
                    ],
                    'quantity' => 1,
                    'description' => 'BILL OF PATIENT with IPD #'.$ipdNumber,
                ],
            ],
            'client_reference_id' => $ipdPaientId,
            'mode' => 'payment',
            'success_url' => url('stripe-payment-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url('stripe-failed-payment?error=payment_cancelled'),
        ]);
        $result = [
            'sessionId' => $session['id'],
        ];

        return $this->sendResponse($result, __('messages.flash.session_created'));
    }

    /**
     * @return RedirectResponse|RedirectorStripe::setApiKey(<API-KEY>)
     *
     * @throws Exception
     */
    public function paymentSuccess(Request $request): RedirectResponse
    {
        $sessionId = $request->get('session_id');

        if (empty($sessionId)) {
            throw new UnprocessableEntityHttpException('session_id required');
        }

        $this->stripeRepository->patientBillingPaymentSuccess($sessionId);

        Flash::success(__('messages.flash.your_payment_success'));

        return redirect(route('patient.ipd'));
    }

    /**
     * @return Factory|View
     */
    public function handleFailedPayment(): RedirectResponse
    {
        Flash::error(__('messages.flash.your_payment_failed'));

        return redirect(route('patient.ipd'));
    }
}
