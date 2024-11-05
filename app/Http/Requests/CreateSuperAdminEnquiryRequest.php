<?php

namespace App\Http\Requests;

use App\Models\SuperAdminEnquiry;
use App\Rules\ValidRecaptcha;
use Illuminate\Foundation\Http\FormRequest;

class CreateSuperAdminEnquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = SuperAdminEnquiry::$rules;
        if (getSuperAdminSettingKeyValue('enable_google_recaptcha')) {
            $rules['g-recaptcha-response'] = ['required', new ValidRecaptcha];
        }

        return $rules;
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'g-recaptcha-response.recaptcha' =>   __('messages.new_change.captcha_failed'),
            'g-recaptcha-response.required' =>  __('messages.new_change.google_captcha_required'),
        ];
    }
}
