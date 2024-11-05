<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use \PDF;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class InvoicePatientMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    private $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $view, string $subject, array $data = [])
    {
        $this->view = $view;
        $this->subject = $subject;
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $invoiceId = $this->data['invoice_id'];
        $invoice = Invoice::find($invoiceId);
        $invoice->invoiceItems;
        $invoiceRepo = App::make(InvoiceRepository::class);
        $data = $invoiceRepo->getSyncListForCreate($invoice->id);
        $data['invoice'] = $invoice;
        $data['currencySymbol'] = getCurrencySymbol();
        $pdf = PDF::loadView('invoices.invoice_pdf', $data);

        return $this->subject($this->subject)
            ->markdown($this->view)
            ->with($this->data)
            ->attachData($pdf->output(), 'Invoice.pdf');
    }
}
