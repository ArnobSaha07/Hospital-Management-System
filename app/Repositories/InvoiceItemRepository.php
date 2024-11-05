<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Exception;

/**
 * Class InvoiceItemRepository
 *
 * @version February 24, 2020, 5:57 am UTC
 */
class InvoiceItemRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'account_id',
        'description',
        'quantity',
        'price',
        'total',
    ];

    /**
     * Return searchable fields
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return InvoiceItem::class;
    }

    /**
     * @throws Exception
     */
    public function updateInvoiceItem(array $invoiceItemInput, int $invoiceId)
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::find($invoiceId);
        $invoiceItemIds = [];
        foreach ($invoiceItemInput as $key => $data) {
            if (isset($data['id']) && ! empty($data['id'])) {
                $invoiceItemIds[] = $data['id'];
                $this->update($data, $data['id']);
            } else {
                /** @var InvoiceItem $invoiceItem */
                $invoiceItem = new InvoiceItem($data);
                $invoiceItem = $invoice->invoiceItems()->save($invoiceItem);
                $invoiceItemIds[] = $invoiceItem->id;
            }
        }

        if (! (isset($invoiceItemIds) && count($invoiceItemIds))) {
            return;
        }

        InvoiceItem::whereNotIn('id', $invoiceItemIds)->whereInvoiceId($invoice->id)->delete();
    }
}
