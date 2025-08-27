<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHealthRecordRequest extends FormRequest
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
            'weight' => 'required|numeric|min:0.1|max:20',
            'temperature' => 'nullable|numeric|min:35|max:42',
            'notes' => 'nullable|string|max:1000',
            'record_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'cat_id.required'=>'Необходимо указать кота',
            'cat_id.exists'=>'Указанный кот не существует',
            'weight.required'=>'Вес обязателен для заполнения',
            'weight.numeric'=>'Вес должен быть числом',
            'temperature.numeric'=>'Температура должна быть числом',
            'record_date.required'=>'Дата записи обязательна',
        ];
    }
}
