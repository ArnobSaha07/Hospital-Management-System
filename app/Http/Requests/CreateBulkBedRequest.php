<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBulkBedRequest extends FormRequest
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
        return [
            'name.*' => 'required|distinct|is_unique:beds,name',
            'bed_type.*' => 'required',
            //            'charge.*'   => 'required|numeric|min:0',
            'charge.*' => 'required|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.*.distinct' => __('messages.new_change.bed_distinct'),
            'name.*.is_unique' => __('messages.new_change.bed_unique'),
            'charge.*.numeric' => __('messages.new_change.charge_number'),
            'charge.*.regex' => __('messages.new_change.charge_regex')
        ];
    }
}
