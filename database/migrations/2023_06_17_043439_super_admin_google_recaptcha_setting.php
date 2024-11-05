<?php

use App\Models\SuperAdminSetting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Artisan::call('db:seed', ['--class' => 'AddrecaptchaSettingToSuperAdminSeeder', '--force' => true]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $reCaptcha = SuperAdminSetting::whereKey('enable_google_recaptcha')->exists();
        if ($reCaptcha) {
            SuperAdminSetting::where('key', 'enable_google_recaptcha')->delete();
            SuperAdminSetting::where('key', 'google_captcha_key')->delete();
            SuperAdminSetting::where('key', 'google_captcha_secret')->delete();
        }
    }
};
