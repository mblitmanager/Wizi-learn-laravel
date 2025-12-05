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
        $sourceType = $this->input('source_type', 'file');
        $mediaId = $this->route('media'); // For edit mode

        $rules = [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:video,document,image,audio',
            'categorie' => 'required|string|in:tutoriel,astuce',
            'duree' => 'nullable|integer|min:1',
            'ordre' => 'nullable|integer|min:0',
            'formation_id' => 'required|exists:formations,id',
            'source_type' => 'required|in:file,url',
        ];

        // Conditional validation based on source_type
        if ($sourceType === 'file') {
            // In edit mode, file is optional (keep existing if not uploaded)
            if ($mediaId) {
                $rules['url'] = [
                    'nullable',
                    'file',
                    'max:512000', // 500MB
                    'mimes:jpg,jpeg,png,gif,mp4,avi,mov,pdf,mp3,wav,ogg,mkv'
                ];
            } else {
                // In create mode, file is required
                $rules['url'] = [
                    'required',
                    'file',
                    'max:512000', // 500MB
                    'mimes:jpg,jpeg,png,gif,mp4,avi,mov,pdf,mp3,wav,ogg,mkv'
                ];
            }
        } else {
            // source_type === 'url'
            $rules['url'] = 'required|url|max:500';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne doit pas dépasser 255 caractères.',

            'description.string' => 'La description doit être une chaîne de caractères.',

            'url.required' => 'Le fichier ou l\'URL est requis.',
            'url.file' => 'Un fichier valide est requis.',
            'url.max' => 'Le fichier ne doit pas dépasser 500 MB.',
            'url.mimes' => 'Le fichier doit être au format jpg, jpeg, png, gif, mp4, avi, mov, pdf, mp3, wav, ogg ou mkv.',
            'url.url' => 'L\'URL fournie n\'est pas valide.',
            
            'source_type.required' => 'Le type de source est obligatoire.',
            'source_type.in' => 'Le type de source doit être soit "file" ou "url".',

            'type.required' => 'Le type est obligatoire.',
            'type.string' => 'Le type doit être une chaîne de caractères.',
            'type.in' => 'Le type doit être soit "video", "document", "image" ou "audio".',

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
