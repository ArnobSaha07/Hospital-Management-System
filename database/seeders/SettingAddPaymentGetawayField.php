<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingAddPaymentGetawayField extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         //PhonePe Payment keys
         Setting::create(['key' => 'phone_pe_enable','value' => null]);
         Setting::create(['key' => 'phonepe_merchant_id','value' => null]);
         Setting::create(['key' => 'phonepe_merchant_user_id','value' => null]);
         Setting::create(['key' => 'phonepe_env','value' => null]);
         Setting::create(['key' => 'phonepe_salt_key','value' => null]);
         Setting::create(['key' => 'phonepe_salt_index','value' => null]);
         Setting::create(['key' => 'phonepe_merchant_transaction_id','value' => null]);

         //FlutterWave Payment Keys
         Setting::create(['key' => 'flutterwave_enable','value' => null]);
         Setting::create(['key' => 'flutterwave_public_key','value' => null]);
         Setting::create(['key' => 'flutterwave_secret_key','value' => null]);
    }
}
