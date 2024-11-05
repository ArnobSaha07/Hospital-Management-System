<?php

namespace App\Http\Requests;

use App\Models\Accountant;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountantRequest extends FormRequest
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
        $rules = Accountant::$rules;
        $rules['email'] = 'required|email:filter|unique:users,email,'.$this->route('accountant')->user->id;

        return $rules;
    }
}
