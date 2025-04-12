<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PoleRelationClientRequest extends FormRequest
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
        $prcid = $this->route('pole_relation_clients');
        $userId = \App\Models\PoleRelationClient::find($prcid)?->user_id;
        return [
            'name' => 'required|string|max:255',
            'prenom' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8',
            'role' => [
                Rule::in(['pole relation client']),
            ],
            'stagiaire_id' => 'required|exists:stagiaires,id',
        ];
    }
}
