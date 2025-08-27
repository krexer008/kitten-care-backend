<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVeterinaryVisitRequest extends FormRequest
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
            'cat_id' => 'required|exists:cats,id',
            'clinic_name' => 'required|string|max:255',
            'reason' => 'required|string|max:500',
            'date_time' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'next_visit_time' => 'nullable|date|after:date_time',
        ];
    }

    public function messages(): array
    {
        return [
            'cat_id.required' => 'Необходимо указать кота',
            'clinic_name.required' => 'Название клиники обязательно',
            'reason.required' => 'Причина визита обязательна',
            'date_time.required' => 'Дата и время визита обязательны',
        ];
    }
}
