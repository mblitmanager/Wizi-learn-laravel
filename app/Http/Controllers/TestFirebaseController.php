<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * @method \Illuminate\Routing\PendingMiddleware middleware(string $middleware)
 */
class TestFirebaseController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
        $this->middleware('jwt.auth');
    }

    public function test(): JsonResponse
    {
        try {
            // Test de la connexion à la base de données
            $database = $this->firebase->getDatabase();

            return response()->json([
                'status' => 'success',
                'message' => 'Firebase est correctement configuré'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de configuration Firebase: ' . $e->getMessage()
            ], 500);
        }
    }
}
