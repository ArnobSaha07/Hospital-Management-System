<?php

namespace App\Http\Requests;

use App\Models\CaseHandler;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCaseHandlerRequest extends FormRequest
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
        $rules = CaseHandler::$rules;
        $rules['email'] = 'required|email|is_unique:users,email,'.$this->route('caseHandler')->user->id;

        return $rules;
    }
}
