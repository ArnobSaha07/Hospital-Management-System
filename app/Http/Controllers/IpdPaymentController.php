<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateIpdPaymentRequest;
use App\Http\Requests\UpdateIpdPaymentRequest;
use App\Models\IpdPayment;
use App\Repositories\IpdPaymentRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laracasts\Flash\Flash;
use Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Unicodeveloper\Paystack\Facades\Paystack;

class IpdPaymentController extends AppBaseController
{
    /** @var IpdPaymentRepository */
    private $ipdPaymentRepository;

    public function __construct(IpdPaymentRepository $ipdPaymentRepo)
    {
        $this->ipdPaymentRepository = $ipdPaymentRepo;
    }

    /**
     * Display a listing of the IpdPayment.
     *
     *
     * @throws Exception
     */
    public function index(Request $request): Response
    {
    }

    /**
     * Store a newly created IpdPayment in storage.
     */
    public function store(CreateIpdPaymentRequest $request): JsonResponse
    {
        $input = $request->all();

        if($input['payment_mode'] == IpdPayment::PAYMENT_MODES_STRIPE){

            // $result = $this->ipdPaymentRepository->stripeSession($input);

            return $this->sendResponse([
                'ipdID' => $input['ipd_patient_department_id'],
                'payment_type' => $input['payment_mode'],
                'amount' => $input['amount'],
                'notes' => $input['notes'],
            ],'Stripe session created successfully');

        }elseif($input['payment_mode'] == IpdPayment::PAYMENT_MODES_RAZORPAY){

            return $this->sendResponse([
                'ipdID' => $input['ipd_patient_department_id'],
                'amount' => $input['amount'],
                'payment_type' => $input['payment_mode'],
                'notes' => $input['notes'],
            ],'Razorpay session created successfully');

        }elseif($input['payment_mode'] == IpdPayment::PAYMENT_MODES_PAYPAL){

            return $this->sendResponse([
                'ipdID' => $input['ipd_patient_department_id'],
                'amount' => $input['amount'],
                'payment_type' => $input['payment_mode'],
                'notes' => $input['notes'],
            ],'paypal session created successfully');

        }
        // elseif($input['payment_mode'] == IpdPayment::PAYMENT_MODES_PAYTM){

        //     return $this->sendResponse([
        //         'ipdID' => $input['ipd_patient_department_id'],
        //         'amount' => $input['amount'],
        //         'payment_type' => $input['payment_mode'],
        //     ],'Paytm session created successfully');

        // }
        elseif($input['payment_mode'] == IpdPayment::PAYMENT_MODES_PAYSTACK){

            return $this->sendResponse([
                'ipdID' => $input['ipd_patient_department_id'],
                'amount' => $input['amount'],
                'payment_type' => $input['payment_mode'],
                'notes' => $input['notes'],
            ],'Paystack session created successfully');

        }
        elseif($input['payment_mode'] == IpdPayment::PAYMENT_MODES_PHONEPE){

            $currency = ['INR'];

            if(!in_array(strtoupper(getCurrentCurrency()),$currency)){
                return $this->sendError(__('messages.phonepe.currency_allowed'));
            }

            $result = $this->ipdPaymentRepository->phonePePayment($input);

            return $this->sendResponse(['url' => $result,'payment_type' => $input['payment_mode']],'PhonePe created successfully');
        }
        elseif($input['payment_mode'] == IpdPayment::PAYMENT_MODES_FLUTTERWAVE){

            if(!in_array(strtoupper(getCurrentCurrency()),flutterWaveSupportedCurrencies())){
                return $this->sendError(__('messages.flutterwave.currency_allowed'));
            }

            $flutterwavePublicKey = getPaymentCredentials('flutterwave_public_key');
            $flutterwaveSecretKey = getPaymentCredentials('flutterwave_secret_key');

            if(!$flutterwavePublicKey && !$flutterwaveSecretKey){
                return $this->sendError(__('messages.flutterwave.set_flutterwave_credential'));
            }

            config([
                'flutterwave.publicKey' => $flutterwavePublicKey,
                'flutterwave.secretKey' => $flutterwaveSecretKey,
            ]);

            $result = $this->ipdPaymentRepository->flutterWavePayment($input);

            return $this->sendResponse(['url' => $result,'payment_type' => $input['payment_mode']],'Flutterwave created successfully');
        }
        else{
            $this->ipdPaymentRepository->store($input);
        }

        return $this->sendSuccess(__('messages.flash.IPD_payment_saved'));
    }

    /**
     * Show the form for editing the specified Ipd Payment.`
     */
    public function edit(IpdPayment $ipdPayment): JsonResponse
    {
        if (! canAccessRecord(IpdPayment::class, $ipdPayment->id)) {
            return $this->sendError(__('messages.flash.ipd_payment_not_found'));
        }

        return $this->sendResponse($ipdPayment, __('messages.flash.IPD_payment_retrieved'));
    }

    /**
     * Update the specified Ipd Payment in storage.
     */
    public function update(IpdPayment $ipdPayment, UpdateIpdPaymentRequest $request): JsonResponse
    {
        $this->ipdPaymentRepository->updateIpdPayment($request->all(), $ipdPayment->id);

        return $this->sendSuccess(__('messages.flash.IPD_payment_updated'));
    }

    /**
     * Remove the specified IpdPayment from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(IpdPayment $ipdPayment): JsonResponse
    {
        if (! canAccessRecord(IpdPayment::class, $ipdPayment->id)) {
            return $this->sendError(__('messages.flash.ipd_payment_not_found'));
        }

        $this->ipdPaymentRepository->deleteIpdPayment($ipdPayment->id);

        return $this->sendSuccess(__('messages.flash.IPD_payment_deleted'));
    }

    public function downloadMedia(IpdPayment $ipdPayment): Media
    {
        $media = $ipdPayment->getMedia(IpdPayment::IPD_PAYMENT_PATH)->first();
        ob_end_clean();
        if ($media != null) {
            $media = $media->id;
            $mediaItem = Media::findOrFail($media);

            return $mediaItem;
        }

        return '';
    }


    public function ipdStripePaymentSuccess(Request $request)
    {
        $this->ipdPaymentRepository->ipdStripePaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));
    }

    public function ipdRazorpayPayment(Request $request)
    {
        $result = $this->ipdPaymentRepository->razorpayPayment($request->all());

       return $this->sendResponse($result, 'order created');
    }

    public function ipdRazorpayPaymentSuccess(Request $request)
    {
        $this->ipdPaymentRepository->ipdRazorpayPaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));
    }

    public function phonePePaymentSuccess(Request $request)
    {
        $this->ipdPaymentRepository->phonePePaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));
    }

    public function flutterwavePaymentSuccess(Request $request)
    {
        if ($request->status ==  'cancelled') {
            Flash::error(__('messages.new_change.payment_fail'));

            if(getLoggedinPatient()){
                return redirect(route('patient.ipd'));
            }

            return redirect(route('ipd.patient.index'));
        }

        $flutterwavePublicKey = getPaymentCredentials('flutterwave_public_key');
        $flutterwaveSecretKey = getPaymentCredentials('flutterwave_secret_key');

        if(!$flutterwavePublicKey && !$flutterwaveSecretKey){
            return $this->sendError(__('messages.flutterwave.set_flutterwave_credential'));
        }

        config([
            'flutterwave.publicKey' => $flutterwavePublicKey,
            'flutterwave.secretKey' => $flutterwaveSecretKey,
        ]);

        $this->ipdPaymentRepository->flutterwavePaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));
    }

    public function paystackConfig()
    {
        config(['paystack.publicKey' => getPaymentCredentials('paystack_public_key'),
            'paystack.secretKey' => getPaymentCredentials('paystack_secret_key'),
            'paystack.paymentUrl' => 'https://api.paystack.co',
        ]);
    }

    public function ipdPaystackPayment(Request $request)
    {
        if (!in_array(strtoupper(getCurrentCurrency()),payStackSupportedCurrencies())) {
            Flash::error(__('messages.new_change.paystack_support_zar'));

            if(getLoggedinPatient()){
                return redirect(route('patient.ipd'));
            }

            return redirect(route('ipd.patient.index'));
        }
        $amount = $request->amount;
        $ipdNumber = $request->ipdNumber;

        $this->paystackConfig();

        try {
            $request->merge([
                'email' => getLoggedInUser()->email,
                'orderID' => $ipdNumber,
                'amount' => $amount * 100,
                'quantity' => 1,
                'currency' => strtoupper(getCurrentCurrency()),
                'reference' => Paystack::genTranxRef(),
                'metadata' => json_encode(['ipd_patient_id' => $ipdNumber,'notes' => $request->notes]),
            ]);
            $authorizationUrl = Paystack::getAuthorizationUrl();

            return $authorizationUrl->redirectNow();
        } catch (\Exception $e) {

            Flash::error(__('messages.payment.payment_failed'));

            if(getLoggedinPatient()){
                return redirect(route('patient.ipd'));
            }

            return redirect(route('ipd.patient.index'));
        }
    }

    public function IpdPaystackPaystackSuccess(Request $request): RedirectResponse
    {
        $paymentDetails = Paystack::getPaymentData();

        $this->ipdPaymentRepository->ipdPaystackPaymentSuccess($paymentDetails);

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        if(getLoggedinPatient()){
            return redirect(route('patient.ipd'));
        }

        return redirect(route('ipd.patient.index'));
    }
}
