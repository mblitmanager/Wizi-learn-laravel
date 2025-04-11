<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormationRequest extends FormRequest
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
            'categorie' => 'required|string|max:255',
            'image' => 'nullable|string',
            'statut' => 'required|string',
            'duree' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'duree.required' => 'La durée est obligatoire.',
        ];
    }
}
