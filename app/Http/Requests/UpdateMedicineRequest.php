<?php

namespace App\Http\Requests;

use App\Models\Medicine;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicineRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->sanitize();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = Medicine::$rules;
        $rules['name'] = 'required|is_unique:medicines,name,'.$this->route('medicine')->id;

        return $rules;
    }

    public function messages(): array
    {
        return [
            'category_id.required' =>  __('messages.new_change.category_required'),
            'brand_id.required' => __('messages.new_change.brand_required'),
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        $input['selling_price'] = ! empty($input['selling_price']) ? str_replace(',', '',
            $input['selling_price']) : null;
        $input['buying_price'] = ! empty($input['buying_price']) ? str_replace(',', '', $input['buying_price']) : null;
        $this->replace($input);
    }
}
