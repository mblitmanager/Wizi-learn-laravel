<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommmercialStoreRequest extends FormRequest
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
        $commecrailId = $this->route('commercial');
        $userId = \App\Models\Commercial::find($commecrailId)?->user_id;

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
                Rule::in(['commercial']),
            ],
            'telephone' => 'nullable|string',
            'stagiaire_id' => 'nullable|exists:stagiaires,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:16096',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'prenom.string' => 'Le prenom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif ou webp.',
            'image.max' => 'La taille de l\'image ne doit pas dépasser 2 Mo.',
        ];
    }
}
