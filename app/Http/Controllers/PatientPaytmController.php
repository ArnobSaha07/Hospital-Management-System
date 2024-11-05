<?php

namespace App\Http\Controllers;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Http\Requests\CreatePaytmDetailRequest;
use App\Models\IpdPatientDepartment;
use App\Repositories\PatientPaytmRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class PatientPaytmController extends Controller
{
    /**
     * @var PatientPaytmRepository
     */
    private $patientPaytmRepository;

    /**
     * PatientPaytmController constructor.
     */
    public function __construct(PatientPaytmRepository $patientPaytmRepository)
    {
        $this->patientPaytmRepository = $patientPaytmRepository;
    }

    public function initiate(Request $request)
    {
        $amount = $request->get('amount');
        $ipdNumber = $request->get('ipdNumber');

        if (strtolower(getCurrentCurrency()) != 'inr') {
            Flash::error(__('messages.new_change.paytm_support_indian'));

            if(getLoggedinPatient()){
                return redirect(route('patient.ipd'));
            }

            return redirect(route('ipd.patient.index'));
        }

        return view('patient_paytm.index', compact('amount', 'ipdNumber'));
    }

    public function payment(CreatePaytmDetailRequest $request)
    {
        $amount = $request->get('amount');
        // $ipdNumber = $request->get('ipdNumber');
        $phone = $request->get('mobile');
        // $ipdPatientId = IpdPatientDepartment::whereIpdNumber($ipdNumber)->first()->id;
        $ipdPatientId = $request->get('ipdNumber');
        $orderId = $ipdPatientId.'|'.time();

        $payment = PaytmWallet::with('receive');

        $payment->prepare([
            'order' => $orderId, // 1 should be your any data id
            'user' => getLoggedInUserId(), // any user id
            'mobile_number' => $phone,
            'email' => getLoggedInUser()->email, // your user email address
            'amount' => $amount,
            'callback_url' => route('patient.paytm.callback'), // callback URLs
        ]);

        return $payment->receive();
    }

    public function paymentCallback(): RedirectResponse
    {
        $paytmPaymentTransaction = PaytmWallet::with('receive');
        $response = $paytmPaymentTransaction->response();

        if ($response == 'failed') {
            Flash::error(__('messages.flash.unable_to_process'));

            if(getLoggedinPatient()){
                return redirect(route('patient.ipd'));
            }

            return redirect(route('ipd.patient.index'));
        } elseif ($response['RESPCODE'] == 01) {
            $this->patientPaytmRepository->patientPaymentSuccess($response);

            Flash::success(__('messages.flash.your_payment_success'));

            if(getLoggedinPatient()){
                return redirect(route('patient.ipd'));
            }

            return redirect(route('ipd.patient.index'));
        }

        $failureMsg = $response['RESPMSG'];

        Flash::error($failureMsg);

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));

    }

    public function failed(): RedirectResponse
    {
        Flash::error(__('messages.flash.unable_to_process'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));
    }
}
