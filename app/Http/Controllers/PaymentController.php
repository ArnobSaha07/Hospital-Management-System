<?php

namespace App\Http\Controllers;

use App\Exports\PaymentExport;
use App\Http\Requests\CreatePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentController extends AppBaseController
{
    /** @var PaymentRepository */
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepository = $paymentRepo;
    }

    /**
     * Display a listing of the Payment.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        return view('payments.index');
    }

    /**
     * Show the form for creating a new Payment.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $accounts = $this->paymentRepository->getAccounts();

        return view('payments.create', compact('accounts'));
    }

    /**
     * Store a newly created Payment in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function store(CreatePaymentRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['amount'] = removeCommaFromNumbers($input['amount']);
        $payment = $this->paymentRepository->create($input);

        Flash::success(__('messages.flash.payment_saved'));

        return redirect(route('payments.index'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Payment $payment)
    {
        if (! canAccessRecord(Payment::class, $payment->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        return view('payments.show')->with('payment', $payment);
    }

    /**
     * Show the form for editing the specified Payment.
     *
     * @return Factory|View
     */
    public function edit(int $id)
    {
        if (! canAccessRecord(Payment::class, $id)) {
            return Redirect::back();
        }
        $payment = Payment::findOrFail($id);
        $accounts = $this->paymentRepository->getAccounts();

        return view('payments.edit', compact('accounts', 'payment'));
    }

    /**
     * Update the specified Payment in storage.
     *
     * @return RedirectResponse|Redirector
     */
    public function update(Payment $payment, UpdatePaymentRequest $request): RedirectResponse
    {
        $input = $request->all();
        $input['amount'] = removeCommaFromNumbers($input['amount']);
        $payment = $this->paymentRepository->update($input, $payment->id);

        Flash::success(__('messages.flash.payment_updated'));

        return redirect(route('payments.index'));
    }

    /**
     * Remove the specified Payment from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Payment $payment): JsonResponse
    {
        $this->paymentRepository->delete($payment->id);

        return $this->sendSuccess(__('messages.flash.payment_deleted'));
    }

    public function paymentExport(): BinaryFileResponse
    {
        $response = Excel::download(new PaymentExport, 'payments-'.time().'.xlsx');

        ob_end_clean();

        return $response;
    }

    public function showModal(Payment $payment): JsonResponse
    {
        if (! canAccessRecord(Payment::class, $payment->id)) {
            return $this->sendError(__('messages.flash.not_allow_access_record'));
        }

        $payment->load('account');
        $payment['amount'] = getCurrencyFormat($payment->amount);

        return $this->sendResponse($payment, __('messages.flash.payment_retrieved'));
    }
}
