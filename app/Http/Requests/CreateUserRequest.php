<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateUserRequest
 */
class CreateUserRequest extends FormRequest
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
        if (request()->get('department_id') == 2) {
            $rules['doctor_department_id'] = 'required';
        }
        $rules['image'] = 'mimes:jpeg,png,jpg,gif,webp';

        return $rules;
    }
}
