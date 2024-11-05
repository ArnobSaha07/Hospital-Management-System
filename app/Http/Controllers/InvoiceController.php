<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Mail\InvoicePatientMail;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Setting;
use App\Repositories\InvoiceRepository;
use \PDF;
use Carbon\Carbon;
use DB;
use Exception;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;

class InvoiceController extends AppBaseController
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepo)
    {
        $this->invoiceRepository = $invoiceRepo;
    }

    /**
     * Display a listing of the Invoice.
     *
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request): View
    {
        $statusArr = Invoice::STATUS_ARR;

        return view('invoices.index')->with('statusArr', $statusArr);
    }

    /**
     * Show the form for creating a new Invoice.
     *
     * @return Factory|View
     */
    public function create(): View
    {
        $data = $this->invoiceRepository->getSyncList();

        return view('invoices.create')->with($data);
    }

    /**
     * Store a newly created Invoice in storage.
     *
     *
     * @throws Exception|Throwable
     */
    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $bill = $this->invoiceRepository->saveInvoice($request->all());
            $this->invoiceRepository->saveNotification($request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($bill, __('messages.flash.invoice_saved'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function show(Invoice $invoice)
    {
        if (! canAccessRecord(Invoice::class, $invoice->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $data['hospitalAddress'] = Setting::where('key', '=', 'hospital_address')->first()->value;
        $data['invoice'] = Invoice::with(['invoiceItems.account', 'patient.address'])->find($invoice->id);

        return view('invoices.show')->with($data);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|RedirectResponse
     */
    public function edit(Invoice $invoice)
    {
        if (! canAccessRecord(Invoice::class, $invoice->id)) {
            Flash::error(__('messages.flash.not_allow_access_record'));

            return Redirect::back();
        }

        $invoice->invoiceItems;
        $data = $this->invoiceRepository->getSyncList();
        $data['invoice'] = $invoice;

        return view('invoices.edit')->with($data);
    }

    /**
     * Update the specified Invoice in storage.
     *
     *
     * @throws Exception
     */
    public function update(Invoice $invoice, UpdateInvoiceRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $bill = $this->invoiceRepository->updateInvoice($invoice->id, $request->all());
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($bill, __('messages.flash.invoice_updated'));
    }

    /**
     * Remove the specified Invoice from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        if (! canAccessRecord(Invoice::class, $invoice->id)) {
            return $this->sendError(__('messages.flash.invoice_not_found'));
        }

        $this->invoiceRepository->delete($invoice->id);

        return $this->sendSuccess(__('messages.flash.invoice_deleted'));
    }

    /**
     * @return RedirectResponse|Redirector
     */
    public function convertToPdf(Invoice $invoice)
    {
        if(app()->getLocale() == "zh"){
            app()->setLocale("en");
        }
        $invoice->invoiceItems;
        $data = $this->invoiceRepository->getSyncListForCreate($invoice->id);
        $data['invoice'] = $invoice;
        $data['currencySymbol'] = getCurrencySymbol();
        $pdf = PDF::loadView('invoices.invoice_pdf', $data);

        return $pdf->stream('invoice.pdf');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|Redirector
     */
    public function sendMail(Invoice $invoice)
    {
        $patient = Patient::with('user')->whereId($invoice->patient_id)->first();

        $mailData = [
            'invoice_id' => $invoice->id,
            'patient_name' => $patient->user->full_name,
            'invoice_number' => $invoice->invoice_id,
            'invoice_date' => Carbon::parse($invoice->invoice_date)->format('d/m/Y'),
            'discount' => $invoice->discount.'%',
            'amount' => getCurrencySymbol().' '.number_format($invoice->amount - ($invoice->amount * $invoice->discount / 100), 2),
            'status' => $invoice->status == 1 ? 'Paid' : 'Pending',
        ];

        Mail::to($patient->user->email)
            ->send(new InvoicePatientMail('emails.invoice_patient_mail',
                __('messages.new_change.patient_invoice_bill'),
                $mailData));

        return $this->sendSuccess(__('messages.new_change.patient_mail_send'));
    }
}
