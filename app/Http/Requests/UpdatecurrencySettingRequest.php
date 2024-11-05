<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatecurrencySettingRequest extends FormRequest
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
        $rules['currency_name'] = 'required|max:25|is_unique:currency_settings,currency_name,'.$this->route('currency_setting')->id;
        $rules['currency_icon'] = 'required';
        $rules['currency_code'] = 'required|min:3|max:3|is_unique:currency_settings,currency_code,'.$this->route('currency_setting')->id;

        return $rules;
    }
}
