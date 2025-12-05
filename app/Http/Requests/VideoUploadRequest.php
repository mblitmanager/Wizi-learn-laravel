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
        return true;
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
                'mimetypes:video/mp4,video/webm,video/quicktime,video/x-msvideo,video/mpeg,video/ogg,video/x-matroska',
                'max:512000', // 500MB max (increased for production)
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
            'video.mimetypes' => 'Le fichier doit être une vidéo au format MP4, WebM, MOV, AVI, MPEG, OGG ou MKV.',
            'video.max' => 'La vidéo ne doit pas dépasser 500 MB.',
            'video.uploaded' => 'Le fichier n\'a pas pu être uploadé. Vérifiez la taille du fichier et les configurations du serveur.',
            'formation_id.required' => 'Une formation doit être sélectionnée.',
            'formation_id.exists' => 'La formation sélectionnée n\'existe pas.',
            'titre.required' => 'Un titre est requis.',
            'categorie.required' => 'Une catégorie est requise.',
            'categorie.in' => 'La catégorie doit être tutoriel ou astuce.',
        ];
    }
}
