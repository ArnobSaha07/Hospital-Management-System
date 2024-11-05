<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHospitalRequest extends FormRequest
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
        $userTenant = User::findOrFail($this->route('hospital'));
        $rules = User::$hospitalRules;
        $rules['hospital_name'] = 'required|string|max:255|unique:tenants,hospital_name,'.$userTenant->tenant_id;
        $rules['username'] = 'required|string|max:12|unique:users,username,'.$this->route('hospital');
        $rules['email'] = 'required|email:filter|unique:users,email,'.$this->route('hospital');

        return $rules;
    }
}
