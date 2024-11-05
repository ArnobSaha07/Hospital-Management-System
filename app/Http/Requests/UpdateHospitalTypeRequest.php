<?php

namespace App\Http\Requests;

use App\Models\HospitalType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHospitalTypeRequest extends FormRequest
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
        $rules = HospitalType::$rules;
        $rules['name'] = $rules['name'].','.$this->id;

        return $rules;
    }
}
