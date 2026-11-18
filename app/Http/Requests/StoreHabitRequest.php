<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHabitRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'frequency' => ['required', 'in:daily,weekly'],
            'days_of_week' => ['required_if:frequency,weekly', 'nullable', 'array', 'min:1', 'max:7'],
            'days_of_week.*' => ['required', 'integer', 'between:1,7', 'distinct'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'habitude est obligatoire.',
            'frequency.required' => 'La fréquence est obligatoire.',
            'frequency.in' => 'La fréquence doit être quotidienne ou hebdomadaire.',
            'color.regex' => 'La couleur doit être un code hexadécimal valide.',
            'days_of_week.required_if' => 'Choisis au moins un jour pour une habitude hebdomadaire.',
            'days_of_week.*.distinct' => 'Chaque jour ne peut être sélectionné qu’une fois.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('frequency') === 'daily') {
            $this->merge(['days_of_week' => null]);
        } elseif (is_array($this->input('days_of_week'))) {
            $this->merge(['days_of_week' => array_map(
                fn ($day) => is_string($day) && preg_match('/^[1-7]$/D', $day) ? (int) $day : $day,
                $this->input('days_of_week'),
            )]);
        }
    }
}
