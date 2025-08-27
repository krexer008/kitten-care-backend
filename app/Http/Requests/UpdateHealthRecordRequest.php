<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHealthRecordRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'weight'=>'sometimes|numeric|min:0.1|max:20',
            'temperature'=>'nullable|numeric|min:35|max:42',
            'notes'=>'nullable|string|max:1000',
            'record_date'=>'sometimes|date',
        ];
    }
}
