<?php

namespace App\Http\Controllers\Stagiaire;

use App\Helpers\PaginationHelper;
use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function stream(Request $request, $path)
    {
        $fullPath = public_path($path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        $size = filesize($fullPath);
        $start = 0;
        $length = $size;

        // Détection dynamique du type MIME
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
