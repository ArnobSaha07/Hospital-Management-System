<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App;
use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSuperAdminSettingRequest;
use App\Models\Setting;
use App\Models\SuperAdminCurrencySetting;
use App\Models\SuperAdminSetting;
use App\Repositories\SettingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingAPIController extends AppBaseController
{
    /** @var SettingRepository */
    private $settingRepository;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
    }

    public function editSuperAdminSettings()
    {
        $settings = [];
        $settings['app_name'] = $this->settingRepository->getSyncListForSuperAdmin()['app_name'];
        $settings['app_logo'] = $this->settingRepository->getSyncListForSuperAdmin()['app_logo'];
        $settings['favicon'] = $this->settingRepository->getSyncListForSuperAdmin()['favicon'];
        $settings['email'] = $this->settingRepository->getSyncListForSuperAdmin()['email'];
        $settings['address'] = $this->settingRepository->getSyncListForSuperAdmin()['address'];
        $settings['phone'] = $this->settingRepository->getSyncListForSuperAdmin()['phone'];
        $settings['default_country_code'] = $this->settingRepository->getSyncListForSuperAdmin()['default_country_code'];
        $settings['plan_expire_notification'] = $this->settingRepository->getSyncListForSuperAdmin()['plan_expire_notification'];
        $settings['default_language'] = $this->settingRepository->getSyncListForSuperAdmin()['default_language'];
        $settings['language'] =array(
            array("id" => "ar", "name" => "Arabic"),
            array("id" => "zh", "name" => "Chinese"),
            array("id" => "en", "name" => "English"),
            array("id" => "fr", "name" => "French"),
            array("id" => "de", "name" => "German"),
            array("id" => "pt", "name" => "Portuguese"),
            array("id" => "ru", "name" => "Russian"),
            array("id" => "es", "name" => "Spanish"),
            array("id" => "tr", "name" => "Turkish")
        );
        $settings['super_admin_currency'] = $this->settingRepository->getSyncListForSuperAdmin()['super_admin_currency'];
        $settings['currency'] = SuperAdminCurrencySetting::get();


        return $this->sendResponse($settings, 'Setting retrieve successfully.');
    }

    public function updateSuperAdminSettings(Request $request)
    {
        $input = $request->all();
        foreach($input as $key => $value){
            $setting = SuperAdminSetting::where('key', '=', $key)->first();
            $setting->update(['value' => $value]);
        }

        if (isset($input['app_logo']) && !empty($input['app_logo'])) {
            /** @var SuperAdminSetting $setting */
            $setting = SuperAdminSetting::where('key', '=', 'app_logo')->first();
            $setting->clearMediaCollection(SuperAdminSetting::PATH);
            $setting->addMedia($input['app_logo'])->toMediaCollection(
                SuperAdminSetting::PATH,
                config('app.media_disc')
            );
            $setting = $setting->refresh();
            $setting->update(['value' => $setting->logo_url]);
        }
        if (isset($input['favicon']) && !empty($input['favicon'])) {
            /** @var SuperAdminSetting $setting */
            $setting = SuperAdminSetting::where('key', '=', 'favicon')->first();
            $setting->clearMediaCollection(SuperAdminSetting::PATH);
            $setting->addMedia($input['favicon'])->toMediaCollection(SuperAdminSetting::PATH, config('app.media_disc'));
            $setting = $setting->refresh();
            $setting->update(['value' => $setting->logo_url]);
        }

        $settings = [];
        $settings['app_name'] = $this->settingRepository->getSyncListForSuperAdmin()['app_name'];
        $settings['app_logo'] = $this->settingRepository->getSyncListForSuperAdmin()['app_logo'];
        $settings['favicon'] = $this->settingRepository->getSyncListForSuperAdmin()['favicon'];
        $settings['email'] = $this->settingRepository->getSyncListForSuperAdmin()['email'];
        $settings['address'] = $this->settingRepository->getSyncListForSuperAdmin()['address'];
        $settings['phone'] = $this->settingRepository->getSyncListForSuperAdmin()['phone'];
        $settings['default_country_code'] = $this->settingRepository->getSyncListForSuperAdmin()['default_country_code'];
        $settings['plan_expire_notification'] = $this->settingRepository->getSyncListForSuperAdmin()['plan_expire_notification'];
        $settings['default_language'] = $this->settingRepository->getSyncListForSuperAdmin()['default_language'];
        $settings['language'] =array(
            array("id" => "ar", "name" => "Arabic"),
            array("id" => "zh", "name" => "Chinese"),
            array("id" => "en", "name" => "English"),
            array("id" => "fr", "name" => "French"),
            array("id" => "de", "name" => "German"),
            array("id" => "pt", "name" => "Portuguese"),
            array("id" => "ru", "name" => "Russian"),
            array("id" => "es", "name" => "Spanish"),
            array("id" => "tr", "name" => "Turkish")
        );
        $settings['super_admin_currency'] = $this->settingRepository->getSyncListForSuperAdmin()['super_admin_currency'];
        $settings['currency'] = SuperAdminCurrencySetting::get();

        return $this->sendResponse  ($settings,'Setting Updated successfully.');
    }
}
