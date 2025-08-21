<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStagiaireRequest extends FormRequest
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
        $stagiaireId = $this->route('stagiaire');
        $userId = \App\Models\Stagiaire::find($stagiaireId)?->user_id;
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                $userId
                    ? Rule::unique('users', 'email')->ignore($userId)
                    : Rule::unique('users', 'email'),
            ],
            'password' => 'nullable|string|min:6',
            'civilite' => 'required|string',
            'prenom' => 'required|string',
            'telephone' => 'required|string',
            'adresse' => 'nullable|string',
            'date_naissance' => 'required|date',
            'ville' => 'required|string',
            'code_postal' => 'required|string',
            'role' => 'nullable|string',
            'statut' => 'nullable|string',
            'formateur_id' => 'nullable|exists:formateurs,id',
            'commercial_id' => 'nullable|exists:commercials,id',
            'pole_relation_client_id' => 'nullable|array',
            'pole_relation_client_id.*' => 'exists:pole_relation_clients,id',
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'date_debut_formation' => 'nullable|date',
            'date_inscription' => 'nullable|date',
            'formations' => ['required', 'array', function ($attribute, $value, $fail) {
                $hasSelected = false;
                foreach ($value as $formation) {
                    if (isset($formation['selected']) && $formation['selected']) {
                        $hasSelected = true;
                        break;
                    }
                }
                if (!$hasSelected) {
                    $fail('La formation est obligatoire.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'prenom.string' => 'Le prenom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',

            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',

            'civilite.required' => 'La civilité est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'date_naissance.required' => 'La date de naissance est obligatoire.',
            'date_naissance.date' => 'La date de naissance doit être une date valide.',

            'ville.required' => 'La ville est obligatoire.',
            'code_postal.required' => 'Le code postal est obligatoire.',

            'formateur_id.exists' => 'Le formateur sélectionné est invalide.',
            'commercial_id.exists' => 'Le commercial sélectionné est invalide.',
            'formations.required' => 'La formation est obligatoire.',
            'partenaire_id.exists' => 'Le partenaire sélectionné est invalide.',
        ];
    }
}
