<?php

namespace Database\Seeders;

use App\Models\SuperAdminSetting;
use Illuminate\Database\Seeder;

class SuperAdminCredentialSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $stripKey = SuperAdminSetting::where('key', 'stripe_key')->first();
        if (! $stripKey) {
            SuperAdminSetting::create([
                'key' => 'stripe_key',
                'value' => '',
            ]);
            SuperAdminSetting::create([
                'key' => 'stripe_secret',
                'value' => '',
            ]);

            SuperAdminSetting::create([
                'key' => 'stripe_enable',
                'value' => 0,
            ]);
        }
        $payPalKey = SuperAdminSetting::where('key', 'paypal_key')->exists();
        if (! $payPalKey) {
            SuperAdminSetting::create([
                'key' => 'paypal_key',
                'value' => '',
            ]);
            SuperAdminSetting::create([
                'key' => 'paypal_secret',
                'value' => '',
            ]);
            SuperAdminSetting::create([
                'key' => 'paypal_mode',
                'value' => '',
            ]);

            SuperAdminSetting::create([
                'key' => 'paypal_enable',
                'value' => 0,
            ]);
        }
        $payPalKey = SuperAdminSetting::where('key', 'razorpay_key')->exists();
        if (! $payPalKey) {
            SuperAdminSetting::create([
                'key' => 'razorpay_key',
                'value' => '',
            ]);
            SuperAdminSetting::create([
                'key' => 'razorpay_secret',
                'value' => '',
            ]);

            SuperAdminSetting::create([
                'key' => 'razorpay_enable',
                'value' => 0,
            ]);
        }
    }
}
