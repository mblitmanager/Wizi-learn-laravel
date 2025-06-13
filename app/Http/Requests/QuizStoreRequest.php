<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizStoreRequest extends FormRequest
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
            'niveau' => 'required|in:débutant,intermédiaire,avancé',
            'duree' => 'required|integer|min:1',
            'nb_points_total' => 'required|integer|min:1',
            'formation_id' => 'required|exists:formations,id',
        ];
    }

    public function messages()
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',
            'niveau.required' => 'Le niveau est obligatoire.',
            'duree.required' => 'La duree est obligatoire.',
            'nb_points_total.required' => 'Le nombre de points total est obligatoire.',
            'formation_id.required' => 'La formation est obligatoire.',
            'formation_id.exists' => 'La formation choisie est invalide.',
        ];
    }
}
