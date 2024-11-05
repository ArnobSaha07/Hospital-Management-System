<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Invoice;
use App\Models\Setting;

class InvoiceAPIController extends AppBaseController
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        if (getLoggedinPatient()) {
            $invoices = Invoice::where('patient_id', getLoggedInUser()->patient->id)->orderBy('id', 'desc')->get();
            $data = [];
            foreach ($invoices as $invoice) {
                $data[] = $invoice->prepareInvoice();
            }

            return $this->sendResponse($data, 'Invoices Retrieved Successfully');
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $invoice = Invoice::with(['patient.patientUser', 'invoiceItems'])->where('id', $id)->where('patient_id', getLoggedInUser()->owner_id)->first();
        $appLogo = Setting::whereTenantId(getLoggedInUser()->tenant_id)->pluck('value', 'key')->toArray();

        if (! $invoice) {
            return $this->sendError(__('messages.invoice.invoice').' '.__('messages.common.not_found'));
        }

        $invoiceDetails = $invoice->prepareInvoiceDetails();
        $invoiceDetails['app_logo'] = $appLogo['app_logo'];

        return $this->sendResponse($invoiceDetails, 'Invoice Retrieved Successfully');
    }
}
