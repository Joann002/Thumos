<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalEntryRequest extends FormRequest
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
            'date' => ['required', 'date'],
            'content' => ['required', 'string', 'max:5000'],
            'mood' => ['nullable', 'in:excellent,good,neutral,bad,terrible'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'date.required' => 'La date est obligatoire.',
            'content.required' => 'Le contenu de l\'entrée est obligatoire.',
            'content.max' => 'Le contenu ne peut pas dépasser 5000 caractères.',
            'mood.in' => 'L\'humeur sélectionnée n\'est pas valide.',
        ];
    }
}
