<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Models\SuperAdminSetting;
use Arr;
use Carbon\Carbon;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\DiskDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileIsTooBig;
use Spatie\MediaLibrary\Exceptions\MediaCannotBeDeleted;

/**
 * Class SettingRepository
 *
 * @version February 19, 2020, 1:45 pm UTC
 */
class SettingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'app_name',
        'app_logo',
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
        return Setting::class;
    }

    public function getSyncList()
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    public function getSyncListForSuperAdmin()
    {
        return SuperAdminSetting::pluck('value', 'key')->toArray();
    }

    /**
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws MediaCannotBeDeleted
     */
    public function updateSetting(array $input)
    {
        if (isset($input['app_logo']) && ! empty($input['app_logo'])) {
            /** @var Setting $setting */
            $setting = Setting::where('key', '=', 'app_logo')->first();
            $setting->clearMediaCollection(Setting::PATH);
            $setting->addMedia($input['app_logo'])->toMediaCollection(Setting::PATH, config('app.media_disc'));
            $setting = $setting->refresh();
            $setting->update(['value' => $setting->logo_url]);
        }
        if (isset($input['favicon']) && ! empty($input['favicon'])) {
            /** @var Setting $setting */
            $setting = Setting::where('key', '=', 'favicon')->first();
            $setting->clearMediaCollection(Setting::PATH);
            $setting->addMedia($input['favicon'])->toMediaCollection(Setting::PATH, config('app.media_disc'));
            $setting = $setting->refresh();
            $setting->update(['value' => $setting->logo_url]);
        }

        $input['hospital_phone'] = preparePhoneNumber($input, 'hospital_phone');
        $loggingUserSetting = Setting::where('key', 'enable_google_recaptcha')->exists();
        if (! $loggingUserSetting) {
            $userSettings = [
                'key' => 'enable_google_recaptcha', 'value' => 0,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'tenant_id' => getLoggedInUser()->tenant_id,
            ];
            Setting::insert($userSettings);
        } else {
            $input['enable_google_recaptcha'] = (! isset($input['enable_google_recaptcha'])) ? false : $input['enable_google_recaptcha'];
        }

        $settingInputArray = Arr::only($input, [
            'app_name', 'company_name', 'hospital_email', 'hospital_phone', 'hospital_from_day', 'hospital_from_time',
            'hospital_address', 'current_currency', 'facebook_url', 'twitter_url', 'instagram_url', 'linkedIn_url',
            'about_us', 'enable_google_recaptcha',
        ]);
        foreach ($settingInputArray as $key => $value) {
            Setting::where('key', '=', $key)->first()->update(['value' => $value]);
        }
    }

    /**
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     * @throws MediaCannotBeDeleted
     */
    public function updateSuperAdminSetting(array $input)
    {

        $setting = SuperAdminSetting::where('key', '=', 'app_name')->first();
        $settingExpireNotification = SuperAdminSetting::where('key', '=', 'plan_expire_notification')->first();
        $settingCountryCode = SuperAdminSetting::where('key', '=', 'default_country_code')->first();
        $settingExpireNotification->update(['value' => $input['plan_expire_notification']]);
        $setting->update(['value' => $input['app_name']]);
        $settingCountryCode->update(['value' => $input['default_country_code']]);
        $currencySetting = SuperAdminSetting::where('key', '=', 'super_admin_currency')->first();
        $currencySetting->update(['value' => strtolower($input['super_admin_currency'])]);

        $input['enable_google_recaptcha'] = isset($input['enable_google_recaptcha']) ? 1 : 0;
        $captchaSetting = SuperAdminSetting::where('key', '=', 'enable_google_recaptcha')->first();
        $captchaSetting->update(['value' => strtolower($input['enable_google_recaptcha'])]);

        $captchaKey = SuperAdminSetting::where('key', '=', 'google_captcha_key')->first();
        $captchaKey->update(['value' => $input['google_captcha_key']]);

        $captchaSecret = SuperAdminSetting::where('key', '=', 'google_captcha_secret')->first();
        $captchaSecret->update(['value' => $input['google_captcha_secret']]);

        $manualData = SuperAdminSetting::where('key','=','manual_instruction')->first();
        $manualData->update(['value' => $input['manual_instruction']]);

        if (isset($input['app_logo']) && ! empty($input['app_logo'])) {
            /** @var SuperAdminSetting $setting */
            $setting = SuperAdminSetting::where('key', '=', 'app_logo')->first();
            $setting->clearMediaCollection(SuperAdminSetting::PATH);
            $setting->addMedia($input['app_logo'])->toMediaCollection(SuperAdminSetting::PATH,
                config('app.media_disc'));
            $setting = $setting->refresh();
            $setting->update(['value' => $setting->logo_url]);
        }
        if (isset($input['favicon']) && ! empty($input['favicon'])) {
            /** @var SuperAdminSetting $setting */
            $setting = SuperAdminSetting::where('key', '=', 'favicon')->first();
            $setting->clearMediaCollection(SuperAdminSetting::PATH);
            $setting->addMedia($input['favicon'])->toMediaCollection(SuperAdminSetting::PATH, config('app.media_disc'));
            $setting = $setting->refresh();
            $setting->update(['value' => $setting->logo_url]);
        }
        if(isset($input['default_language'])){
            $setting = SuperAdminSetting::where('key', '=', 'default_language')->exists();
            if(!$setting){
                SuperAdminSetting::create([
                    'key' => "default_language",
                    'value' => $input['default_language']
                ]);
            }else{
                $setting = SuperAdminSetting::where('key', '=', 'default_language')->first();
                $setting->update([
                    'value' => $input['default_language']
                ]);
            }
        }
    }

    public function updateSuperFooterAdminSetting(array $input)
    {
        $input['phone'] = preparePhoneNumber($input, 'phone');
        $inputArray = Arr::only($input, [
            'footer_text', 'email', 'phone', 'address', 'facebook_url', 'twitter_url',
            'instagram_url', 'linkedin_url',
        ]);
        foreach ($inputArray as $key => $value) {
            $setting = SuperAdminSetting::where('key', '=', $key)->first();
            $setting->update(['value' => $value]);
        }

        return $setting;
    }

    public function updatePaymentSettings(array $input)
    {
        $input['stripe_enable'] = ! isset($input['stripe_enable']) ? 0 : 1;
        $input['paypal_enable'] = ! isset($input['paypal_enable']) ? 0 : 1;
        $input['razorpay_enable'] = ! isset($input['razorpay_enable']) ? 0 : 1;
        $input['paystack_enable'] = ! isset($input['paystack_enable']) ? 0 : 1;
        $input['phonepe_enable'] = ! isset($input['phonepe_enable']) ? 0 : 1;
        $input['flutterwave_enable'] = ! isset($input['flutterwave_enable']) ? 0 : 1;
        $inputArray = Arr::only($input, [
            'stripe_key', 'stripe_secret', 'stripe_enable', 'paypal_enable', 'paypal_key', 'paypal_secret','paystack_enable','paystack_key','paystack_secret',
            'paypal_mode', 'razorpay_enable', 'razorpay_key', 'razorpay_secret','flutterwave_enable','flutterwave_key','flutterwave_secret','phonepe_enable','phonepe_merchant_id','phonepe_merchant_user_id',
            'phonepe_env','phonepe_salt_key','phonepe_salt_index','phonepe_merchant_transaction_id',
        ]);
        foreach ($inputArray as $key => $value) {
            $setting = SuperAdminSetting::where('key', '=', $key)->first();
            $setting->update(['value' => $value]);
        }

        return $setting;
    }

    public function updateAdminSettingAPI(array $input)
    {
        if (isset($input['app_logo']) && ! empty($input['app_logo'])) {
            /** @var Setting $setting */
            $setting = Setting::whereTenantId(getLoggedInUser()->tenant_id)->where('key', '=', 'app_logo')->first();
            $setting->clearMediaCollection(Setting::PATH);
            $setting->addMedia($input['app_logo'])->toMediaCollection(Setting::PATH, config('app.media_disc'));
            $setting = $setting->refresh();
            $setting->update(['value' => $setting->logo_url]);
        }
        if (isset($input['favicon']) && ! empty($input['favicon'])) {
            /** @var Setting $setting */
            $setting = Setting::whereTenantId(getLoggedInUser()->tenant_id)->where('key', '=', 'favicon')->first();
            $setting->clearMediaCollection(Setting::PATH);
            $setting->addMedia($input['favicon'])->toMediaCollection(Setting::PATH, config('app.media_disc'));
            $setting = $setting->refresh();
            $setting->update(['value' => $setting->logo_url]);
        }

        $input['hospital_phone'] = preparePhoneNumber($input, 'hospital_phone');
        $loggingUserSetting = Setting::where('key', 'enable_google_recaptcha')->exists();
        if (! $loggingUserSetting) {
            $userSettings = [
                'key' => 'enable_google_recaptcha', 'value' => 0,
                'created_at' => Carbon::now(), 'updated_at' => Carbon::now(),
                'tenant_id' => getLoggedInUser()->tenant_id,
            ];
            Setting::insert($userSettings);
        } else {
            $input['enable_google_recaptcha'] = (! isset($input['enable_google_recaptcha'])) ? false : $input['enable_google_recaptcha'];
        }

        $settingInputArray = Arr::only($input, [
            'app_name', 'company_name', 'hospital_email', 'hospital_phone', 'hospital_from_day', 'hospital_from_time',
            'hospital_address', 'current_currency', 'facebook_url', 'twitter_url', 'instagram_url', 'linkedIn_url',
            'about_us', 'enable_google_recaptcha',
        ]);
        foreach ($settingInputArray as $key => $value) {
            Setting::whereTenantId(getLoggedInUser()->tenant_id)->where('key', '=', $key)->first()->update(['value' => $value]);
        }
    }
}
