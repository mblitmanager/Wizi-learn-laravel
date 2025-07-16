<?php
namespace App\Http\Controllers\Stagiaire;

use App\Http\Controllers\Controller;
use App\Services\StagiaireAchievementService;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AchievementController extends Controller
{
    protected $achievementService;

    public function __construct(StagiaireAchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    // GET /api/stagiaire/achievements
    public function getAchievements(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stagiaire = $user->stagiaire;
            if (!$stagiaire) {
                return response()->json(['error' => 'Stagiaire non trouvé'], 404);
            }
            $achievements = $stagiaire->achievements()->get();
            return response()->json(['achievements' => $achievements]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    // POST /api/stagiaire/achievements/check
    public function checkAchievements(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $stagiaire = $user->stagiaire;
            if (!$stagiaire) {
                return response()->json(['error' => 'Stagiaire non trouvé'], 404);
            }

            // Si un code d'achievement est passé (ex: android_download)
            $code = $request->input('code');
            if ($code) {
                $newAchievements = $this->achievementService->unlockAchievementByCode($stagiaire, $code);
            } else {
                $newAchievements = $this->achievementService->checkAchievements($stagiaire);
            }
            return response()->json(['new_achievements' => $newAchievements]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
