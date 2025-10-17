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
            'prenom' => 'required|string|max:255',
            'civilite' => 'required|string|in:M,Mme,Mlle',
            'email' => [
                'required',
                'email',
                'max:255',
                $userId
                    ? Rule::unique('users', 'email')->ignore($userId)
                    : Rule::unique('users', 'email'),
            ],
            'password' => $this->isMethod('POST') ? 'required|string|min:8' : 'nullable|string|min:8',
            'telephone' => 'nullable|string|max:20',
            'catalogue_formation_id' => 'required|exists:catalogue_formations,id',
            'stagiaire_id' => 'nullable|exists:stagiaires,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:16096',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'civilite.required' => 'La civilité est obligatoire.',
            'civilite.in' => 'La civilité doit être M, Mme ou Mlle.',

            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 255 caractères.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'email.max' => 'L\'adresse e-mail ne doit pas dépasser 255 caractères.',

            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',

            'telephone.string' => 'Le téléphone doit être une chaîne de caractères.',
            'telephone.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',

            'catalogue_formation_id.required' => 'La formation est obligatoire.',
            'catalogue_formation_id.exists' => 'La formation sélectionnée n\'existe pas.',

            'stagiaire_id.exists' => 'Le stagiaire sélectionné n\'existe pas.',

            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif, svg, ico ou webp.',
            'image.max' => 'La taille de l\'image ne doit pas dépasser 16 Mo.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // S'assurer que la civilité est en majuscules si nécessaire
        if ($this->has('civilite')) {
            $this->merge([
                'civilite' => ucfirst($this->civilite),
            ]);
        }
    }
}
