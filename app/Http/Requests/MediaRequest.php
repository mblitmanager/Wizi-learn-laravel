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
            'description' => 'nullable|string|max:1000',
            'url' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,avi,mov,pdf|max:51200',
            'type' => 'required|string|in:video,document,image',
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
            'description.max' => 'La description ne doit pas dépasser 1000 caractères.',

            'url.mimes' => 'Le fichier doit être au format jpg, jpeg, png, mp4 ou pdf.',
            'url.max' => 'Le fichier ne doit pas dépasser 10 Mo.',

            'type.required' => 'Le type est obligatoire.',
            'type.string' => 'Le type doit être une chaîne de caractères.',
            'type.in' => 'Le type doit être soit "video", "document" ou "image".',

            'formation_id.required' => 'L\'ID de la formation est obligatoire.',
            'formation_id.exists' => 'La formation sélectionnée n\'existe pas.',
        ];
    }
}
