<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceSliderRequest extends FormRequest
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
            'img_url' => 'image|mimes:jpeg,png,jpg,gif,webp',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'img_url.required' => __('messages.service_slider.service_slider_image'),
        ];
    }
}
