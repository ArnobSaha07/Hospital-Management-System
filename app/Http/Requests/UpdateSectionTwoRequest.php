<?php

namespace App\Http\Requests;

use App\Models\SectionTwo;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSectionTwoRequest extends FormRequest
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
        return SectionTwo::$rules;
    }

    public function messages(): array
    {
        return [
            'card_one_text_secondary.max' => __('messages.new_change.card_one_char'),
        ];
    }
}
