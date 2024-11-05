<?php

namespace App\Http\Requests;

use App\Rules\ValidRecaptcha;
use Illuminate\Foundation\Http\FormRequest;

class CreateWebAppointmentRequest extends FormRequest
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
        $validationFields = [
            'doctor_id' => 'required',
            'department_id' => 'required',
            'opd_date' => 'required',
            'problem' => 'nullable',
            'email' => 'required|email:filter',
        ];
        if (request()->get('patient_type') == 1) {
            $validationFields['password'] = 'required|same:password_confirmation|min:6';
        }
        if (request()->get('patient_type') == 2) {
            $validationFields['patient_id'] = 'required';
        }
        if (getSettingForReCaptcha(request()->get('hospital_username')) == true) {
            $validationFields['g-recaptcha-response'] = ['required', new ValidRecaptcha];
        }

        return $validationFields;
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
