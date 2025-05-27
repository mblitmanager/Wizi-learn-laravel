<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogueFormationRequest extends FormRequest
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
        return [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duree' => 'required|integer|min:1',
            'formation_id' => 'required|exists:formations,id',
            'certification' => 'nullable|string|max:255',
            'prerequis' => 'nullable|string|max:255',
            'image_url' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov,pdf,mp3,webp|max:102400',
            'cursus_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'tarif' => 'required|numeric|min:0',
            'statut' => 'required|in:1,0',

        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',

            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',

            'duree.required' => 'La durée est obligatoire.',
            'duree.integer' => 'La durée doit être un nombre entier.',
            'duree.min' => 'La durée doit être au moins de 1.',

            'formation_id.required' => 'La formation est obligatoire.',
            'formation_id.exists' => 'La formation sélectionnée est invalide.',

            'certification.max' => 'La certification ne doit pas dépasser 255 caractères.',

            'prerequis.max' => 'Le champ prérequis ne doit pas dépasser 255 caractères.',

            'image_url.file' => 'Le fichier doit être une image.',
            'image_url.mimes' => 'Le fichier doit être de type : jpeg, png, jpg, gif, webp.',
            'image_url.max' => 'L\'image ne doit pas dépasser 100 Mo.',

            'cursus_pdf.file' => 'Le fichier doit être un PDF.',
            'cursus_pdf.mimes' => 'Le fichier doit être de type : pdf.',
            'cursus_pdf.max' => 'Le fichier ne doit pas dépasser 100 Mo.',

            'tarif.required' => 'Le tarif est obligatoire.',
            'tarif.numeric' => 'Le tarif doit être un nombre.',
            'tarif.min' => 'Le tarif doit être au minimum de 0.',

            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut doit être 1 (actif) ou 0 (inactif).',
        ];
    }
}
