<?php

use App\Models\BloodBank;
use App\Models\CurrencySetting;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorDepartment;
use App\Models\FrontSetting;
use App\Models\Invoice;
use App\Models\Module;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\PatientAdmission;
use App\Models\PatientCase;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SuperAdminCurrencySetting;
use App\Models\SuperAdminSetting;
use App\Models\User;
use App\Models\VaccinatedPatients;
use App\Models\ZoomOAuth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\DiskDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileIsTooBig;
use Stripe\Stripe;

/**
 * @return int
 */
function getLoggedInUserId()
{
    return Auth::id();
}

/**
 * @return User
 */
function getLoggedInUser()
{
    return Auth::user();
}

/**
 * @return mixed
 */
function getCurrentLoginUserLanguageName()
{
    return Auth::user()->language;
}

/**
 * @return bool
 */
function getLoggedinDoctor()
{
    return Auth::user()->hasRole(['Doctor']);
}

/**
 * @return bool
 */
function getLoggedinPatient()
{
    return Auth::user()->hasRole(['Patient']);
}

/**
 * return avatar url.
 *
 * @return string
 */
function getAvatarUrl()
{
    return 'https://ui-avatars.com/api/';
}

/**
 * return avatar full url.
 *
 * @param  int  $userId
 * @param  string  $name
 * @return string
 */
function getUserImageInitial($userId, $name)
{
    return getAvatarUrl()."?name=$name&size=100&rounded=true&color=fff&background=".getRandomColor($userId);
}

/**
 * return avatar full url.
 *
 * @param  int  $userId
 * @param  string  $name
 */
function getApiUserImageInitial($userId, $username): string
{
    $name = str_replace(' ', '', $username);
    return getAvatarUrl()."?name=$name&color=fff&background=".getRandomColor($userId);
}

/**
 * return random color.
 *
 * @param  int  $userId
 * @return string
 */
function getRandomColor($userId)
{
    $colors = ['329af0', 'fc6369', 'ffaa2e', '42c9af', '7d68f0'];
    $index = $userId % 5;

    return $colors[$index];
}

/**
 * @return string|string[]
 */
function removeCommaFromNumbers($number)
{
    return (gettype($number) == 'string' && ! empty($number)) ? str_replace(',', '', $number) : $number;
}

/**
 * @param  User  $user
 * @param  string  $image
 * @return mixed
 *
 * @throws DiskDoesNotExist
 * @throws FileDoesNotExist
 * @throws FileIsTooBig
 */
function storeProfileImage($user, $image)
{
    $mediaId = $user->addMedia($image)
        ->toMediaCollection(User::COLLECTION_PROFILE_PICTURES, config('app.media_disc'))->id;

    return $mediaId;
}

function superAdminCurrency()
{
    $current_currency = SuperAdminSetting::where('key', '=', 'super_admin_currency')->first()->value;
    $currency = SuperAdminCurrencySetting::where('currency_code', strtoupper($current_currency))->first();
    $currencyIcon = $currency->currency_icon ?? 'inr';

    return $currencyIcon;
}

/**
 * @param  User  $user
 * @param  string  $attachment
 * @return mixed
 *
 * @throws DiskDoesNotExist
 * @throws FileDoesNotExist
 * @throws FileIsTooBig
 */
function storeAttachments($user, $attachment)
{
    $media = $user->addMedia($attachment)
        ->toMediaCollection(User::COLLECTION_MAIL_ATTACHMENTS, config('app.media_disc'));

    return $media;
}

/**
 * @param  User  $user
 * @param  string  $image
 * @return mixed
 *
 * @throws DiskDoesNotExist
 * @throws FileDoesNotExist
 * @throws FileIsTooBig
 */
function updateProfileImage($user, $image)
{
    $user->clearMediaCollection(User::COLLECTION_PROFILE_PICTURES);
    $mediaId = $user->addMedia($image)
        ->toMediaCollection(User::COLLECTION_PROFILE_PICTURES, config('app.media_disc'))->id;

    return $mediaId;
}

function getLogoUrl()
{
    static $appLogo;

    if (empty($appLogo)) {
        $appLogo = Setting::where('key', '=', 'app_logo')->first();
    }

    return $appLogo->logo_url;
}

/**
 * @return Department
 */
function getDepartments()
{
    /** @var Department $departments */
    $departments = Department::all()->pluck('name', 'id');

    return $departments;
}

/**
 * @return DoctorDepartment
 */
function getDoctorsDepartments()
{
    /** @var DoctorDepartment $doctorDepartments */
    $doctorDepartments = DoctorDepartment::all()->pluck('title', 'id')->sort();

    return $doctorDepartments;
}

/**
 * @return mixed
 */
function getAppName()
{
    static $appName;

    if (empty($appName)) {
        $appName = Setting::where('key', '=', 'app_name')->first()->value;
    }

    return $appName;
}

/**
 * @param  array  $models
 * @param  string  $columnName
 * @param  int  $id
 * @return bool
 */
function canDelete($models, $columnName, $id)
{
    foreach ($models as $model) {
        $result = $model::where($columnName, $id)->exists();
        if ($result) {
            return true;
        }
    }

    return false;
}

/*
 * @return mixed
 */
function getCompanyName()
{
    $companyName = Setting::where('key', '=', 'company_name')->first()->value;

    return $companyName;
}

/**
 * @param  string  $columnName
 * @param  int  $id
 * @return bool
 */
function canDeletePayroll($model, $columnName, $id, $ownerType)
{
    $result = $model::where($columnName, $id)->where('owner_type', $ownerType)->exists();
    if ($result) {
        return true;
    }

    return false;
}

/**
 * @return array
 */
function getBloodGroups()
{
    return BloodBank::orderBy('blood_group')->pluck('blood_group', 'blood_group')->toArray();
}

/**
 * @param  string|null  $currency
 * @return string
 */
function getCurrenciesClass($currency = null)
{
    static $defaultCurrency;

    if (empty($defaultCurrency)) {
        if (! $currency) {
            $defaultCurrency = Setting::where('key', 'current_currency')->first()->value;
        }
    }

    switch ($defaultCurrency) {
        case 'inr':
            return 'fas fa-rupee-sign';
        case 'aud':
            return 'fas fa-dollar-sign';
        case 'usd':
            return 'fas fa-dollar-sign';
        case 'eur':
            return 'fas fa-euro-sign';
        case 'jpy':
            return 'fas fa-yen-sign';
        case 'gbp':
            return 'fas fa-pound-sign';
        case 'cad':
            return 'fas fa-dollar-sign';
        default:
            return 'fas fa-dollar-sign';

    }
}

/**
 * @param  string|null  $currency
 * @return string
 */
function getCurrenciesForSetting($currency = null)
{
    if (! $currency) {
        $defaultCurrency = Setting::where('key', 'current_currency')->first()->value;
    }

    switch ($currency) {
        case 'inr':
            return 'fas fa-rupee-sign';
        case 'aud':
            return 'fas fa-dollar-sign';
        case 'usd':
            return 'fas fa-dollar-sign';
        case 'eur':
            return 'fas fa-euro-sign';
        case 'jpy':
            return 'fas fa-yen-sign';
        case 'gbp':
            return 'fas fa-pound-sign';
        case 'cad':
            return 'fas fa-dollar-sign';
        default:
            return 'fas fa-dollar-sign';
    }
}

/**
 * @param  string|null  $currency
 * @return string
 */
function getCurrencyForPDF($currency = null)
{
    if (! $currency) {
        $currency = Setting::where('key', 'current_currency')->first()->value;
    }

    switch ($currency) {
        case 'inr':
            return 8377;
        case 'aud':
            return 36;
        case 'usd':
            return 36;
        case 'eur':
            return 8364;
        case 'jpy':
            return 165;
        case 'gbp':
            return 163;
        case 'cad':
            return 36;
    }
}

/**
 * @return mixed
 */
function getCurrentCurrency()
{
    /** @var Setting $currentCurrency */
    static $currentCurrency;

    if(getLoggedInUser()){
        $currentCurrency = Setting::where('key','current_currency')->Where('tenant_id', getLoggedInUser()->tenant_id)->first();
    }
    else{
        if (empty($currentCurrency)) {
            $currentCurrency = Setting::where('key', 'current_currency')->first();
        }
    }

    return $currentCurrency->value;
}

/**
 * @return mixed
 */
function getAPICurrentCurrency()
{
    /** @var Setting $currentCurrency */
    static $currentCurrency;

    if (empty($currentCurrency)) {
        $currentCurrency = Setting::where('key','current_currency')->Where('tenant_id', getLoggedInUser()->tenant_id)->first();
    }

    return $currentCurrency->value;
}

//total-Amount Invoice

function totalAmount()
{
    $totalSum = 0;
    $amount = Invoice::get();

    foreach ($amount as $amounts) {
        $total = 0;
        if ($amounts['discount'] != 0) {
            $total += $amounts['amount'] - ($amounts['amount'] * $amounts['discount'] / 100);
        } else {
            $totalSum += $amounts['amount'];
        }

        $totalSum += $total;
    }

    return $totalSum;
}

// number formatted code

// number formatted code

/**
 * @return string
 */
function formatCurrency($currencyValue)
{
    $isIndianCur = getCurrencySymbol() == '₹';
    $amountValue = $currencyValue;
    $precision = 2;
    //    $currencySuffix = ''; //thousand,lac, crore
    //    $numberOfDigits = countDigit(round($amountValue)); //this is call :)
    //    if ($numberOfDigits > 3) {
    //        if ($isIndianCur) {
    //            if ($numberOfDigits % 2 != 0) {
    //                $divider = divider($numberOfDigits - 1);
    //            } else {
    //                $divider = divider($numberOfDigits);
    //            }
    //        } else {
    //            $divider = 1000;
    //        }
    //    } else {
    //        $divider = 1;
    //    }

    //    $formattedAmount = $amountValue / $divider;
    //    $formattedAmount = number_format($formattedAmount, 2);
    //    if ($numberOfDigits == 4 || $numberOfDigits == 5) {
    //        $currencySuffix = 'k';
    //    }
    //    if ($numberOfDigits == 6 || $numberOfDigits == 7) {
    //        $currencySuffix = $isIndianCur ? 'Lac' : 'k';
    //    }
    //    if ($numberOfDigits == 8 || $numberOfDigits == 9) {
    //        $currencySuffix = $isIndianCur ? 'Cr' : 'k';
    //    }
    if ($amountValue < 900) {
        // 0 - 900
        $numberFormat = number_format($amountValue, $precision);
        $suffix = '';
    } else {
        if ($amountValue < 900000) {
            // 0.9k-850k
            $numberFormat = number_format($amountValue / 1000, $precision);
            $suffix = 'K';
        } else {
            if ($amountValue < 900000000) {
                // 0.9m-850m
                $numberFormat = number_format($amountValue / 1000000, $precision);
                $suffix = 'M';
            } else {
                if ($amountValue < 900000000000) {
                    // 0.9b-850b
                    $numberFormat = number_format($amountValue / 1000000000, $precision);
                    $suffix = 'B';
                } else {
                    // 0.9t+
                    $numberFormat = number_format($amountValue / 1000000000000, $precision);
                    $suffix = 'T';
                }
            }
        }
    }

    // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
    // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ($precision > 0) {
        $dotZero = '.'.str_repeat('0', $precision);
        $numberFormat = str_replace($dotZero, '', $numberFormat);
    }

    //  return $formattedAmount.' '.$currencySuffix;

    return $numberFormat.$suffix;
}

/**
 * @return int|string
 */
function convertCurrency($amount)
{
    // Convert Price to Crores or Millions or Thousands
    $length = strlen(round($amount));

    if (empty($amount)) {
        return 0;
    }

    if ($length == 4 || $length == 5 || $length == 6) {
        // Thousand
        $amount = $amount / 1000;
        $amount = round($amount, 2);
        $currency = $amount.' '.'K';
    } elseif ($length == 7) {
        // Millions
        $amount = $amount / 1000000;
        $amount = round($amount, 2);
        $currency = $amount.' '.'M';
    } elseif ($length == 8 || $length == 9) {
        // Crores
        $amount = $amount / 10000000;
        $amount = round($amount, 2);
        $currency = $amount.' '.'Cr';
    } else {
        $currency = $amount;
    }

    return $currency;
}

/**
 * @return int
 */
function countDigit($number)
{
    return strlen($number);
}

/**
 * @return int|string
 */
function divider($numberOfDigits)
{
    $tens = '1';
    if ($numberOfDigits > 8) {
        return 10000000;
    }

    while (($numberOfDigits - 1) > 0) {
        $tens .= '0';
        $numberOfDigits--;
    }

    return $tens;
}

/**
 * @param  array  $input
 * @param  string  $key
 * @return string|null
 */
function preparePhoneNumber($input, $key)
{
    return (! empty($input[$key])) ? '+'.$input['prefix_code'].$input[$key] : null;
}

/**
 * @return mixed
 */
function getDoctorDepartment($doctorDepartmentId)
{
    return DoctorDepartment::where('id', $doctorDepartmentId)->value('title');
}

/**
 * @return Collection
 */
function getPatientsList($userOwnerId)
{
    $patientCase = PatientCase::with('patient.patientUser')->where(
        'doctor_id',
        '=',
        $userOwnerId
    )->where('status', '=', 1)->get()->pluck('patient.user_id', 'id');

    $patientAdmission = PatientAdmission::with('patient.patientUser')->where(
        'doctor_id',
        '=',
        $userOwnerId
    )->where('status', '=', 1)->get()->pluck('patient.user_id', 'id');

    $arrayMerge = array_merge($patientAdmission->toArray(), $patientCase->toArray());
    $patientIds = array_unique($arrayMerge);

    $patients = Patient::with('patientUser')->whereIn('user_id', $patientIds)
        ->whereHas('patientUser', function (Builder $query) {
            $query->where('status', 1);
        })->get()->pluck('patientUser.full_name', 'id');

    return $patients;
}

/**
 * @return array
 */
function getCurrencies()
{
    //    $currencyPath = file_get_contents(storage_path().'/currencies/defaultCurrency.json');
    //    $currenciesData = json_decode($currencyPath, true);
    $currenciesData = CurrencySetting::all();
    $currencies = [];

    foreach ($currenciesData as $currency) {
        $convertCode = strtolower($currency['currency_code']);
        $currencies[$convertCode] = [
            'symbol' => $currency['currency_icon'],
            'name' => $currency['currency_name'],
        ];
    }

    return $currencies;
}

/**
 * @return mixed
 */
function getCurrencySymbol()
{
    $currenciesData = CurrencySetting::all();

    return collect($currenciesData)->where(
        'currency_code',
        strtoupper(getCurrentCurrency())
    )->pluck('currency_icon')->first();
}

/**
 * @return mixed
 */
function getAPICurrencySymbol()
{
    $currenciesData = CurrencySetting::all();

    return collect($currenciesData)->where(
        'currency_code',
        strtoupper(getAPICurrentCurrency())
    )->pluck('currency_icon')->first();
}

/**
 * @return array
 */
function getSettingValue()
{
    return Setting::all()->keyBy('key');
}

/**
 * @return array
 */
function getSuperAdminSettingValue()
{
    return SuperAdminSetting::all()->keyBy('key');
}

/**
 * @return array
 */
function getSuperAdminSettingKeyValue($key)
{
    /** @var SuperAdminSetting $setting */
    $setting = SuperAdminSetting::where('key', '=', $key)->value('value');

    return $setting;
}

/**
 * @return mixed
 */
function getFrontSettingValue($type, $key)
{
    return FrontSetting::whereType($type)->where('key', $key)->value('value');
}

function setStripeApiKey($tenantId)
{
    $stripeKey = Setting::whereTenantId($tenantId)->where('key', '=', 'stripe_secret')->first();
    $stripe = Stripe::setApiKey($stripeKey->value);

    return $stripe;
}

function setSuperAdminStripeApiKey()
{
    $secretKey = '';
    $stripeKey = getSuperAdminSettingKeyValue('stripe_key');
    $stripeSecret = getSuperAdminSettingKeyValue('stripe_secret');
    if (isset($stripeKey) && ! is_null($stripeKey) && isset($stripeSecret) && ! is_null($stripeSecret)) {
        $secretKey = getSuperAdminSettingKeyValue('stripe_key');
    } else {
        $secretKey = config('services.stripe.secret_key');
    }
    Stripe::setApiKey($secretKey);
}
function getSuperAdminStripeApiKey()
{
    $secretKey = '';
    $stripeKey = getSuperAdminSettingKeyValue('stripe_key');
    $stripeSecret = getSuperAdminSettingKeyValue('stripe_secret');
    if (isset($stripeKey) && ! is_null($stripeKey) && isset($stripeSecret) && ! is_null($stripeSecret)) {
        $secretKey = getSuperAdminSettingKeyValue('stripe_key');
    } else {
        $secretKey = config('services.stripe.secret_key');
    }

    return $secretKey;
}

function superAdminStripeApiKey()
{
    $secretKey = '';
    $stripeKey = getSuperAdminSettingKeyValue('stripe_key');
    $stripeSecret = getSuperAdminSettingKeyValue('stripe_secret');
    if (isset($stripeKey) && ! is_null($stripeKey) && isset($stripeSecret) && ! is_null($stripeSecret)) {
        $secretKey = getSuperAdminSettingKeyValue('stripe_secret');
    } else {
        $secretKey = config('services.stripe.key');
    }

    return $secretKey;
}

/**
 * @return string
 */
function getFileName($fileName, $attachment)
{
    $fileNameExtension = $attachment->getClientOriginalExtension();

    $newName = $fileName.'-'.time();

    return $newName.'.'.$fileNameExtension;
}

/**
 * @param  array  $data
 */
function addNotification($data)
{
    $notificationRecord = [
        'type' => $data[0],
        'user_id' => $data[1],
        'notification_for' => $data[2],
        'title' => $data[3],
    ];

    if ($user = User::find($data[1])) {
        Notification::create($notificationRecord);
    }
}

/**
 * @param  array  $role
 * @return Notification[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|Collection
 */
function getNotification($role)
{
    return Notification::whereUserId(Auth::id())->whereNotificationFor(Notification::NOTIFICATION_FOR[$role])->where(
        'read_at',
        null
    )->orderByDesc('created_at')->toBase()->get();
}

/**
 * @param  array  $data
 * @return array
 */
function getAllNotificationUser($data)
{
    return array_filter($data, function ($key) {
        return $key != getLoggedInUserId();
    }, ARRAY_FILTER_USE_KEY);
}

/**
 * @param  array  $notificationFor
 * @return string
 */
function getNotificationIcon($notificationFor)
{
    switch ($notificationFor) {
        case 1:
            return 'fas fa-calendar-check';
        case 2:
            return 'fas fa-file-invoice';
        case 3:
            return 'fa fa-rupee-sign';
        case 7:
        case 4:
            return 'fas fa-notes-medical';
        case 5:
            return 'fas fa-stethoscope';
        case 8:
        case 6:
            return 'fas fa-prescription';
        case 9:
            return 'fas fa-diagnoses';
        case 10:
            return 'fas fa-chart-pie';
        case 11:
            return 'fas fa-money-bill-wave';
        case 12:
            return 'fas fa-user-injured';
        case 13:
            return 'fa fa-briefcase-medical';
        case 14:
            return 'fa fa-users';
        case 15:
            return 'fa fa-clipboard';
        case 16:
            return 'fas fa-user-plus';
        case 17:
            return 'fas fa-ambulance';
        case 18:
            return 'fas fa-box';
        case 19:
            return 'fas fa-wallet';
        case 20:
            return 'fas fa-money-check';
        case 21:
            return 'fa fa-video';
        case 22:
            return 'fa fa-file-video';
        default:
            return 'fa fa-inbox';
    }
}

/**
 * @return string[]
 */
function getLanguages()
{
    return User::LANGUAGES;
}

/**
 * @return mixed|null
 */
function checkLanguageSession()
{
    $defaultLanguage = getSuperAdminSettingValue()['default_language']->value;

    if (Session::has('languageName')) {
            return Session::get('languageName');
    }elseif(Session::has('languageChangeName') && Session::get('languageChangeName') != $defaultLanguage){
        App::setLocale(Session::get('languageChangeName'));
        return Session::get('languageChangeName');
    }else{
        if(!empty($defaultLanguage)){
            App::setLocale($defaultLanguage);
            return $defaultLanguage;
        }
    }


    return 'en';
}

function headerLanguageName()
{
    $defaultLanguage = getSuperAdminSettingValue()['default_language']->value;
    if (Session::has('languageChangeName') && Session::get('languageChangeName') != $defaultLanguage) {
        return Session::get('languageChangeName');
    }else{
        if(!empty($defaultLanguage)){
            return $defaultLanguage;
        }
    }

    return 'en';
}

/**
 * @return mixed|null
 */
function getCurrentLanguageName()
{
    return getLanguages()[checkLanguageSession()];
}

/**
 * @return mixed|null
 */
function getHeaderLanguageName()
{
    return getLanguages()[headerLanguageName()];
}

/*
 * @param $input
 *
 * @param  null  $vaccinatedPatient
 * @param  null  $isCreate
 * @return bool
 */
function checkVaccinatePatientValidation($input, $vaccinatedPatient = null, $isCreate = null)
{
    $patients = VaccinatedPatients::where('patient_id', $input['patient_id'])->get();
    $returnValue = false;
    if ($isCreate) {
        if ($input['patient_id'] != $vaccinatedPatient->patient_id) {
            $patients = VaccinatedPatients::where('patient_id', '!=', $vaccinatedPatient->patient_id)->get();
        }
    }

    foreach ($patients as $patient) {
        if ($input['patient_id'] == $patient->patient_id &&
            $input['vaccination_id'] == $patient->vaccination_id &&
            $input['dose_number'] == $patient->dose_number) {
            $returnValue = true;
            break;
        }
    }

    return $returnValue;
}

function removeFile($model, $mediaCollection)
{
    $model->clearMediaCollection($mediaCollection);
}

function redirectToDashboard(): string
{
    $user = Auth::user();
    if ($user->hasRole('Super Admin')) {
        return 'super-admin/dashboard';
    } else {
        if ($user->hasRole('Admin')) {
            return 'dashboard';
        } elseif ($user->hasRole(['Receptionist'])) {
            $module = Module::whereNotIn(
                'name',
                Module::Receptionist
            )->whereIsActive(1)->whereTenantId(getLoggedInUser()->tenant_id)->first();
            if ($module) {
                return route($module->route);
            }

            return 'appointments';
        } elseif ($user->hasRole(['Doctor'])) {
            $module = Module::whereNotIn(
                'name',
                Module::Doctor
            )->whereIsActive(1)->whereTenantId(getLoggedInUser()->tenant_id)->first();
            if ($module) {
                return route($module->route);
            }

            return 'employee/doctor';
        } elseif ($user->hasRole(['Pharmacist'])) {
            $module = Module::whereNotIn(
                'name',
                Module::Pharmacist
            )->whereIsActive(1)->whereTenantId(getLoggedInUser()->tenant_id)->first();
            if ($module) {
                return route($module->route);
            }

            return 'employee/doctor';
        } elseif ($user->hasRole(['Lab Technician'])) {
            $module = Module::whereNotIn(
                'name',
                Module::LAB_TECHNICIAN
            )->whereIsActive(1)->whereTenantId(getLoggedInUser()->tenant_id)->first();
            if ($module) {
                return route($module->route);
            }

            return 'employee/doctor';
        } elseif ($user->hasRole(['Case Manager'])) {
            $module = Module::whereNotIn(
                'name',
                Module::CASE_HANDLER
            )->whereIsActive(1)->whereTenantId(getLoggedInUser()->tenant_id)->first();
            if ($module) {
                return route($module->route);
            }

            return 'employee/doctor';
        } elseif ($user->hasRole(['Patient'])) {
            // $module = Module::whereNotIn(
            //     'name',
            //     Module::Patient
            // )->whereIsActive(1)->whereTenantId(getLoggedInUser()->tenant_id)->first();
            // if ($module) {
            //     return route($module->route);
            // }

            // return 'patient/my-cases';
            return 'patient/dashboard';
        } elseif ($user->hasRole(['Nurse'])) {
            $module = Module::whereNotIn(
                'name',
                Module::Nurse
            )->whereIsActive(1)->whereTenantId(getLoggedInUser()->tenant_id)->first();
            if ($module) {
                return route($module->route);
            }

            return 'bed-types';
        } elseif ($user->hasRole(['Accountant'])) {
            $module = Module::whereNotIn(
                'name',
                Module::Accountant
            )->whereIsActive(1)->whereTenantId(getLoggedInUser()->tenant_id)->first();
            if ($module) {
                return route($module->route);
            }

            return 'accounts';
        } else {
            return 'employee/notice-board';
        }
    }
}

/**
 * @return User
 */
function getUser()
{
    $loggedInUser = getLoggedInUser();
    $user = null;
    if (! empty($loggedInUser) && request()->segment(2) != $loggedInUser->username || empty($loggedInUser)) {
        $uName = null;
        $uName = request()->segment(2);
        $user = User::withoutGlobalScope(new \Stancl\Tenancy\Database\TenantScope())
            ->where('username', $uName)
            ->first();

        if ($user == null) {
            return $loggedInUser;
        }
    } else {
        $user = getLoggedInUser();
    }

    return $user;
}

/**
 * @return array
 */
function getSchedulesTimingSlot()
{
    $period = new CarbonPeriod('00:00', '15 minutes', '24:00'); // for create use 24 hours format later change format
    $slots = [];
    foreach ($period as $item) {
        $slots[$item->format('H:i')] = $item->format('H:i');
    }

    return $slots;
}

function getSubscriptionPlanCurrencyIcon($key): string
{
    $currencyPath = file_get_contents(storage_path().'/currencies/defaultCurrency.json');
    $currenciesData = json_decode($currencyPath, true)['currencies'];
    $currency = collect($currenciesData)->firstWhere(
        'code',
        strtoupper($key)
    );

    return $currency['symbol'];
}

function getSubscriptionPlanCurrencyCode($key): string
{
    //    $currencyPath = file_get_contents(storage_path().'/currencies/defaultCurrency.json');
    //    $currenciesData = json_decode($currencyPath, true)['currencies'];
    //    $currency = collect($currenciesData)->firstWhere('code',
    //        strtoupper($key));

    $currenciesData = SuperAdminCurrencySetting::pluck('currency_code', DB::raw('LOWER(currency_code)'))->toArray();

    return $currenciesData[$key] ?? '';
}

/**
 * @return array
 */
function zeroDecimalCurrencies()
{
    return [
        'BIF',
        'CLP',
        'DJF',
        'GNF',
        'JPY',
        'KMF',
        'KRW',
        'MGA',
        'PYG',
        'RWF',
        'UGX',
        'VND',
        'VUV',
        'XAF',
        'XOF',
        'XPF',
    ];
}

function getCurrencyFullName(): array
{
    $currencyPath = file_get_contents(storage_path().'/currencies/defaultCurrency.json');
    $currenciesData = json_decode($currencyPath, true);
    $currencies = [];

    foreach ($currenciesData['currencies'] as $currency) {
        $convertCode = strtolower($currency['code']);
        $currencies[$convertCode] = $currency['symbol'].' - '.$currency['code'].' '.$currency['name'];
    }

    return $currencies;
}

/**
 * @return mixed
 */
function isSubscriptionAssign()
{
    return Subscription::where('status', Subscription::ACTIVE)->where('user_id', \Auth::user()->id)->first();
}

/**
 * @return array
 */
function isSubscriptionExpired()
{
    $subscription = Subscription::with('subscriptionPlan')
        ->where('status', Subscription::ACTIVE)
        ->where('user_id', getLoggedInUserId())
        ->first();

    if ($subscription && $subscription->isExpired()) {
        return [
            'status' => true,
            'message' => 'Your current plan is expired, please choose new plan.',
        ];
    }

    if ($subscription == null) {
        return [
            'status' => true,
            'message' => 'Please choose a plan to continue the service.',
        ];
    }

    $subscriptionEndDate = Carbon::parse($subscription->ends_at);
    $currentDate = Carbon::parse(Carbon::now())->format('Y-m-d');

    $expirationMessage = '';
    $diffInDays = $subscriptionEndDate->diffInDays($currentDate);
    $superAdminSettingValue = getSuperAdminSettingValue();
    if ($diffInDays <= $superAdminSettingValue['plan_expire_notification']['value'] && $diffInDays != 0) {
        $expirationMessage = "Your '{$subscription->subscriptionPlan->name}' is about to expired in {$diffInDays} days.";
    }

    return [
        'status' => $subscriptionEndDate->diffInDays($currentDate) <= $superAdminSettingValue['plan_expire_notification']['value'],
        'message' => $expirationMessage,
    ];
}

/**
 * @return array
 */
function getPayPalSupportedCurrencies()
{
    return [
        'AUD', 'BRL', 'CAD', 'CNY', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK',
        'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'USD',
    ];
}

///**
// * This function will be used for getting the specific menu that has being provided in the subscription plan.
// *
// * @param $routeName
// *
// * @return bool
// */
//function isFeatureAllowToUse($routeName)
//{
//    if (getLoggedInUser()->hasRole('Super Admin')) {
//        return true;
//    }
//
//    $menuAccess = false;
//    $authUser = getLoggedInUser();
//    if (!$authUser->hasRole('Admin')) {
//        $authUser = User::withoutGlobalScope(new \Stancl\Tenancy\Database\TenantScope())
//            ->where('tenant_id', $authUser->owner->tenant_id)->first();
//    }
//
////    $subscription = Subscription::with('subscriptionPlan.planFeatures')
////        ->where('status', Subscription::ACTIVE)
////        ->where('user_id', $authUser->id)->first();
//
//    $subscription = DB::table('subscriptions')
//        ->where('status', Subscription::ACTIVE)
//        ->where('user_id', $authUser->id)
//        ->select('subscription_plan_id')
//        ->first();
//
//    if (!$subscription) {
//        return false;
//    }
//
//    $subscriptionPlanFeaturesId = DB::table('feature_subscriptionplan')
//        ->where('subscription_plan_id', $subscription->subscription_plan_id)
//        ->pluck('feature_id')->toArray();
//
//
////    $subscriptionPlanFeaturesId = $subscription->subscriptionPlan->planFeatures->pluck('feature_id')->toArray();
//
//    $feature = DB::table('features')->whereJsonContains('route->route_name', [$routeName])->first();
////    whereJsonContains not support than use below query
////    $feature = DB::table('features')->where('route', 'like', '%'.$routeName.'%')->first();
//    $featureId = $feature->id;
//    if ($feature->has_parent != 0) {
//        $featureId = $feature->has_parent;
//    }
//
//    if ($feature != null && in_array($featureId, $subscriptionPlanFeaturesId)) {
//        $menuAccess = true;
//    }
//
//    return $menuAccess;
//}

/**
 * @return mixed
 */
function getCurrentActiveSubscriptionPlan()
{
    if (! Auth::user()) {
        return null;
    }

    return Subscription::where('status', Subscription::ACTIVE)
        ->where('user_id', \Auth::user()->id)
        ->first();
}

/**
 * @return string
 */
function getParseDate($date)
{
    return Carbon::parse($date);
}

/**
 * @return bool
 */
function isAuth()
{
    return Auth::check() ? true : false;
}

/**
 * @return Builder|Model|object|null
 */
function currentActiveSubscription()
{
    if (! Auth::user()) {
        return null;
    }

    return Subscription::with('subscriptionPlan')
        ->where('status', Subscription::ACTIVE)
        ->where('user_id', \Auth::user()->id)
        ->first();
}

/**
 * @return array|void
 */
function getProratedPlanData($planIDChosenByUser)
{
    /** @var SubscriptionPlan $subscriptionPlan */
    $subscriptionPlan = SubscriptionPlan::findOrFail($planIDChosenByUser);
    $newPlanDays = $subscriptionPlan->frequency == SubscriptionPlan::MONTH ? 30 : 365;

    $currentSubscription = currentActiveSubscription();
    $frequency = $subscriptionPlan->frequency == SubscriptionPlan::MONTH ? 'Monthly' : 'Yearly';

    $startsAt = Carbon::now();

    $carbonParseStartAt = Carbon::parse($currentSubscription->starts_at);
    $usedDays = $carbonParseStartAt->copy()->diffInDays($startsAt);
    $totalExtraDays = 0;
    $totalDays = $newPlanDays;

    $endsAt = Carbon::now()->addDays($newPlanDays);

    $startsAt = $startsAt->copy()->format('jS M, Y');
    if ($usedDays <= 0) {
        $startsAt = $carbonParseStartAt->copy()->format('jS M, Y');
    }

    if (! $currentSubscription->isExpired() && ! checkIfPlanIsInTrial($currentSubscription)) {
        $amountToPay = 0;

        $currentPlan = $currentSubscription->subscriptionPlan; // TODO: take fields from subscription

        // checking if the current active subscription plan has the same price and frequency in order to process the calculation for the proration
        $planPrice = $currentPlan->price;
        $planFrequency = $currentPlan->frequency;
        if ($planPrice != $currentSubscription->plan_amount || $planFrequency != $currentSubscription->plan_frequency) {
            $planPrice = $currentSubscription->plan_amount;
            $planFrequency = $currentSubscription->plan_frequency;
        }

        $frequencyDays = $planFrequency == SubscriptionPlan::MONTH ? 30 : 365;
        $perDayPrice = round($planPrice / $frequencyDays, 2);

        $remainingBalance = round($planPrice - ($perDayPrice * $usedDays), 2);

        if ($remainingBalance < $subscriptionPlan->price) { // adjust the amount in plan
            $amountToPay = round($subscriptionPlan->price - $remainingBalance, 2);
        } else {
            $endsAt = Carbon::now();
            $perDayPriceOfNewPlan = round($subscriptionPlan->price / $newPlanDays, 2);
            $totalExtraDays = round($remainingBalance / $perDayPriceOfNewPlan);

            $endsAt = $endsAt->copy()->addDays($totalExtraDays);
            $totalDays = $totalExtraDays;
        }

        return [
            'startDate' => $startsAt,
            'name' => $subscriptionPlan->name.' / '.$frequency,
            'trialDays' => $subscriptionPlan->trial_days,
            'remainingBalance' => $remainingBalance,
            'endDate' => $endsAt->format('jS M, Y'),
            'amountToPay' => $amountToPay,
            'usedDays' => $usedDays,
            'totalExtraDays' => $totalExtraDays,
            'totalDays' => $totalDays,
        ];
    }

    return [
        'name' => $subscriptionPlan->name.' / '.$frequency,
        'trialDays' => $subscriptionPlan->trial_days,
        'startDate' => $startsAt,
        'endDate' => $endsAt->format('jS M, Y'),
        'remainingBalance' => 0,
        'amountToPay' => $subscriptionPlan->price,
        'usedDays' => $usedDays,
        'totalExtraDays' => $totalExtraDays,
        'totalDays' => $totalDays,
    ];
}

/**
 * @return array
 */
function getCurrentPlanDetails()
{
    $currentSubscription = currentActiveSubscription();
    $isExpired = $currentSubscription->isExpired();
    $currentPlan = $currentSubscription->subscriptionPlan;

    if ($currentPlan->price != $currentSubscription->plan_amount) {
        $currentPlan->price = $currentSubscription->plan_amount;
    }

    $startsAt = Carbon::now();
    $totalDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($currentSubscription->ends_at);
    $usedDays = Carbon::parse($currentSubscription->starts_at)->diffInDays($startsAt);
    $remainingDays = $totalDays - $usedDays;

    $frequency = $currentSubscription->plan_frequency == SubscriptionPlan::MONTH ? 'Monthly' : 'Yearly';

    $days = $currentSubscription->plan_frequency == SubscriptionPlan::MONTH ? 30 : 365;

    $perDayPrice = round($currentPlan->price / $days, 2);

    if (checkIfPlanIsInTrial($currentSubscription)) {
        $remainingBalance = 0.00;
        $usedBalance = 0.00;
    } else {
        $remainingBalance = $currentPlan->price - ($perDayPrice * $usedDays);
        $usedBalance = $currentPlan->price - $remainingBalance;
    }

    return [
        'name' => $currentPlan->name.' / '.$frequency,
        'trialDays' => $currentPlan->trial_days,
        'startAt' => Carbon::parse($currentSubscription->starts_at)->translatedFormat('jS M, Y'),
        'endsAt' => Carbon::parse($currentSubscription->ends_at)->translatedFormat('jS M, Y'),
        'usedDays' => $usedDays,
        'remainingDays' => $remainingDays,
        'totalDays' => $totalDays,
        'usedBalance' => $usedBalance,
        'remainingBalance' => $remainingBalance,
        'isExpired' => $isExpired,
        'currentPlan' => $currentPlan,
    ];
}

/**
 * @param  Subscription  $currentSubscription
 * @return bool
 */
function checkIfPlanIsInTrial($currentSubscription)
{
    $now = Carbon::now();
    if (! empty($currentSubscription->trial_ends_at) && $currentSubscription->trial_ends_at > $now) {
        return true;
    }

    return false;
}

/**
 * @return false|string
 */
function getMenuLinks($menu)
{
    //ipd opd routes
    if ($menu == User::MAIN_IPD_OPD) {
        $defaultRoute = route('ipd.patient.index');
        $subMenus = ['IPD Patients', 'OPD Patients'];
    }
    //bed management routes
    if ($menu == User::MAIN_BED_MGT) {
        $defaultRoute = route('bed-assigns.index');
        $subMenus = ['Bed Types', 'Beds', 'Bed Assigns'];
    }
    //billing module
    if ($menu == User::MAIN_BILLING_MGT) {
        $defaultRoute = route('accounts.index');
        $subMenus = [
            'Accounts', 'Employee Payrolls', 'Invoices', 'Payments', 'Payment Reports', 'Advance Payments', 'Bills', 'Manual Billing Payments'
        ];
    }
    //blood bank module
    if ($menu == User::MAIN_BLOOD_BANK_MGT) {
        $defaultRoute = route('blood-banks.index');
        $subMenus = ['Blood Banks', 'Blood Donors', 'Blood Donations', 'Blood Issues'];
    }
    //document module
    if ($menu == User::MAIN_DOCUMENT) {
        $defaultRoute = route('documents.index');
        $subMenus = ['Documents', 'Document Types'];
    }
    //doctor module
    if ($menu == User::MAIN_DOCTOR) {
        $defaultRoute = route('doctors.index');
        $subMenus = ['Doctors', 'Departments', 'Schedules', 'Doctor Holidays', 'Breaks'];
    }

    //prescription module
    if ($menu == User::MAIN_PRESCRIPTION) {
        $defaultRoute = route('prescriptions.index');
        $subMenus = ['Prescriptions'];
    }

    //diagnosis module
    if ($menu == User::MAIN_DIAGNOSIS) {
        $defaultRoute = route('diagnosis.category.index');
        $subMenus = ['Diagnosis Categories', 'Diagnosis Tests'];
    }
    //finance module
    if ($menu == User::MAIN_FINANCE) {
        $defaultRoute = route('incomes.index');
        $subMenus = ['Income', 'Expense'];
    }
    //    Front Office
    if ($menu == User::MAIN_FRONT_OFFICE) {
        $defaultRoute = route('call_logs.index');
        $subMenus = ['Call Logs', 'Visitors', 'Postal', 'Receive', 'Postal', 'Dispatch'];
    }
    // Hospital Charge
    if ($menu == User::MAIN_HOSPITAL_CHARGE) {
        $defaultRoute = route('charge-categories.index');
        $subMenus = ['Charge Categories', 'Charges', 'Doctor OPD Charges'];
    }
    // Inventory
    if ($menu == User::MAIN_INVENTORY) {
        $defaultRoute = route('item-categories.index');
        $subMenus = ['Items Categories', 'Items', 'Item Stocks', 'Issued Items'];
    }
    // live Consolation
    if ($menu == User::MAIN_LIVE_CONSULATION) {
        $defaultRoute = route('live.consultation.index');
        $subMenus = ['Live Consultations', 'Live Meetings'];
    }
    // medicines
    if ($menu == User::MAIN_MEDICINES) {
        $defaultRoute = route('categories.index');
        $subMenus = ['Medicines', 'Medicine Brands', 'Medicine Categories'];
    }
    // patient case
    if ($menu == User::MAIN_PATIENT_CASE) {
        $defaultRoute = route('patients.index');
        $subMenus = ['Patients', 'Cases', 'Case Handlers', 'Patient Admissions'];
    }
    // Pathology
    if ($menu == User::MAIN_PATHOLOGY) {
        $defaultRoute = route('pathology.category.index');
        $subMenus = ['Pathology Categories', 'Pathology Tests'];
    }
    // Report
    if ($menu == User::MAIN_REPORT) {
        $defaultRoute = route('birth-reports.index');
        $subMenus = ['Birth Reports', 'Death Reports', 'Investigation Reports', 'Operation Reports'];
    }
    // Radiology
    if ($menu == User::MAIN_RADIOLOGY) {
        $defaultRoute = route('radiology.category.index');
        $subMenus = ['Radiology Categories', 'Radiology Tests'];
    }
    // Service
    if ($menu == User::MAIN_SERVICE) {
        $defaultRoute = route('insurances.index');
        $subMenus = ['Insurances', 'Packages', 'Services', 'Ambulances', 'Ambulance Calls'];
    }
    // Sms/Mail
    if ($menu == User::MAIN_SMS_MAIL) {
        $defaultRoute = route('sms.index');
        $subMenus = ['SMS', 'Mail'];
    }

    //doctor role bed management routes
    if ($menu == User::MAIN_DOCTOR_BED_MGT) {
        $defaultRoute = route('bed-assigns.index');
        $subMenus = ['Bed Assigns'];
    }
    //    document doctore
    if ($menu == User::MAIN_DOCTOR_PATIENT_CASE) {
        $defaultRoute = route('patients.index');
        $subMenus = ['Patient Admissions'];
    }
    if ($menu == User::MAIN_CASE_MANGER_PATIENT_CASE) {
        $defaultRoute = route('patient-cases.index');
        $subMenus = ['Cases', 'Patient Admissions'];
    }
    if ($menu == User::MAIN_CASE_MANGER_SERVICE) {
        $defaultRoute = route('ambulances.index');
        $subMenus = ['Ambulances', 'Ambulance Calls'];
    }
    if ($menu == User::MAIN_ACCOUNT_MANAGER_MGT) {
        $defaultRoute = route('accounts.index');
        $subMenus = ['Accounts', 'Employee Payrolls', 'Invoices', 'Payments', 'Bills'];
    }

    if ($menu == User::MAIN_VACCINATION_MGT) {
        $defaultRoute = route('vaccinated-patients.index');
        $subMenus = ['Vaccinated Patients', 'Vaccinations'];
    }

    $allDisabled = \App\Models\Module::whereIn('name', $subMenus)
        ->where('is_active', true)
        ->get();

    if ($allDisabled->isEmpty()) {
        return false;
    }

    if ($allDisabled->count() != 2) {
        return route($allDisabled->last()->route);
    }

    if($defaultRoute = route('accounts.index')){
        return route($allDisabled->last()->route);
    }

    return $defaultRoute;
}

/**
 * @return mixed
 */
function getSettingForReCaptcha($userName)
{
    $user = User::where('userName', $userName)->first();
    if (! $user) {
        $user = DB::table('users')->where('userName', $userName)->first();
    }
    $isEnabledGoogleCapcha = Setting::where('key', 'enable_google_recaptcha')->where(
        'tenant_id',
        $user->tenant_id
    )->value('value');

    return $isEnabledGoogleCapcha;
}

function getRazorPaySupportedCurrencies(): array
{
    return [
        'USD', 'EUR', 'GBP', 'SGD', 'AED', 'AUD', 'CAD', 'CNY', 'SEK', 'NZD', 'MXN', 'HKD', 'NOK', 'RUB', 'ALL', 'AMD',
        'ARS', 'AWG', 'BBD', 'BDT', 'BMD', 'BND', 'BOB', 'BSD', 'BWP', 'BZD', 'CHF', 'COP', 'CRC', 'CUP', 'CZK', 'DKK',
        'DOP', 'DZD', 'EGP', 'ETB', 'FJD', 'GIP', 'GMD', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS',
        'INR', 'JMD', 'KES', 'KGS', 'KHR', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'MAD', 'MDL', 'MKD', 'MMK',
        'MNT', 'MOP', 'MUR', 'MVR', 'MWK', 'MYR', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'PEN', 'PGK', 'PHP', 'PKR', 'QAR',
        'SAR', 'SCR', 'SLL', 'SOS', 'SSP', 'SVC', 'SZL', 'THB', 'TTD', 'TZS', 'UYU', 'UZS', 'YER', 'ZAR', 'GHS',
    ];
}

/**
 * @return mixed
 */
function getCurrentVersion()
{
    $composerFile = file_get_contents('../composer.json');
    $composerData = json_decode($composerFile, true);
    $currentVersion = $composerData['version'];

    return $currentVersion;
}

function filterLangChange($filterArray): array
{
    foreach ($filterArray as $key => $value) {
        if ($value == 'All' || $value == 'Active' || $value == 'Deactive') {
            $array[$key] = __('messages.filter.'.strtolower($value));
        } elseif ($value == 'Unpaid' || $value == 'Paid') {
            $array[$key] = __('messages.employee_payroll.'.strtolower($value));
        } else {
            $array[$key] = __('messages.transaction_filter.'.strtolower($value));
        }
    }

    return $array;
}

function adminCurrencies(): array
{
    $currencies = SuperAdminCurrencySetting::all();

    $data = [];
    foreach ($currencies as $currency) {
        $convertCode = strtolower($currency['currency_code']);
        $data[$convertCode] = $currency['currency_icon'].' - '.$currency['currency_code'].' '.$currency['currency_name'];
    }

    return $data;
}

/**
 * @return mixed
 */
function getAdminCurrencySymbol($currencyCode)
{
    $currenciesData = SuperAdminCurrencySetting::pluck('currency_icon', DB::raw('LOWER(currency_code)'))->toArray();

    return $currenciesData[$currencyCode] ?? '';
}

function canCurrencyDelete($model, $columnName, $value): bool
{
    $result = $model::where($columnName, strtolower($value))->exists();
    if ($result) {
        return true;
    }

    return false;
}

function getAdminCurrencyFormat($currency, $amount): string
{
    $currencies = array_keys(Gerardojbaez\Money\Currency::getAllCurrencies());
    $is_valid_currency = in_array(strtoupper($currency), $currencies);

    if ($is_valid_currency) {
        $money = new Gerardojbaez\Money\Money($amount, strtoupper($currency));
        $curr = new Gerardojbaez\Money\Currency(strtoupper($currency));

        if ($curr->getSymbolPlacement() == 'after') {
            $value = $money->amount().getAdminCurrencySymbol($currency);
        } else {
            $value = getAdminCurrencySymbol($currency).$money->amount();
        }

        return $value;
    }

    return getAdminCurrencySymbol($currency).' '.number_format($amount, 2);
}

function getMoneyFormat($currency, $amount): string
{
    $currencies = array_keys(Gerardojbaez\Money\Currency::getAllCurrencies());
    $is_valid_currency = in_array(strtoupper($currency), $currencies);

    if ($is_valid_currency) {
        $money = new Gerardojbaez\Money\Money($amount, strtoupper($currency));

        $value =$money->amount();


        return $value;
    }

    return number_format($amount, 2);
}

function getCurrencyFormat($amount): string
{
    $currency = getCurrentCurrency();
    $currencies = array_keys(Gerardojbaez\Money\Currency::getAllCurrencies());
    $is_valid_currency = in_array(strtoupper($currency), $currencies);

    if ($is_valid_currency) {
        $money = new Gerardojbaez\Money\Money($amount, strtoupper($currency));
        $curr = new Gerardojbaez\Money\Currency(strtoupper($currency));

        if ($curr->getSymbolPlacement() == 'after') {
            $value = $money->amount().getCurrencySymbol();
        } else {
            $value = getCurrencySymbol().$money->amount();
        }

        return $value;
    }

    return getCurrencySymbol().' '.number_format((float)$amount, 2);
}

function getCurrencyFormatForPDF($amount): string
{
    $currency = getAPICurrentCurrency();
    $currencies = array_keys(Gerardojbaez\Money\Currency::getAllCurrencies());
    $is_valid_currency = in_array(strtoupper($currency), $currencies);

    if ($is_valid_currency) {
        $money = new Gerardojbaez\Money\Money($amount, strtoupper($currency));
        $curr = new Gerardojbaez\Money\Currency(strtoupper($currency));

        if ($curr->getSymbolPlacement() == 'after') {
            $value = $money->amount().getCurrencySymbol();
        } else {
            $value = getAPICurrencySymbol().$money->amount();
        }

        return $value;
    }

    return getAPICurrencySymbol().' '.number_format($amount, 2);
}

/**
 * @return bool
 */
function canAccessRecord($model, $id)
{
    $recordExists = $model::where('id', $id)->exists();

    if ($recordExists) {
        return true;
    }

    return false;
}

/**
 * @return bool|Model|\Illuminate\Database\Query\Builder|object
 */
function loginUserSubscription()
{
    if (getLoggedInUser()->hasRole('Super Admin')) {
        return true;
    }

    $authUser = getLoggedInUser();
    if (! $authUser->hasRole('Admin')) {
        $authUser = User::withoutGlobalScope(new \Stancl\Tenancy\Database\TenantScope())
            ->where('tenant_id', $authUser->owner->tenant_id)->first();
    }

    $subscription = DB::table('subscriptions')
        ->where('status', Subscription::ACTIVE)
        ->where('user_id', $authUser->id)
        ->select('subscription_plan_id')
        ->first();

    if (! $subscription) {
        return false;
    }

    return $subscription;
}

/**
 * @return array|false
 */
function subscriptionPlanFeaturesId()
{
    if (getLoggedInUser()->hasRole('Super Admin')) {
        return true;
    }

    $subscription = loginUserSubscription();

    if (! $subscription) {
        return false;
    }

    $subscriptionPlanFeaturesId = DB::table('feature_subscriptionplan')
        ->where('subscription_plan_id', $subscription->subscription_plan_id)
        ->pluck('feature_id')->toArray();

    return $subscriptionPlanFeaturesId;
}

/**
 * @return bool
 */
function isFeatureAllowToUse($routeName, $subscriptionPlanFeaturesId)
{
    if (getLoggedInUser()->hasRole('Super Admin')) {
        return true;
    }

    if (! $subscriptionPlanFeaturesId) {
        return false;
    }

    $feature = DB::table('features')->whereRaw('route LIKE ?', ['%'.$routeName.'%'])->first();

    $featureId = $feature->id;
    if ($feature->has_parent != 0) {
        $featureId = $feature->has_parent;
    }

    $menuAccess = false;

    if ($feature != null && in_array($featureId, $subscriptionPlanFeaturesId)) {
        $menuAccess = true;
    }

    return $menuAccess;
}

function getSelectedPaymentGateway($keyName)
{
    $key = $keyName;
    static $settingValues;

    if (isset($settingValues[$key])) {
        return $settingValues[$key];
    }
    /** @var Setting $setting */
    $setting = Setting::where('key', '=', $keyName)->first();

    if (isset($setting->value) && $setting->value !== '' ) {
        $settingValues[$key] = $setting->value;
    } else {
        $envKey = strtoupper($key);
        $settingValues[$key] = env($envKey);
    }

    return $settingValues[$key];
}

if (! function_exists('getSettingValueByKey')) {
    /**
     * @return mixed
     */
    function getSettingValueByKey($keyName)
    {
        /** @var Setting $setting */
        $setting = Setting::where('key', '=', $keyName)->first();
        if ($setting) {
            return $setting->value;
        }

        return false;
    }
}

function getSuperAdminAppLogoUrl()
{
    static $appLogo;

    if (empty($appLogo)) {
        $appLogo = SuperAdminSetting::where('key', '=', 'app_logo')->first();
    }

    return $appLogo->logo_url;
}

/**
 * @return mixed
 */
function getSuperAdminAppName()
{
    static $appName;

    if (empty($appName)) {
        $appName = SuperAdminSetting::where('key', '=', 'app_name')->first()->value;
    }

    return $appName;
}

function generateUniquePurchaseNumber()
{
    do {
        $code = random_int(100000, 999999);
    } while (\App\Models\PurchaseMedicine::where('purchase_no', '=', $code)->first());

    return $code;
}
function generateUniqueBillNumber()
{
    do {
        $code = random_int(1000, 9999);
    } while (\App\Models\MedicineBill::where('bill_number', '=', $code)->first());

    return $code;
}

function isZoomTokenExpire()
{
    $isExpired = false;
    $zoomOAuth = ZoomOAuth::where('user_id', Auth::id())->first();
    $currentTime =  Carbon::now();

    if(!isset($zoomOAuth) || $zoomOAuth->updated_at < $currentTime->subMinutes(57)){
        $isExpired = true;
    }

    return  $isExpired;
}

function getWeekDate(): string
{
    $date = Carbon::now();
    $startOfWeek = $date->startOfWeek()->subDays(1);
    $startDate = $startOfWeek->format('Y-m-d');
    $endOfWeek = $startOfWeek->addDays(6);
    $endDate = $endOfWeek->format('Y-m-d');

    return $startDate.' - '.$endDate;
}
function getDoctorSchedule()
{
   return Schedule::with('doctor')->where('doctor_id', getLoggedInUser()->owner_id)->first('id');
};

function getPaymentCredentials($key)
{
    $credentialValue ='';

    $query = Setting::whereTenantId(getLoggedInUser()->tenant_id)->pluck('value','key')->toArray();
    if(!empty($query)){
        if(isset($query[$key])){
            $credentialValue = $query[$key];
        }
    }

    return $credentialValue;
}
function regionCode($regionCode)
{
    $code = str_replace('+','',$regionCode);
    return '+'.$code;
}
if(!function_exists('getIpdPaymentTypes')){
    function getIpdPaymentTypes()
    {
        $ipdPaymentTypes = [];
        $stripe = getPaymentCredentials('stripe_enable');
        $payPal = getPaymentCredentials('paypal_enable');
        $razorpay = getPaymentCredentials('razorpay_enable');
        // $paytm = getPaymentCredentials('paytm_enable');
        $payStack = getPaymentCredentials('paystack_enable');
        $phonePay = getPaymentCredentials('phone_pe_enable');
        $flutterWave = getPaymentCredentials('flutterwave_enable');

        $ipdPaymentTypes[1] = 'Cash';
        $ipdPaymentTypes[2] = 'Cheque';

        if(!empty($stripe) && $stripe){
            $ipdPaymentTypes[3] = 'Stripe';
        }
        if(!empty($payPal) && $payPal){
            $ipdPaymentTypes[4] = 'Paypal';
        }
        if(!empty($razorpay) && $razorpay){
            $ipdPaymentTypes[5] = 'Razorpay';
        }
        // if(!empty($paytm) && $paytm){
        //     $ipdPaymentTypes[6] = 'Paytm';
        // }
        if(!empty($payStack) && $payStack){
            $ipdPaymentTypes[7] = 'PayStack';
        }
        if(!empty($phonePay) && $phonePay){
            $ipdPaymentTypes[8] = 'PhonePe';
        }
        if(!empty($flutterWave) && $flutterWave){
            $ipdPaymentTypes[9] = 'FlutterWave';
        }

        return $ipdPaymentTypes;
    }
}


if(!function_exists('getBillPaymentType')){

    function getBillPaymentType()
    {
        $billPaymentTypes = [];

        $stripe = getPaymentCredentials('stripe_enable');
        $phonepe = getPaymentCredentials('phone_pe_enable');
        $flutterWave = getPaymentCredentials('flutterwave_enable');

        if(!empty($stripe) && $stripe){
            $billPaymentTypes[0] = 'Stripe';
        }

        if(!empty($phonepe) && $phonepe){
            $billPaymentTypes[1] = 'PhonePe';
        }
        if(!empty($flutterWave) && $flutterWave){
            $billPaymentTypes[3] = 'FlutterWave';
        }
        $billPaymentTypes[2] = 'Cash';

        return $billPaymentTypes;

    }
}

if(!function_exists('getPurchaseMedicinePaymentTypes')){
    function getPurchaseMedicinePaymentTypes()
    {
        $paymentTypeArray = [];
        $stripeCheck = getPaymentCredentials('stripe_enable');
        $razorpayCheck = getPaymentCredentials('razorpay_enable');
        $paystackCheck = getPaymentCredentials('paystack_enable');
        $phonePe = getPaymentCredentials('phone_pe_enable');
        $flutterWave = getPaymentCredentials('flutterwave_enable');

        $paymentTypeArray[0] = 'Cash';

        $paymentTypeArray[1] = 'Cheque';

        if(!empty($stripeCheck)){
            $paymentTypeArray[5] = 'Stripe';
        }
        if(!empty($razorpayCheck)){
            $paymentTypeArray[2] = 'Razorpay';
        }
        if(!empty($paystackCheck)){
            $paymentTypeArray[3] = 'Paystack';
        }
        if(!empty($phonePe)){
            $paymentTypeArray[4] = 'PhonePe';
        }
        if(!empty($flutterWave)){
            $paymentTypeArray[6] = 'FlutterWave';
        }

        return $paymentTypeArray;
    }
}

function getSuperAdminPaymentCredentials($key)
{
    $credentialValue ='';

    $query = SuperAdminSetting::pluck('value','key')->toArray();
    if(!empty($query)){
        $credentialValue = $query[$key];
    }

    return $credentialValue;
}

if(!function_exists('getSuperAdminPaymentTypes')){
    function getSuperAdminPaymentTypes()
    {
        $paymentTypeArray = [];
        $stripeCheck = getSuperAdminPaymentCredentials('stripe_enable');
        $razorpayCheck = getSuperAdminPaymentCredentials('razorpay_enable');
        $paystackCheck = getSuperAdminPaymentCredentials('paystack_enable');
        $phonePe = getSuperAdminPaymentCredentials('phonepe_enable');
        $paypal = getSuperAdminPaymentCredentials('paypal_enable');
        $flutterWave = getSuperAdminPaymentCredentials('flutterwave_enable');

        $paymentTypeArray[4] = 'Manual';

        if(!empty($stripeCheck)){
            $paymentTypeArray[1] = 'Stripe';
        }
        if(!empty($paypal)){
            $paymentTypeArray[2] = 'Paypal';
        }
        if(!empty($razorpayCheck)){
            $paymentTypeArray[3] = 'Razorpay';
        }
        if(!empty($paystackCheck)){
            $paymentTypeArray[6] = 'Paystack';
        }
        if(!empty($phonePe)){
            $paymentTypeArray[7] = 'PhonePe';
        }
        if(!empty($flutterWave)){
            $paymentTypeArray[8] = 'FlutterWave';
        }

        return $paymentTypeArray;
    }
}

if(!function_exists('getAppointmentPaymentTypes')){
    function getAppointmentPaymentTypes()
    {
        $paymentTypeArray = [];
        $stripeCheck = getPaymentCredentials('stripe_enable');
        $razorpayCheck = getPaymentCredentials('razorpay_enable');
        $phonePe = getPaymentCredentials('phone_pe_enable');
        $paystackCheck = getPaymentCredentials('paystack_enable');
        $paypal = getPaymentCredentials('paypal_enable');
        $flutterWave = getPaymentCredentials('flutterwave_enable');
        $paymentTypeArray[4] = 'Cash';
        $paymentTypeArray[6] = 'Cheque';

        if(!empty($stripeCheck)){
            $paymentTypeArray[1] = 'Stripe';
        }
        if(!empty($razorpayCheck)){
            $paymentTypeArray[2] = 'Razorpay';
        }
        if(!empty($paypal)){
            $paymentTypeArray[3] = 'Paypal';
        }
        if(!empty($flutterWave)){
            $paymentTypeArray[5] = 'FlutterWave';
        }
        if(!empty($phonePe)){
            $paymentTypeArray[7] = 'PhonePe';
        }
        if(!empty($paystackCheck)){
            $paymentTypeArray[8] = 'Paystack';
        }

        return $paymentTypeArray;
    }
}

function flutterWaveSupportedCurrencies()
{
    return ['GBP','CAD','XAF','CLP','COP','EGP','EUR','GHS','GNF','KES','MWK','MAD','NGN','RWF','SLL','STD','ZAR','TZS','UGX','USD','XOF','ZMW'];
}

function payStackSupportedCurrencies()
{
    return ['ZAR','USD','GHS','NGN','KES'];
}

if(!function_exists('getGoogleJsonFilePath')){
    function getGoogleJsonFilePath()
    {
        $googleJsonFilePath = Doctor::whereUserId(Auth::id())->value('google_json_file_path');

        if(!empty($googleJsonFilePath)){
            return $googleJsonFilePath;
        }

        return null;
    }
}

