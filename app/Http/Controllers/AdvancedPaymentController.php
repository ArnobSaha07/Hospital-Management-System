<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAdvancedPaymentRequest;
use App\Http\Requests\UpdateAdvancedPaymentRequest;
use App\Models\AdvancedPayment;
use App\Repositories\AdvancedPaymentRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdvancedPaymentController extends AppBaseController
{
    /** @var AdvancedPaymentRepository */
    private $advancedPaymentRepository;

    public function __construct(AdvancedPaymentRepository $advancedPaymentRepo)
    {
        $this->advancedPaymentRepository = $advancedPaymentRepo;
    }

    /**
     * Display a listing of the AdvancedPayment.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        //        $receiptNo = strtoupper(Str::random(8));
        $patients = $this->advancedPaymentRepository->getPatients();

        return view('advanced_payments.index', compact('patients'));
    }

    /**
     * Store a newly created AdvancedPayment in storage.
     */
    public function store(CreateAdvancedPaymentRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['amount'] = removeCommaFromNumbers($input['amount']);
        Schema::disableForeignKeyConstraints();
        $this->advancedPaymentRepository->create($input);
        Schema::enableForeignKeyConstraints();
        $this->advancedPaymentRepository->createNotification($input);

        return $this->sendSuccess(__('messages.flash.advanced_payment_save'));
    }

    /**
     * @return Factory|RedirectResponse|Redirector|View
     */
    public function show(AdvancedPayment $advancedPayment)
    {
        if (! canAccessRecord(AdvancedPayment::class, $advancedPayment->id)) {
            return Redirect::back();
        }

        $advancedPayment = $this->advancedPaymentRepository->find($advancedPayment->id);
        if (empty($advancedPayment)) {
            Flash::error(__('messages.flash.advanced_payment_not'));

            return redirect(route('advancedPayments.index'));
        }
        $patients = $this->advancedPaymentRepository->getPatients();

        return view('advanced_payments.show')->with(['advancedPayment' => $advancedPayment, 'patients' => $patients]);
    }

    /**
     * Show the form for editing the specified AdvancedPayment.
     */
    public function edit(AdvancedPayment $advancedPayment): JsonResponse
    {
        if (! canAccessRecord(AdvancedPayment::class, $advancedPayment->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        return $this->sendResponse($advancedPayment, __('messages.flash.advanced_payment_retrieve'));
    }

    public function update(AdvancedPayment $advancedPayment, UpdateAdvancedPaymentRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['amount'] = removeCommaFromNumbers($input['amount']);
        Schema::disableForeignKeyConstraints();
        $this->advancedPaymentRepository->update($input, $advancedPayment->id);
        Schema::enableForeignKeyConstraints();

        return $this->sendSuccess(__('messages.flash.advanced_payment_updated'));
    }

    /**
     * Remove the specified AdvancedPayment from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(AdvancedPayment $advancedPayment): JsonResponse
    {
        if (! canAccessRecord(AdvancedPayment::class, $advancedPayment->id)) {
            return $this->sendError(__('messages.flash.advance_payment_not_found'));
        }
        $advancedPayment->delete();

        return $this->sendSuccess(__('messages.flash.advanced_payment_deleted'));
    }
}
