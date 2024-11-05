<?php

namespace App\Http\Requests;

use App\Models\Document;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
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
        $rules = Document::$rules;
        //        unset($rules['file']);
        $rules['file'] = 'nullable|mimes:jpeg,png,pdf,docx,doc,webp';

        return $rules;
    }
}
