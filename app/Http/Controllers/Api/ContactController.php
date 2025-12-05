<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;

class ContactController extends Controller
{
    public function sendContactForm(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'problem_type' => 'required|string',
            'message' => 'required|string',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:56048'
        ]);

        // Récupération des infos utilisateur
        $user = User::where('email', $validated['email'])->first();
        $userInfo = $user ? [
            'name' => $user->name,
            'role' => $user->role,
            'adresse' => $user->adresse,
            'last_login' => $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais',
            'is_online' => $user->is_online ? 'En ligne' : 'Hors ligne',
        ] : null;

        // Gestion des pièces jointes
        $attachments = [];
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            $files = is_array($files) ? $files : [$files];

            foreach ($files as $file) {
                if ($file instanceof UploadedFile && $file->isValid()) {
                    // 1. Stocker le fichier sur le disque public
                    $path = $file->store('attachments', 'public');

                    // 2. Préparer les données pour l'e-mail avec le chemin et le nom
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'size' => round($file->getSize() / 1024, 2),
                        'path' => $path, // C'est le chemin de stockage sur votre serveur
                    ];
                }
            }
        }

        // Préparation des données pour la vue
        $mailData = [
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'problem_type' => $validated['problem_type'],
            'messageContent' => $validated['message'],
            'isRegisteredUser' => $user !== null,
            'userInfo' => $userInfo,
            'attachments' => $attachments, // On passe directement le tableau d'attachements avec les chemins
        ];

        Mail::to('contact@wizi-learn.com')->send(
            new ContactFormMail($mailData)
        );

        return response()->json(['message' => 'Message envoyé avec succès']);
    }
}
