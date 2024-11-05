<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
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
        $rules = User::$rules;
        $rules['department_id'] = 'nullable';
        $rules['gender'] = 'nullable';
        $rules['image'] = 'mimes:jpeg,png,jpg,gif,webp';

        return $rules;
    }
}
