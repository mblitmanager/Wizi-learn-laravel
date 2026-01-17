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
use Illuminate\Support\Facades\DB;
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
            $user = JWTAuth::parseToken()->authenticate();
            $perPage = $request->get('perPage', 10);

            // Récupérer l'ID du stagiaire (supposons que l'utilisateur authentifié est un stagiaire)
            $stagiaireId = $user->stagiaire->id ?? null;

            $tutoriels = $this->mediaService->getTutoriels($perPage, $stagiaireId);
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
            $user = JWTAuth::parseToken()->authenticate();
            $perPage = $request->get('perPage', 10);
            $stagiaireId = $user->stagiaire->id ?? null;

            $tutoriels = $this->mediaService->getTutorielsByFormation($formationId, $perPage, $stagiaireId);
            return response()->json($tutoriels);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getAstucesByFormation(Request $request, $formationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $perPage = $request->get('perPage', 10);
            $stagiaireId = $user->stagiaire->id ?? null;

            $astuces = $this->mediaService->getAstucesByFormation($formationId, $perPage, $stagiaireId);
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
            // Check if it's in storage/app/public
            $storagePath = storage_path('app/public/' . $path);
            if (file_exists($storagePath)) {
                $fullPath = $storagePath;
            } else {
                abort(404);
            }
        }

        // --- Achievement logic (runs before stream) ---
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stagiaire = $user->stagiaire;
            if ($stagiaire) {
                $media = \App\Models\Media::where('url', 'LIKE', '%' . $path . '%')->first();
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
                    app(\App\Services\StagiaireAchievementService::class)->checkAchievements($stagiaire);
                }
            }
        } catch (\Exception $e) {
            // Ignore auth errors for streaming
        }

        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Content-Disposition' => 'inline',
        ];

        // Using Laravel's built-in file response which handles Range requests (206 Partial Content) automatically
        // and is highly optimized.
        return response()->file($fullPath, $headers);
    }

    /**
     * Stream subtitle files (WebVTT format).
     */
    public function streamSubtitle(Request $request, $path)
    {
        $fullPath = storage_path('app/public/subtitles/' . $path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        $headers = [
            'Content-Type' => 'text/vtt',
            'Access-Control-Allow-Origin' => '*',
            'Content-Disposition' => 'inline',
        ];

        return response()->file($fullPath, $headers);
    }

    // App/Http/Controllers/Stagiaire/MediaController.php
    // Dans MediaController.php
    public function markAsWatched(Request $request, $mediaId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stagiaire = $user->stagiaire;

            if (!$stagiaire) {
                return response()->json(['error' => 'Stagiaire not found'], 404);
            }

            // Vérifier d'abord si la relation existe déjà
            $existing = DB::table('media_stagiaire')
                ->where('media_id', $mediaId)
                ->where('stagiaire_id', $stagiaire->id)
                ->first();

            if ($existing && $existing->is_watched) {
                return response()->json(['success' => false, 'message' => 'Already watched']);
            }

            // Mettre à jour ou créer la relation
            DB::table('media_stagiaire')->updateOrInsert(
                [
                    'media_id' => $mediaId,
                    'stagiaire_id' => $stagiaire->id,
                ],
                [
                    'is_watched' => true,
                    'watched_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Vérifier les achievements après marquage de la vidéo
            $achievementService = app(\App\Services\StagiaireAchievementService::class);
            $newAchievements = $achievementService->checkAchievements($stagiaire);

            return response()->json([
                'success' => true,
                'newAchievements' => $newAchievements
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getFormationsWithWatchedStatus(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stagiaire = $user->stagiaire;

            if (!$stagiaire) {
                return response()->json(['error' => 'Stagiaire not found'], 404);
            }

            $formations = $this->mediaService->getFormationsWithWatchedStatus($stagiaire->id);
            return response()->json($formations);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload a video file to the server.
     */
    public function uploadVideo(\App\Http\Requests\VideoUploadRequest $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $video = $request->file('video');
            $filename = time() . '_' . str_replace(' ', '_', $video->getClientOriginalName());

            // Store the video in /public/uploads/medias
            $uploadDir = public_path('uploads/medias');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $video->move($uploadDir, $filename);

            // Create media entry
            $media = \App\Models\Media::create([
                'titre' => $request->titre,
                'description' => $request->description,
                'formation_id' => $request->formation_id,
                'categorie' => $request->categorie,
                'ordre' => $request->ordre ?? 0,
                'type' => 'video',
                'url' => '/uploads/medias/' . $filename,
                'video_platform' => 'server',
                'video_file_path' => $filename,
                'size' => $video->getSize(),
                'mime' => $video->getMimeType(),
                'uploaded_by' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vidéo téléchargée avec succès',
                'media' => $media,
            ], 201);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du téléchargement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List server-hosted videos (paginated).
     */
    public function listServerVideos(Request $request)
    {
        try {
            JWTAuth::parseToken()->authenticate();
            $perPage = (int) $request->get('perPage', 20);
            $query = \App\Models\Media::where('video_platform', 'server')->orderBy('created_at', 'desc');
            $videos = $query->paginate($perPage);

            // append `video_url` attribute for server videos (uses accessor in model)
            $videos->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'titre' => $item->titre,
                    'description' => $item->description,
                    'url' => $item->video_url ?? $item->url,
                    'size' => $item->size,
                    'mime' => $item->mime,
                    'uploaded_by' => $item->uploaded_by,
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json($videos);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
