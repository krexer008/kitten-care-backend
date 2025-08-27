<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Здесь позже добавим проверку прав доступа
        // Пока разрешаем все
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date|before:today',
            'gender' => 'required|in:male, female',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Имя питомца обязательно для заполнения',
            'gender.required' => 'Пол питомца обязателен для указания',
            'gender.in' => 'Пол должен быть "male" или "female"',
            'birthday.before' => 'Дата рождения не может быть в будущем',
        ];
    }
}
