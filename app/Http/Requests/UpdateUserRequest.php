<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUserRequest
 */
class UpdateUserRequest extends FormRequest
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
        $rules['email'] = 'required|email:filter|unique:users,email,'.$this->route('user')->id;
        $rules['department_id'] = 'nullable';
        $rules['image'] = 'mimes:jpeg,png,jpg,gif,webp';

        return $rules;
    }
}
