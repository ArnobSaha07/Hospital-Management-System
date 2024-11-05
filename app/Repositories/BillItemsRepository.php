<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\BillItems;

/**
 * Class BillItemsRepository
 *
 * @version February 13, 2020, 9:51 am UTC
 */
class BillItemsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'medicine_id',
        'bill_id',
        'qty',
        'price',
        'amount',
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
        return BillItems::class;
    }

    /**
     * @throws \Exception
     */
    public function updateBillItem(array $billItemInput, int $billId)
    {
        /** @var Bill $bill */
        $bill = Bill::find($billId);
        $billItemIds = [];
        foreach ($billItemInput as $key => $data) {
            if (isset($data['id']) && ! empty($data['id'])) {
                $billItemIds[] = $data['id'];
                $this->update($data, $data['id']);
            } else {
                /** @var BillItems $billItem */
                $billItem = new BillItems($data);
                $billItem = $bill->billItems()->save($billItem);
                $billItemIds[] = $billItem->id;
            }
        }

        if (! (isset($billItemIds) && count($billItemIds))) {
            return;
        }
        BillItems::whereNotIn('id', $billItemIds)->whereBillId($bill->id)->delete();
    }
}
