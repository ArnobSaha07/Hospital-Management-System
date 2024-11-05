<?php

namespace App\Http\Requests;

use App\Models\Bill;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBillRequest extends FormRequest
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
        return Bill::$rules;
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => __('messages.new_change.admission_required'),
            'patient_id.min' => __('messages.new_change.one_patient'),
        ];
    }
}
