<?php

namespace App\Http\Requests;

use App\Models\Nurse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNurseRequest extends FormRequest
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
        $rules = Nurse::$rules;
        $rules['email'] = 'required|email:filter|unique:users,email,'.$this->route('nurse')->user->id;

        return $rules;
    }
}
