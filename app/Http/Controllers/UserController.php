<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Media;
use App\Models\CatalogueFormation;
use App\Notifications\QuizCreated;
use App\Notifications\MediaCreated;
use App\Notifications\NewFilleul;
use App\Notifications\CatalogueFormationUpdated;
use App\Notifications\ClassementReset;

class UserController extends Controller
{
    public function saveFcmToken(Request $request) {
        try {
            $request->user()->update(['fcm_token' => $request->token]);
            return response()->json(['message' => 'Token enregistré']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'enregistrement du token'], 500);
        }
    }

    public function notifyQuizCreated(Quiz $quiz) {
        try {
            $users = User::where('role', 'stagiaire')->get();
            foreach ($users as $user) {
                $user->notify(new QuizCreated($quiz));
            }
            return response()->json(['message' => 'Notifications envoyées']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'envoi des notifications'], 500);
        }
    }

    public function notifyMediaCreated(Media $media) {
        try {
            $users = User::where('role', 'stagiaire')->get();
            foreach ($users as $user) {
                $user->notify(new MediaCreated($media));
            }
            return response()->json(['message' => 'Notifications envoyées']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'envoi des notifications'], 500);
        }
    }

    public function notifyNewFilleul(User $filleul, User $parrain) {
        try {
            $parrain->notify(new NewFilleul($filleul));
            return response()->json(['message' => 'Notification envoyée']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'envoi de la notification'], 500);
        }
    }

    public function notifyCatalogueFormationUpdated(CatalogueFormation $formation) {
        try {
            $users = User::where('role', 'stagiaire')->get();
            foreach ($users as $user) {
                $user->notify(new CatalogueFormationUpdated($formation));
            }
            return response()->json(['message' => 'Notifications envoyées']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de l\'envoi des notifications'], 500);
        }
    }

    public function notifyClassementReset() {
       try {
        $users = User::where('role', 'stagiaire')->get();
        foreach ($users as $user) {
            $user->notify(new ClassementReset());
        }
        return response()->json(['message' => 'Notifications envoyées']);
       } catch (\Exception $e) {
        return response()->json(['message' => 'Erreur lors de l\'envoi des notifications'], 500);
       }
    }
}
