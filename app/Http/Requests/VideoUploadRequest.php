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
