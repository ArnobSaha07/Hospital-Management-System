<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateMedicineBillRequest;
use App\Http\Requests\CreatePatientRequest;
use App\Http\Requests\UpdateMedicineBillRequest;
use App\Models\Category;
use App\Models\Medicine;
use App\Models\MedicineBill;
use App\Models\SaleMedicine;
use App\Repositories\DoctorRepository;
use App\Repositories\IpdPatientDepartmentRepository;
use App\Repositories\MedicineBillRepository;
use App\Repositories\MedicineRepository;
use App\Repositories\PatientRepository;
use App\Repositories\PrescriptionRepository;
use DB;
use \PDF;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Unicodeveloper\Paystack\Facades\Paystack;

class MedicineBillController extends AppBaseController
{
    /* @var  PrescriptionRepository
          @var DoctorRepository
         */
    private $prescriptionRepository;

    private $doctorRepository;

    private $medicineRepository;

    private $patientRepository;

    private $medicineBillRepository;

    public function __construct(
        PrescriptionRepository $prescriptionRepo,
        DoctorRepository $doctorRepository,
        MedicineRepository $medicineRepository,
        PatientRepository $patientRepo,
        MedicineBillRepository $medicineBillRepository,
    ) {
        $this->prescriptionRepository = $prescriptionRepo;
        $this->doctorRepository = $doctorRepository;
        $this->medicineRepository = $medicineRepository;
        $this->patientRepository = $patientRepo;
        $this->medicineBillRepository = $medicineBillRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {

        return view('medicine-bills.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {

        $patients = $this->prescriptionRepository->getPatients();
        $doctors = $this->doctorRepository->getDoctors();
        $medicines = $this->prescriptionRepository->getMedicines();
        $data = $this->medicineRepository->getSyncList();
        $medicineList = $this->medicineRepository->getMedicineList();
        $mealList = $this->medicineRepository->getMealList();
        $IpdRepo = App::make(IpdPatientDepartmentRepository::class);
        $medicineCategories = $IpdRepo->getMedicinesCategoriesData();
        $medicineCategoriesList = $IpdRepo->getMedicineCategoriesList();

        return view('medicine-bills.create',
            compact('patients', 'doctors', 'medicines', 'medicineList', 'mealList', 'medicineCategoriesList', 'medicineCategories'))->with($data);
    }

    public function setFlutterWaveConfig()
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateMedicineBillRequest $request)
    {
        $input = $request->all();

        if($input['payment_type'] == MedicineBill::MEDICINE_BILL_STRIPE){
            $medicineBill = $this->medicineBillRepository->medicineBillStore($input);
            $result = $this->medicineBillRepository->stripeSession($input, $medicineBill);

            return $this->sendResponse(['payment_type' => $input['payment_type'],$result], 'stripe session created successfully.');

        }elseif($input['payment_type'] == MedicineBill::MEDICINE_BILL_RAZORPAY){
            $medicineBill = $this->medicineBillRepository->medicineBillStore($input);

            return $this->sendResponse(['payment_type' => $input['payment_type'], 'bill_number' => $medicineBill->bill_number], 'razorpay session created successfully.');

        }elseif($input['payment_type'] == MedicineBill::MEDICINE_BILL_PAYSTACK){
            foreach ($input['medicine'] as $key => $value) {
                $medicine = Medicine::find($input['medicine'][$key]);
                if (! empty($duplicateIds)) {
                    foreach ($duplicateIds as $key => $value) {
                        $medicine = Medicine::find($duplicateIds[$key]);
                        return $this->sendError(__('messages.medicine_bills.duplicate_medicine'));
                    }
                }
                $qty = $input['quantity'][$key];

                if ($medicine->available_quantity < $qty) {
                    $available = $medicine->available_quantity == null ? 0 : $medicine->available_quantity;
                    return $this->sendError(__('messages.medicine_bills.available_quantity').' '.$medicine->name.' '.__('messages.medicine_bills.is').' '.$available.'.');
                }
            }
            return $this->sendResponse(['payStackData' => $input],'PayStack session created successfully.');

        }elseif($input['payment_type'] == MedicineBill::MEDICINE_BILL_PHONEPE){

            $result = $this->medicineBillRepository->phonePePayment($input);

            return $this->sendResponse(['url' => $result,'payment_type' => $input['payment_type']],'PhonePe session created successfully.');

        }elseif($input['payment_type'] == MedicineBill::MEDICINE_BILL_FLUTTERWAVE){

            if(!in_array(strtoupper(getCurrentCurrency()), flutterWaveSupportedCurrencies())){
                return $this->sendError(__('messages.flutterwave.currency_allowed'));
            }

            $this->setFlutterWaveConfig();

            session(['medicineBillDataFlutterWave' => $input]);

            $result = $this->medicineBillRepository->flutterWavePayment($request->all());

            return $this->sendResponse(['url' => $result,'payment_type' => $input['payment_type']],'Flutterwave created successfully');

        }else{
            $this->medicineBillRepository->medicineBillStore($input);

            return $this->sendSuccess(__('messages.medicine_bills.medicine_bill').' '.__('messages.common.saved_successfully'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show(MedicineBill $medicineBill): View
    {
        $medicineBill->load(['saleMedicine.medicine']);

        return view('medicine-bills.show', compact('medicineBill'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicineBill $medicineBill): View
    {
        $medicineBill->load(['saleMedicine.medicine.category', 'saleMedicine.medicine.purchasedMedicine', 'patient', 'doctor']);

        $patients = $this->prescriptionRepository->getPatients();
        $doctors = $this->doctorRepository->getDoctors();
        $medicines = $this->prescriptionRepository->getMedicines();
        $data = $this->medicineRepository->getSyncList();
        $medicineList = $this->medicineRepository->getMedicineList();
        $mealList = $this->medicineRepository->getMealList();
        $IpdRepo = App::make(IpdPatientDepartmentRepository::class);
        $medicineCategories = $IpdRepo->getMedicinesCategoriesData();
        $medicineCategoriesList = $IpdRepo->getMedicineCategoriesList();

        return view('medicine-bills.edit',
            compact('patients', 'doctors', 'medicines', 'medicineList', 'mealList', 'medicineBill', 'medicineCategoriesList', 'medicineCategories'))->with($data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MedicineBill $medicineBill, UpdateMedicineBillRequest $request)
    {
        $input = $request->all();
        if(empty($input['medicine']) && $input['payment_status'] == false){

            return $this->sendError(__('messages.medicine_bills.medicine_not_selected'));
        }

        $this->medicineBillRepository->update($medicineBill,$input);

        return $this->sendSuccess(__('messages.medicine_bills.medicine_bill').' '.__('messages.common.saved_successfully'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * *  @return \Illuminate\Http\Response
     */
    public function destroy(MedicineBill $medicineBill)
    {
        $medicineBill->saleMedicine()->delete();
        $medicineBill->delete();

        return $this->sendSuccess(__('messages.medicine_bills.medicine_bill').' '.__('messages.common.deleted_successfully'));
    }

    /** Store a newly created Patient in storage.
     */
    public function storePatient(CreatePatientRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['status'] = isset($input['status']) ? 1 : 0;

        $this->patientRepository->store($input);
        $this->patientRepository->createNotification($input);
        $patients = $this->prescriptionRepository->getPatients();

        return $this->sendResponse($patients, __('messages.flash.Patient_saved'));
    }

    public function convertToPDF($id): Response
    {
        if(app()->getLocale() == "zh"){
            app()->setLocale("en");
        }
        $data = $this->prescriptionRepository->getSettingList();
        $medicineBill = MedicineBill::with(['saleMedicine.medicine'])->where('id', $id)->first();

        $pdf = PDF::loadView('medicine-bills.medicine_bill_pdf', compact('medicineBill', 'data'));

        return $pdf->stream('medicine-bill.pdf');
    }

    public function getMedicineCategory(Category $category): JsonResponse
    {
        $data = [];
        $data['category'] = $category;
        $data['medicine'] = Medicine::whereCategoryId($category->id)->pluck('name', 'id')->toArray();

        return $this->sendResponse($data, 'retrieved');
    }

    public function stripeSuccess(Request $request)
    {
        $this->medicineBillRepository->medicineBillstripeSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('medicine-bills.index'));
    }

    public function stripeFailed(Request $request)
    {
        $this->medicineBillRepository->medicineBillstripeFailed($request->all());

        Flash::error(__('messages.payment.payment_failed'));

        return redirect(route('medicine-bills.index'));
    }

    public function razorPayPayment(Request $request)
    {
        $result = $this->medicineBillRepository->razorPayPayment($request->all());

        return $this->sendResponse($result, 'RazorPay order created successfully.');
    }

    public function razorPayPaymentSuccess(Request $request)
    {
        $result = $this->medicineBillRepository->razorPayPaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('medicine-bills.index'));
    }

    public function razorPayPaymentFailed(Request $request)
    {
        $input = $request->all();

        $input['payment_status'] = isset($input['payment_status']) ? 1 : 0;

            $medicineBill = MedicineBill::orderBy('created_at', 'desc')->latest()->first();

            if ($input['category_id']) {
                foreach ($input['category_id'] as $key => $value) {
                    $medicine = Medicine::find($input['medicine'][$key]);
                    $tax = $input['tax_medicine'][$key] == null ? $input['tax_medicine'][$key] : 0;

                    $saleMedicine = SaleMedicine::where('medicine_bill_id',$medicineBill->id)->first();
                    $saleMedicine->delete();

                    if ($input['payment_status'] == 1) {
                        $medicine->update([
                            'available_quantity' => $input['quantity'][$key] + $medicine->available_quantity,
                        ]);
                    }
                }
            }

        $medicineBill->delete();

        return $this->sendSuccess(__('messages.payment.payment_failed'));
    }

    public function paystackConfig()
    {
        config(['paystack.publicKey' => getPaymentCredentials('paystack_public_key'),
            'paystack.secretKey' => getPaymentCredentials('paystack_secret_key'),
            'paystack.paymentUrl' => 'https://api.paystack.co',
        ]);
    }

    public function paystackPayment(Request $request)
    {
        $this->paystackConfig();

        if (!in_array(strtoupper(getCurrentCurrency()),payStackSupportedCurrencies())) {
            Flash::error(__('messages.new_change.paystack_support_zar'));

            return redirect(route('medicine-purchase.index'));
        }

        $data = $request->data;
        $amount = $request->net_amount;

        try {
            $request->merge([
                'email' => getLoggedInUser()->email,
                'amount' => $amount * 100,
                'quantity' => 1,
                'currency' => strtoupper(getCurrentCurrency()),
                'reference' => Paystack::genTranxRef(),
                'metadata' => json_encode(['data' => $data]),
            ]);
            $authorizationUrl = Paystack::getAuthorizationUrl();

            return $authorizationUrl->redirectNow();
        } catch (\Exception $e) {
            Flash::error(__('messages.payment.payment_failed'));

            return redirect(route('medicine-bills.index'));
        }
    }

    public function paystackPaymentSuccess()
    {
        $this->paystackConfig();

        $paymentDetails = Paystack::getPaymentData();

        $this->medicineBillRepository->paystackPaymentSuccess($paymentDetails);

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('medicine-bills.index'));
    }

    public function phonePePaymentSuccess(Request $request)
    {
        $this->medicineBillRepository->phonePePaymentSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('medicine-bills.index'));
    }

    public function flutterWaveSuccess(Request $request)
    {
        if($request->status == 'cancelled'){
            Flash::error(__('messages.new_change.payment_fail'));

            return redirect(route('medicine-bills.index'));
        }

        $this->setFlutterWaveConfig();

        $this->medicineBillRepository->flutterWaveSuccess($request->all());

        Flash::success(__('messages.payment.your_payment_is_successfully_completed'));

        return redirect(route('medicine-bills.index'));
    }
}
