<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVeterinaryVisitRequest extends FormRequest
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
            'clinic_name'=>'sometimes|string|max:255',
            'reason'=>'sometimes|string|max:500',
            'date_time'=>'sometimes|date',
            'notes'=>'nullable|string|max:1000',
            'next_visit_time'=>'nullable|date|after:date_time',
        ];
    }
}
