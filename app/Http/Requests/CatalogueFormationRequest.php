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
            'image_url' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'tarif' => 'required|numeric|min:0',
            'statut' => 'required|in:1,0',

        ];
    }
}
