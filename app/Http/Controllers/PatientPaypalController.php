<?php

namespace App\Http\Controllers;

use App\Models\IpdPatientDepartment;
use App\Models\User;
use App\Repositories\PatientPaypalRepository;
use Flash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

/**
 * Class PatientPaypalController
 */
class PatientPaypalController extends AppBaseController
{
    /**
     * @var PatientPaypalRepository
     */
    private $patientPaypalRepository;

    public function __construct(PatientPaypalRepository $patientPaypalRepository)
    {
        $this->patientPaypalRepository = $patientPaypalRepository;
    }

    public function onBoard(Request $request)
    {
        if (! in_array(strtoupper(getCurrentCurrency()), getPayPalSupportedCurrencies())) {
            return $this->sendError(__('messages.flash.currency_not_supported_paypal'));
        }

        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        $amount = $request->get('amount');
        // $ipdNumber = $request->get('ipdNumber');
        // $ipdPatientId = IpdPatientDepartment::whereIpdNumber($ipdNumber)->first()->id;
        $ipdPatientId = $request->get('ipdId');
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
                    'reference_id' => $ipdPatientId,
                    'amount' => [
                        'value' => $amount,
                        'currency_code' => getCurrentCurrency(),
                    ],
                ],
            ],
            'application_context' => [
                'cancel_url' => route('patient.paypal.failed'),
                'return_url' => route('patient.paypal.success'),
            ],
        ];

        $order = $provider->createOrder($data);

        return response()->json(['url' => $order['links'][1]['href'], 'status' => 201]);
    }

    public function success(Request $request): RedirectResponse
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

        $this->patientPaypalRepository->patientPaymentSuccess($response);

        Flash::success(__('messages.flash.your_payment_success'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));

    }

    public function failed(): RedirectResponse
    {
        Flash::error(__('messages.flash.your_payment_failed'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }
        
        return redirect(route('ipd.patient.index'));
    }
}
