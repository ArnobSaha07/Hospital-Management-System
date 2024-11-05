<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBillRequest;
use App\Http\Requests\UpdateBillRequest;
use App\Models\Bill;
use App\Models\BillTransaction;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\BillRepository;
use Auth;
use \PDF;
use Carbon\Carbon;
use DB;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Response;
use Stripe\Checkout\Session;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BillController extends AppBaseController
{
    /** @var BillRepository */
    private $billRepository;

    public function __construct(BillRepository $billRepo)
    {
        $this->billRepository = $billRepo;
    }

    /**
     * Display a listing of the Bill.
     *
     * @return Factory|View|Response
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('bills.index');
    }

    /**
     * Show the form for creating a new Bill.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $data = $this->billRepository->getSyncList(false);

        return view('bills.create')->with($data);
    }

    /**
     * Store a newly created Bill in storage.
     *
     *
     * @throws Exception
     */
    public function store(CreateBillRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
            $birthDate = $patientId->patientUser->dob;
            $billDate = Carbon::parse($input['bill_date'])->toDateString();
            if (! empty($birthDate) && $billDate < $birthDate) {
                return $this->sendError(__('messages.flash.bill_date_smaller'));
            }
            $bill = $this->billRepository->saveBill($request->all());
            $this->billRepository->saveNotification($input);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($bill, __('messages.flash.bill_saved'));
    }

    /**
     * Display the specified Bill.
     *
     * @return Factory|View
     */
    public function show(int $id)
    {
        if (! canAccessRecord(Bill::class, $id)) {
            return Redirect::back();
        }

        $bill = Bill::with(['billItems.medicine', 'patient', 'patientAdmission'])->findOrFail($id);
        $admissionDate = Carbon::parse($bill->patientAdmission->admission_date);
        $dischargeDate = Carbon::parse($bill->patientAdmission->discharge_date);
        $bill->totalDays = $admissionDate->diffInDays($dischargeDate) + 1;

        return view('bills.show')->with('bill', $bill);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Bill $bill)
    {
        if (! canAccessRecord(Bill::class, $bill->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $bill->billItems;
        $isEdit = true;
        $data = $this->billRepository->getSyncList($isEdit);
        $data['bill'] = $bill;

        return view('bills.edit')->with($data);
    }

    /**
     * Update the specified Bill in storage.
     *
     *
     * @throws Exception
     */
    public function update(Bill $bill, UpdateBillRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $patientId = Patient::with('patientUser')->whereId($input['patient_id'])->first();
            $birthDate = $patientId->patientUser->dob;
            $billDate = Carbon::parse($input['bill_date'])->toDateString();
            if (! empty($birthDate) && $billDate < $birthDate) {
                return $this->sendError('Bill date should not be smaller than patient birth date.');
            }
            $bill = $this->billRepository->updateBill($bill->id, $request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($bill, __('messages.flash.bill_updated'));
    }

    /**
     * Remove the specified Bill from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Bill $bill): JsonResponse
    {
        $this->billRepository->delete($bill->id);

        return $this->sendSuccess(__('messages.flash.bill_deleted'));
    }

    public function getPatientAdmissionDetails(Request $request): JsonResponse
    {
        $inputs = $request->all();
        $patientAdmissionDetails = $this->billRepository->patientAdmissionDetails($inputs);

        return $this->sendResponse($patientAdmissionDetails, __('messages.flash.bill_retrieved'));
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function convertToPdf(Bill $bill)
    {
        if (! canAccessRecord(Bill::class, $bill->id)) {
            return Redirect::back();
        }
        if(app()->getLocale() == "zh"){
            app()->setLocale("en");
        }
        $bill->billItems;
        $data = $this->billRepository->getSyncListForCreate($bill->id);
        $data['bill'] = $bill;
        $pdf = PDF::loadView('bills.bill_pdf', $data);

        return $pdf->stream('bill.pdf');
    }

    public function billPayment(Request $request)
    {
        $input = $request->all();
        $bill = Bill::whereId($input['id'])->first();
        $input['amount'] = $bill['amount'];
        if($input['paymentType'] == BillTransaction::TYPE_CASH){
            $bill->update(['status' => Bill::PENDING]);
            BillTransaction::create([
                'payment_type' => BillTransaction::TYPE_CASH,
                'amount' => $input['amount'],
                'bill_id' => $input['id'],
                'status' => BillTransaction::UNPAID,
            ]);

            return $this->sendSuccess(__('messages.lunch_break.payment_request_send'));
        }elseif($input['paymentType'] == BillTransaction::PHONEPE){
            $currency = ['INR'];

            if(!in_array(strtoupper(getCurrentCurrency()),$currency)){
                return $this->sendError(__('messages.phonepe.currency_allowed'));
            }

            $result = $this->billRepository->phonePePayment($input);

            return $this->sendResponse(['url' => $result,'payment_type' => $input['paymentType']],'PhonePe created successfully');
        }elseif ($input['paymentType'] == BillTransaction::FLUTTERWAVE){

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

            $result = $this->billRepository->flutterWavePayment($input);

            return $this->sendResponse(['url' => $result,'payment_type' => $input['paymentType']],'FlutterWave created successfully');
        }
        else{
            $stripeSecretKey = Setting::whereTenantId($bill->tenant_id)
            ->where('key', '=', 'stripe_secret')
            ->first();
            $stripeKey = Setting::whereTenantId($bill->tenant_id)
            ->where('key', '=', 'stripe_key')
            ->first();
            if (! empty($stripeSecretKey->value) || ! empty($stripeKey->value)) {
                setStripeApiKey($bill->tenant_id);
                $stripeKey = $stripeKey->value;
            } else {
                return $this->sendError(__('messages.new_change.provide_stripe_key'));
            }

            $session = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => $bill->patient->patientUser->email,
                'line_items' => [
                    [
                        'price_data' => [
                            'product_data' => [
                                'name' => 'Payment for Patient bill',
                            ],
                            'unit_amount' => in_array(getCurrentCurrency(), zeroDecimalCurrencies()) ? $bill->amount : $bill->amount * 100,
                            'currency' => getCurrentCurrency(),
                        ],
                        'quantity' => 1,
                    ],
                ],
                'client_reference_id' => $bill->id,
                'mode' => 'payment',
                'success_url' => route('bill.stripe.payment.success').'?session_id={CHECKOUT_SESSION_ID}',
            ]);

            $result = [
                'sessionId' => $session['id'],
            ];
            return $this->sendResponse([
                'bill_id' => $bill->id,
                'payment_type' => $input['paymentType'],
                'stripe_key' => $stripeKey,
                $result
            ],'Stripe session created successfully');
        }
    }

    public function manualBillingPayment()
    {
        return view('bills.manual-billing-payment');
    }

    public function changeBillPaymentStatus(Request $request): JsonResponse
    {
        $input = $request->all();
        $billTransaction = BillTransaction::with('bill.patient.patientUser')->findOrFail($input['id']);
        if ($input['status'] == BillTransaction::APPROVED) {
            DB::table('bill_transactions')
            ->where('id', $billTransaction->id)
            ->update([
                'is_manual_payment' => $input['status'],
                'status' => BillTransaction::PAID,
                'tenant_id' => $billTransaction->bill->patient->patientUser->tenant_id,
            ]);
            Bill::whereId($billTransaction->bill_id)->update(['status' => Bill::PAID]);

            return $this->sendSuccess(__('messages.flash.manual_payment_approved'));
        }else{
            if($input['status'] == BillTransaction::DENIED){
                DB::table('bill_transactions')
                ->where('id', $billTransaction->id)
                ->update([
                    'is_manual_payment' => $input['status'],
                    'status' => BillTransaction::UNPAID,
                    'tenant_id' => $billTransaction->bill->patient->patientUser->tenant_id,
                ]);
                Bill::whereId($billTransaction->bill_id)->update(['status' => Bill::UNPAID]);

                return $this->sendSuccess(__('messages.flash.manual_payment_denied'));
            }
        }
    }


    public function paymentSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (empty($sessionId)) {
            throw new UnprocessableEntityHttpException(__('messages.bill.session_id_required'));
        }
        $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
        setStripeApiKey($tenantId);

        $sessionData = \Stripe\Checkout\Session::retrieve($sessionId);
        $bill = Bill::find($sessionData->client_reference_id);

        if(!empty($bill)){
            BillTransaction::create([
                'transaction_id' => $sessionData->id,
                'payment_type' => 0,
                'amount' => $bill->amount,
                'bill_id' => $bill->id,
                'status' => 1,
                'meta' => null,
                'is_manual_payment' => 0,
            ]);
            $bill->update(['payment_mode' => 0, 'status' => '1']);
        }

        return redirect(route('bill.index'));
    }


    public function billPhonePePaymentSuccess(Request $request)
    {
        $this->billRepository->billPhonePePaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect()->back();
    }

    public function flutterwavePaymentSuccess(Request $request)
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

        if($request['status'] == 'cancelled'){
            Flash::error(__('messages.payment.payment_failed'));

            return redirect()->back();
        }
        $this->billRepository->flutterwavePaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect()->back();
    }
}
