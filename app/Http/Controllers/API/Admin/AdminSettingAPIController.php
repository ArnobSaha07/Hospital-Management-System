<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use App\Repositories\SettingRepository;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSettingAPIController extends AppBaseController
{

    /** @var SettingRepository */
    private $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function editAdminSetting(): JsonResponse
    {
        $settings =[];
        $data = Setting::whereTenantId(getLoggedInUser()->tenant_id)->pluck('value', 'key')->toArray();
        $phone = (new \Propaganistas\LaravelPhone\PhoneNumber($data['hospital_phone']))->toLibPhoneObject();
        $phonenumber = $phone->getNationalNumber();
        $country_code = $phone->getCountryCode();
        $settings['app_name'] = $data['app_name'];
        $settings['company_name'] = $data['company_name'];
        $settings['hospital_email'] = $data['hospital_email'];
        $settings['hospital_phone'] = $phonenumber;
        $settings['country_code'] = isset($country_code)? '+'.$country_code :  "+91";
        $settings['enable_google_recaptcha'] = $data['enable_google_recaptcha'];
        $settings['app_logo'] = $data['app_logo'];
        $settings['favicon'] = asset($data['favicon']);

        return $this->sendResponse($settings, 'Admin Setting retrive successfully.');
    }

    public function updateAdminSetting(UpdateSettingRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->settingRepository->updateAdminSettingAPI($request->all());

        return $this->sendSuccess('Admin Setting Update Successfull.');
    }

}
