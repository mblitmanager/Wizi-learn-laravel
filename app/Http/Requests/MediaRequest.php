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
            'url' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $sourceType = request()->input('source_type');
                    if ($sourceType === 'file') {
                        if (!request()->hasFile('url')) {
                            $fail('Le fichier est requis lorsque vous choisissez de téléverser un fichier.');
                        }
                        $file = request()->file('url');
                        $allowedMimes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'pdf', 'mp3'];
                        if (!in_array($file->getClientOriginalExtension(), $allowedMimes)) {
                            $fail('Le fichier doit être au format jpg, jpeg, png, gif, mp4, avi, mov, pdf ou mp3.');
                        }
                        if ($file->getSize() > 102400 * 1024) { // 100MB en octets
                            $fail('Le fichier ne doit pas dépasser 100 Mo.');
                        }
                    } elseif ($sourceType === 'url') {
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            $fail('L\'URL fournie n\'est pas valide.');
                        }
                    }
                }
            ],
            'type' => 'required|string|in:video,document,image,audio',
            'categorie' => 'required|string|in:tutoriel,astuce',
            'duree' => 'nullable|integer|min:1',
            'ordre' => 'nullable|integer|min:0',
            'formation_id' => 'required|exists:formations,id',
            'source_type' => 'required|in:file,url',
        ];
    }

    public function messages()
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',

            'description.string' => 'La description doit être une chaîne de caractères.',

            'url.*' => 'Le champ URL n\'est pas valide.',
            'source_type.required' => 'Le type de source est obligatoire.',
            'source_type.in' => 'Le type de source doit être soit "file" ou "url".',

            'type.required' => 'Le type est obligatoire.',
            'type.string' => 'Le type doit être une chaîne de caractères.',
            'type.in' => 'Le type doit être soit "video", "document" ou "image".',

            'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
            'categorie.required' => 'La catégorie est obligatoire.',
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
