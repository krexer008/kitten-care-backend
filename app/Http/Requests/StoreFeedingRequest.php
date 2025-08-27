<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFeedingRequest extends FormRequest
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
            'cat_id' => 'required|exists:cats, id',
            'food_type' => 'required|string|max:255',
            'weight_grams' => 'required|integer|min:1|max:1000',
            'date_time' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'cat_id.required' => 'Необходимо указать кота',
            'cat_id.exists' => 'Указанный кот не существует',
            'food_type.required' => 'Тип корма обязателен',
            'weight_grams.required' => 'Вес корма обязателен',
            'weight_grams.min' => 'Вес корма должен быть не менее 1 грамма',
            'weight_grams.max' => 'Вес корма не может превышать 1000 грамм',
            'date_time.required' => 'Дата и время кормления обязательны',
        ];
    }
}
