<?php

namespace App\Http\Requests;

use App\Models\OpdPatientDepartment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOpdPatientDepartmentRequest extends FormRequest
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
        return OpdPatientDepartment::$rules;
    }

    public function messages(): array
    {
        return [
            'case_id.required' => __('messages.new_change.case_required'),
            'bed_id.required' => __('messages.new_change.bed_required'),
        ];
    }
}
