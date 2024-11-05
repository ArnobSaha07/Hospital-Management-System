<?php

namespace Database\Seeders;

use App\Models\SuperAdminSetting;
use Illuminate\Database\Seeder;

class AddrecaptchaSettingToSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reCaptcha = SuperAdminSetting::whereKey('enable_google_recaptcha')->exists();
        if (! $reCaptcha) {
            SuperAdminSetting::create([
                'key' => 'enable_google_recaptcha', 'value' => 0,
            ]);
            SuperAdminSetting::create([
                'key' => 'google_captcha_key', 'value' => '',
            ]);
            SuperAdminSetting::create([
                'key' => 'google_captcha_secret', 'value' => '',
            ]);
        }
    }
}
