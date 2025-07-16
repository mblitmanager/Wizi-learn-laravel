<?php

namespace App\Http\Controllers\Stagiaire;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Models\Stagiaire;
use App\Services\MediaService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function getTutoriels(Request $request)
    {
        try {
            JWTAuth::parseToken()->authenticate();
            $perPage = $request->get('perPage', 10);
            $tutoriels = $this->mediaService->getTutoriels($perPage);
            return response()->json($tutoriels);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getAstuces(Request $request)
    {
        try {
            JWTAuth::parseToken()->authenticate();
            $perPage = $request->get('perPage', 10);
            $astuces = $this->mediaService->getAstuces($perPage);
            return response()->json($astuces);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getTutorielsByFormation(Request $request, $formationId)
    {
        try {
            JWTAuth::parseToken()->authenticate();
            $perPage = $request->get('perPage', 10);
            $tutoriels = $this->mediaService->getTutorielsByFormation($formationId, $perPage);
            return response()->json($tutoriels);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getAstucesByFormation(Request $request, $formationId)
    {
        try {
            JWTAuth::parseToken()->authenticate();
            $perPage = $request->get('perPage', 10);
            $astuces = $this->mediaService->getAstucesByFormation($formationId, $perPage);
            return response()->json($astuces);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getInteractiveFormations()
    {
        try {
            JWTAuth::parseToken()->authenticate();
            $formations = $this->mediaService->getInteractiveFormations();
            return response()->json($formations);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function stream(Request $request, $path)
    {
        $fullPath = public_path($path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        $size = filesize($fullPath);
        $start = 0;
        $length = $size;
        $mime = mime_content_type($fullPath);

        $headers = [
            'Content-Type' => $mime,
            'Content-Length' => $size,
            'Accept-Ranges' => 'bytes',
            'Access-Control-Allow-Origin' => '*',
            'Content-Disposition' => 'inline',
        ];

        if ($request->headers->has('Range')) {
            $range = $request->header('Range');
            if (preg_match('/bytes=(\d+)-(\d*)/', $range, $matches)) {
                $start = intval($matches[1]);
                $end = $matches[2] ? intval($matches[2]) : $size - 1;
                $length = $end - $start + 1;

                $headers['Content-Range'] = "bytes $start-$end/$size";
                $headers['Content-Length'] = $length;
                $status = 206;
            }
        } else {
            $status = 200;
        }

        // --- Début ajout pour achievement vidéo ---
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stagiaire = $user->stagiaire;
            if ($stagiaire) {
                // On stocke l'ID de la vidéo vue (si tutoriel)
                $media = \App\Models\Media::where('url', $path)->first();
                if ($media && $media->categorie === 'tutoriel') {
                    $videosVues = [];
                    if (property_exists($stagiaire, 'videos_vues') && is_array($stagiaire->videos_vues)) {
                        $videosVues = $stagiaire->videos_vues;
                    } elseif (isset($stagiaire->videos_vues) && is_string($stagiaire->videos_vues)) {
                        $videosVues = json_decode($stagiaire->videos_vues, true) ?: [];
                    }
                    if (!in_array($media->id, $videosVues)) {
                        $videosVues[] = $media->id;
                        $stagiaire->videos_vues = json_encode($videosVues);
                        $stagiaire->save();
                    }
                    // Vérification des achievements
                    app(\App\Services\StagiaireAchievementService::class)->checkAchievements($stagiaire);
                }
            }
        } catch (\Exception $e) {
            // On ignore les erreurs d'auth ou autres ici
        }
        // --- Fin ajout pour achievement vidéo ---

        $response = new StreamedResponse(function () use ($fullPath, $start, $length) {
            $file = fopen($fullPath, 'rb');
            fseek($file, $start);
            $remaining = $length;
            $chunkSize = 1024 * 8;

            while (!feof($file) && $remaining > 0) {
                $toRead = min($chunkSize, $remaining);
                echo fread($file, $toRead);
                flush();
                $remaining -= $toRead;
            }
            fclose($file);
        }, $status, $headers);

        return $response;
    }
}
