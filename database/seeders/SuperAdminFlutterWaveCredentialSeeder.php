<?php

namespace Database\Seeders;

use App\Models\SuperAdminSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminFlutterWaveCredentialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payStackKey = SuperAdminSetting::where('key', 'paystack_key')->exists();
        if (! $payStackKey) {
            SuperAdminSetting::create([
                'key' => 'paystack_key',
                'value' => null,
            ]);
            SuperAdminSetting::create([
                'key' => 'paystack_secret',
                'value' => null,
            ]);

            SuperAdminSetting::create([
                'key' => 'paystack_enable',
                'value' => 0,
            ]);
        }

        $phonePe = SuperAdminSetting::where('key', 'phonepe_merchant_id')->exists();
        if (! $phonePe) {
            SuperAdminSetting::create([
                'key' => 'phonepe_merchant_id',
                'value' => null,
            ]);
            SuperAdminSetting::create([
                'key' => 'phonepe_merchant_user_id',
                'value' => null,
            ]);
            SuperAdminSetting::create([
                'key' => 'phonepe_env',
                'value' => null,
            ]);
            SuperAdminSetting::create([
                'key' => 'phonepe_salt_key',
                'value' => null,
            ]);
            SuperAdminSetting::create([
                'key' => 'phonepe_salt_index',
                'value' => null,
            ]);
            SuperAdminSetting::create([
                'key' => 'phonepe_merchant_transaction_id',
                'value' => null,
            ]);
            SuperAdminSetting::create([
                'key' => 'phonepe_enable',
                'value' => 0,
            ]);
        }

        $flutterWaveKey = SuperAdminSetting::where('key', 'flutterwave_key')->exists();
        if (! $flutterWaveKey) {
            SuperAdminSetting::create([
                'key' => 'flutterwave_key',
                'value' => null,
            ]);
            SuperAdminSetting::create([
                'key' => 'flutterwave_secret',
                'value' => null,
            ]);

            SuperAdminSetting::create([
                'key' => 'flutterwave_enable',
                'value' => 0,
            ]);
        }
    }
}
