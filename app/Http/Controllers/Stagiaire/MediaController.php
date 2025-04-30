<?php

namespace App\Http\Controllers\Stagiaire;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function getTutoriels()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $tutoriels = $this->mediaService->getTutoriels();
            $data = PaginationHelper::paginate($tutoriels, 10);
            return response()->json($data);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getAstuces()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $astuces = $this->mediaService->getAstuces();
            return response()->json($astuces);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getTutorielsByFormation($formationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $tutoriels = $this->mediaService->getTutorielsByFormation($formationId);
            return response()->json($tutoriels);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getAstucesByFormation($formationId)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $astuces = $this->mediaService->getAstucesByFormation($formationId);
            return response()->json($astuces);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function getInteractiveFormations()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $formations = $this->mediaService->getInteractiveFormations();
            return response()->json($formations);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function streamVideo(Request $request, $videoName)
    {
        $videoPath = public_path('uploads/medias/' . $videoName);
        $fileSize = filesize($videoPath);
        $range = $request->header('Range');

        if ($range) {
            // Parsing du Range
            preg_match('/bytes=(\d+)-(\d*)/', $range, $matches);
            $start = (int) $matches[1];
            $end = $matches[2] ? (int) $matches[2] : $fileSize - 1;
            $length = $end - $start + 1;

            // Set the headers
            $headers = [
                'Content-Type' => 'video/mp4',
                'Content-Length' => $length,
                'Content-Range' => "bytes $start-$end/$fileSize",
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
            ];

            $file = fopen($videoPath, 'rb');
            fseek($file, $start);
            $stream = fread($file, $length);
            fclose($file);

            // Return the response with the video chunk
            return response($stream, 206, $headers);
        }

        // If no range, serve the entire video
        $headers = [
            'Content-Type' => 'video/mp4',
            'Content-Length' => $fileSize,
        ];

        return response()->file($videoPath, $headers);
    }
}
