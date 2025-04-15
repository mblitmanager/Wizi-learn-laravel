<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormateurStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $formateurId = $this->route('formateur');

        $userId = \App\Models\Formateur::find($formateurId)?->user_id;
        return [
            'name' => 'required|string|max:255',
            'prenom' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8',
            'role' => [
                Rule::in(['formateur']),
            ],
            'formation_id' => 'required|exists:formations,id',
            'stagiaire_id' => 'required|exists:stagiaires,id',
        ];
    }
}
