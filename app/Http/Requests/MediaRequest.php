<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaRequest extends FormRequest
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
            'description' => 'nullable|string',
            'url' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov,pdf,mp3|max:102400',
            'type' => 'required|string|in:video,document,image,audio',
            'categorie' => 'required|string|in:tutoriel,astuce',
            'duree' => 'nullable|integer|min:1',
            'ordre' => 'nullable|integer|min:0',
            'formation_id' => 'required|exists:formations,id',
        ];
    }

    public function messages()
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',

            'description.string' => 'La description doit être une chaîne de caractères.',

            'url.mimes' => 'Le fichier doit être au format jpg, jpeg, png, mp4 ou pdf.',
            'url.max' => 'Le fichier ne doit pas dépasser 10 Mo.',

            'type.required' => 'Le type est obligatoire.',
            'type.string' => 'Le type doit être une chaîne de caractères.',
            'type.in' => 'Le type doit être soit "video", "document" ou "image".',


            'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
            'categorie.in' => 'La catégorie doit être soit "tutoriel" ou "astuce".',

            'duree.integer' => 'La durée doit être un nombre entier.',
            'duree.min' => 'La durée doit être supérieure à 0.',

            'ordre.integer' => 'L\'ordre doit être un nombre entier.',
            'ordre.min' => 'L\'ordre doit être supérieur ou égal à 0.',

            'formation_id.required' => 'L\'ID de la formation est obligatoire.',
            'formation_id.exists' => 'La formation sélectionnée n\'existe pas.',
        ];
    }
}
