<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', 'string', 'max:100'],
            'target_date' => ['nullable', 'date', 'after_or_equal:today'],
            'status' => ['nullable', 'in:active,completed,archived'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de l\'objectif est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'target_date.after_or_equal' => 'La date cible ne peut pas être dans le passé.',
        ];
    }
}
