<?php

namespace App\Http\Requests;

use App\Models\JournalEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJournalEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'content' => ['required', 'string', 'max:5000'],
            'mood' => ['nullable', Rule::in(JournalEntry::MOODS)],
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
