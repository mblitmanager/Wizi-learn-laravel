<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PoleRelationClientRequest extends FormRequest
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
        $prcid = $this->route('pole_relation_clients');
        $userId = \App\Models\PoleRelationClient::find($prcid)?->user_id;
        return [
            'name' => 'required|string|max:255',
            'prenom' => 'required|string',
            'email' => [
                'required',
                'email',
                $userId
                    ? Rule::unique('users', 'email')->ignore($userId)
                    : Rule::unique('users', 'email'),
            ],
            'password' => 'nullable|string|min:8',
            'role' => [
                Rule::in(['pole relation client']),
            ],
            'stagiaire_id' => 'nullable|exists:stagiaires,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'prenom.string' => 'Le prenom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
        ];
    }
}
