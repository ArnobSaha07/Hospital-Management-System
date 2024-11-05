<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Arr;

/**
 * Class UserRepository
 */
class PaymentGatewayRepository extends BaseRepository
{
    public $fieldSearchable = [
        'application_name',
    ];

    /**
     * {@inheritDoc}
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * {@inheritDoc}
     */
    public function model()
    {
        return Setting::class;
    }

    public function PaymentGateway(array $input): void
    {
        $inputArr = Arr::except($input, ['_token']);

        if (! isset($inputArr['stripe_enable'])) {
            $inputArr = Arr::add($inputArr, 'stripe_enable', 0);
        }
        if (! isset($inputArr['paypal_enable'])) {
            $inputArr = Arr::add($inputArr, 'paypal_enable', 0);
        }
        if (! isset($inputArr['razorpay_enable'])) {
            $inputArr = Arr::add($inputArr, 'razorpay_enable', 0);
        }
        if (! isset($inputArr['paytm_enable'])) {
            $inputArr = Arr::add($inputArr, 'paytm_enable', 0);
        }
        if (! isset($inputArr['paystack_enable'])) {
            $inputArr = Arr::add($inputArr, 'paystack_enable', 0);
        }
        if (! isset($inputArr['phone_pe_enable'])) {
            $inputArr = Arr::add($inputArr, 'phone_pe_enable', 0);
        }
        if (! isset($inputArr['flutterwave_enable'])) {
            $inputArr = Arr::add($inputArr, 'flutterwave_enable', 0);
        }
        foreach ($inputArr as $key => $value) {
            /** @var UserSetting $UserSetting */
            $tenantId = User::findOrFail(getLoggedInUserId())->tenant_id;
            $UserSetting = Setting::where('tenant_id', $tenantId)->where('key', '=', $key)->first();
            if (! $UserSetting) {
                Setting::create([
                    'tenant_id' => $tenantId,
                    'key' => $key,
                    'value' => $value,
                ]);
            } else {
                $UserSetting->update(['value' => $value]);
            }
        }
    }
}
