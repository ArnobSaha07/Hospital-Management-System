<?php

namespace App\Http\Requests;

use App\Models\LabTechnician;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLabTechnicianRequest extends FormRequest
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
        $rules = LabTechnician::$rules;
        $rules['email'] = 'required|email:filter|unique:users,email,'.$this->route('lab_technician')->user->id;

        return $rules;
    }
}
