<?php

namespace App\Http\Controllers;

use App\Models\IpdPatientDepartment;
use App\Repositories\PatientRazorpayRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Razorpay\Api\Api;

class PatientRazorpayController extends AppBaseController
{
    /**
     * @var PatientRazorpayRepository
     */
    private $patientRazorpayRepository;

    private $razorpayKey;

    private $razorpaySecretKey;

    public function __construct(PatientRazorpayRepository $patientRazorpayRepository)
    {
        $this->patientRazorpayRepository = $patientRazorpayRepository;
        $this->razorpayKey = getSelectedPaymentGateway('razorpay_key');
        $this->razorpaySecretKey = getSelectedPaymentGateway('razorpay_secret');
    }

    public function onBoard(Request $request): JsonResponse
    {
        $input = $request->all();

        $api = new Api($this->razorpayKey, $this->razorpaySecretKey);

        $amount = intval($request->get('amount'));
        // $ipdNumber = $request->get('ipdNumber');
        // $ipdPatientId = IpdPatientDepartment::whereIpdNumber($ipdNumber)->first()->id;
        $ipdPatientId = $request->get('ipdNumber');

        $orderData = [
            'receipt' => $ipdPatientId,
            'amount' => $amount * 100,
            'currency' => strtoupper(getCurrentCurrency()),
            'notes' => [
                'email' => getLoggedInUser()->email,
                'name' => getLoggedInUser()->full_name,
                'ipd_patient_id' => $ipdPatientId,
                'notes' => $request->notes,
            ],
        ];

        $razorpayOrder = $api->order->create($orderData);

        $data['id'] = $razorpayOrder->id;
        $data['currency'] = strtoupper(getCurrentCurrency());
        $data['amount'] = $razorpayOrder->amount;
        $data['name'] = getLoggedInUser()->full_name;
        $data['email'] = getLoggedInUser()->email;
        $data['contact'] = getLoggedInUser()->phone;
        $data['notes'] = $request->notes;

        return $this->sendResponse($data, __('messages.flash.order_created'));
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function paymentSuccess(Request $request): RedirectResponse
    {
        $input = $request->all();

        Log::info('RazorPay Payment Successfully');
        $api = new Api($this->razorpayKey, $this->razorpaySecretKey);
        if (count($input) && ! empty($input['razorpay_payment_id'])) {
            $payment = $api->payment->fetch($input['razorpay_payment_id']);
            $generatedSignature = hash_hmac('sha256', $payment['order_id'].'|'.$input['razorpay_payment_id'],
                $this->razorpaySecretKey);
            if ($generatedSignature != $input['razorpay_signature']) {
                return redirect()->back();
            }
        }

        $this->patientRazorpayRepository->patientPaymentSuccess($payment);

        Flash::success(__('messages.flash.your_payment_success'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));

    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function paymentFailed(Request $request): RedirectResponse
    {
        Flash::error(__('messages.flash.your_payment_failed'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));
    }
}
