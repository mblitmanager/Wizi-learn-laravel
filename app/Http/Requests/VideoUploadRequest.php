<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'video' => 'required|file|mimetypes:video/mp4,video/ogg,video/webm,video/quicktime,video/x-matroska,video/mpeg|max:512000', // max ~500MB
            'titre' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'formation_id' => 'nullable|integer|exists:formations,id',
            'categorie' => 'nullable|string',
            'ordre' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'video.required' => 'Le fichier vidéo est requis.',
            'video.mimetypes' => 'Format vidéo non supporté.',
            'video.max' => 'La taille maximale est de 500MB.',
        ];
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your auth requirements
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'video' => [
                'required',
                'file',
                'mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo',
                'max:102400', // 100MB max
            ],
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'formation_id' => 'required|exists:formations,id',
            'categorie' => 'required|in:tutoriel,astuce',
            'ordre' => 'nullable|integer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'video.required' => 'Un fichier vidéo est requis.',
            'video.mimetypes' => 'Le fichier doit être une vidéo au format MP4, WebM, MOV ou AVI.',
            'video.max' => 'La vidéo ne doit pas dépasser 100 MB.',
            'formation_id.required' => 'Une formation doit être sélectionnée.',
            'formation_id.exists' => 'La formation sélectionnée n\'existe pas.',
        ];
    }
}
