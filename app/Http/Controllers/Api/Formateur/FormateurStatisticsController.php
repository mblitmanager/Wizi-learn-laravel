<?php

namespace App\Http\Controllers\Api\Formateur;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Stagiaire;
use App\Models\ResultatQuiz;
use App\Models\ProgressionStagiaire;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class FormateurStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // TODO: Add formateur role middleware
        // $this->middleware('role:formateur');
    }

    /**
     * Get formateur dashboard overview
     */
    public function dashboard(): JsonResponse
    {
        try {
            $formateurId = Auth::id();
            $myFormations = $this->getMyFormationsList($formateurId);
            
            $data = [
                'summary' => [
                    'totalFormations' => count($myFormations),
                    'totalStudents' => $this->getTotalStudents($formateurId),
                    'averageCompletion' => $this->getAverageCompletion($formateurId),
                    'activeStudents' => $this->getActiveStudents($formateurId),
                ],
                'myFormations' => $myFormations,
                'recentActivity' => $this->getRecentActivity($formateurId, 10),
                'topStudents' => $this->getTopStudents($formateurId, 5),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch formateur dashboard',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get my formations list with stats
     */
    public function myFormations(): JsonResponse
    {
        try {
            $formateurId = Auth::id();
            $formations = $this->getMyFormationsList($formateurId);

            return response()->json($formations);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch formations',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quiz statistics for a specific formation
     */
    public function formationQuizStats(int $id): JsonResponse
    {
        try {
            $formateurId = Auth::id();
            
            // Verify formateur owns this formation
            if (!$this->ownsFormation($formateurId, $id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $data = [
                'averageScore' => $this->getAverageQuizScore($id),
                'successRate' => $this->getQuizSuccessRate($id),
                'totalAttempts' => $this->getTotalQuizAttempts($id),
                'hardestQuestions' => $this->getHardestQuestions($id, 5),
                'scoreDistribution' => $this->getScoreDistribution($id),
                'recentResults' => $this->getRecentQuizResults($id, 10),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch quiz statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students for a specific formation
     */
    public function formationStudents(int $id): JsonResponse
    {
        try {
            $formateurId = Auth::id();
            
            // Verify formateur owns this formation
            if (!$this->ownsFormation($formateurId, $id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $students = Formation::find($id)
                ->stagiaires()
                ->with(['user', 'progressions' => function ($q) use ($id) {
                    $q->where('formation_id', $id);
                }])
                ->get()
                ->map(function ($stagiaire) use ($id) {
                    $progression = $stagiaire->progressions->first();
                    
                    return [
                        'id' => $stagiaire->id,
                        'name' => $stagiaire->user->name ?? 'Unknown',
                        'email' => $stagiaire->user->email ?? '',
                        'progress' => $progression->progression ?? 0,
                        'last_activity' => $stagiaire->user->last_activity ?? null,
                        'enrolled_at' => $stagiaire->created_at->format('Y-m-d'),
                    ];
                });

            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch students',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper methods
     */
    
    private function getMyFormationsList(int $formateurId): array
    {
        return Formation::where('formateur_id', $formateurId)
            ->withCount('stagiaires')
            ->get()
            ->map(function ($formation) {
                return [
                    'id' => $formation->id,
                    'titre' => $formation->titre,
                    'students_count' => $formation->stagiaires_count,
                    'completion_rate' => $this->getFormationCompletionRate($formation->id),
                    'average_progress' => $this->getFormationAverageProgress($formation->id),
                ];
            })
            ->toArray();
    }

    private function getTotalStudents(int $formateurId): int
    {
        return DB::table('formation_stagiaire')
            ->join('formations', 'formation_stagiaire.formation_id', '=', 'formations.id')
            ->where('formations.formateur_id', $formateurId)
            ->distinct('formation_stagiaire.stagiaire_id')
            ->count('formation_stagiaire.stagiaire_id');
    }

    private function getAverageCompletion(int $formateurId): float
    {
        $avg = DB::table('progression_stagiaire')
            ->join('formations', 'progression_stagiaire.formation_id', '=', 'formations.id')
            ->where('formations.formateur_id', $formateurId)
            ->avg('progression_stagiaire.progression');

        return round($avg ?? 0, 2);
    }

    private function getActiveStudents(int $formateurId): int
    {
        return DB::table('formation_stagiaire')
            ->join('formations', 'formation_stagiaire.formation_id', '=', 'formations.id')
            ->join('stagiaires', 'formation_stagiaire.stagiaire_id', '=', 'stagiaires.id')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->where('formations.formateur_id', $formateurId)
            ->where('users.last_activity', '>=', Carbon::now()->subDays(7))
            ->distinct('stagiaires.id')
            ->count('stagiaires.id');
    }

    private function getRecentActivity(int $formateurId, int $limit): array
    {
        // TODO: Implement with activity log
        return [];
    }

    private function getTopStudents(int $formateurId, int $limit): array
    {
        return DB::table('progression_stagiaire')
            ->join('formations', 'progression_stagiaire.formation_id', '=', 'formations.id')
            ->join('stagiaires', 'progression_stagiaire.stagiaire_id', '=', 'stagiaires.id')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->where('formations.formateur_id', $formateurId)
            ->select(
                'stagiaires.id',
                'users.name',
                DB::raw('AVG(progression_stagiaire.progression) as avg_progress')
            )
            ->groupBy('stagiaires.id', 'users.name')
            ->orderBy('avg_progress', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function ownsFormation(int $formateurId, int $formationId): bool
    {
        return Formation::where('id', $formationId)
            ->where('formateur_id', $formateurId)
            ->exists();
    }

    private function getFormationCompletionRate(int $formationId): float
    {
        $total = ProgressionStagiaire::where('formation_id', $formationId)->count();
        $completed = ProgressionStagiaire::where('formation_id', $formationId)
            ->where('progression', 100)
            ->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    private function getFormationAverageProgress(int $formationId): float
    {
        return round(
            ProgressionStagiaire::where('formation_id', $formationId)
                ->avg('progression') ?? 0,
            2
        );
    }

    private function getAverageQuizScore(int $formationId): float
    {
        return DB::table('resultat_quiz')
            ->join('quizs', 'resultat_quiz.quiz_id', '=', 'quizs.id')
            ->where('quizs.formation_id', $formationId)
            ->avg('resultat_quiz.score') ?? 0;
    }

    private function getQuizSuccessRate(int $formationId): float
    {
        $total = DB::table('resultat_quiz')
            ->join('quizs', 'resultat_quiz.quiz_id', '=', 'quizs.id')
            ->where('quizs.formation_id', $formationId)
            ->count();

        $passed = DB::table('resultat_quiz')
            ->join('quizs', 'resultat_quiz.quiz_id', '=', 'quizs.id')
            ->where('quizs.formation_id', $formationId)
            ->where('resultat_quiz.reussi', true)
            ->count();

        return $total > 0 ? round(($passed / $total) * 100, 2) : 0;
    }

    private function getTotalQuizAttempts(int $formationId): int
    {
        return DB::table('resultat_quiz')
            ->join('quizs', 'resultat_quiz.quiz_id', '=', 'quizs.id')
            ->where('quizs.formation_id', $formationId)
            ->count();
    }

    private function getHardestQuestions(int $formationId, int $limit): array
    {
        return DB::table('questions')
            ->join('quizs', 'questions.quiz_id', '=', 'quizs.id')
            ->join('reponses', 'questions.id', '=', 'reponses.question_id')
            ->where('quizs.formation_id', $formationId)
            ->select(
                'questions.id',
                'questions.intitule_question as text',
                DB::raw('COUNT(*) as attempts'),
                DB::raw('SUM(CASE WHEN reponses.is_correct = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*) as success_rate')
            )
            ->groupBy('questions.id', 'questions.intitule_question')
            ->orderBy('success_rate', 'asc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getScoreDistribution(int $formationId): array
    {
        return DB::table('resultat_quiz')
            ->join('quizs', 'resultat_quiz.quiz_id', '=', 'quizs.id')
            ->where('quizs.formation_id', $formationId)
            ->select(
                DB::raw('FLOOR(score / 10) * 10 as range_start'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('range_start')
            ->orderBy('range_start')
            ->get()
            ->toArray();
    }

    private function getRecentQuizResults(int $formationId, int $limit): array
    {
        return DB::table('resultat_quiz')
            ->join('quizs', 'resultat_quiz.quiz_id', '=', 'quizs.id')
            ->join('stagiaires', 'resultat_quiz.stagiaire_id', '=', 'stagiaires.id')
            ->join('users', 'stagiaires.user_id', '=', 'users.id')
            ->where('quizs.formation_id', $formationId)
            ->select(
                'users.name as student_name',
                'quizs.titre as quiz_title',
                'resultat_quiz.score',
                'resultat_quiz.reussi',
                'resultat_quiz.created_at'
            )
            ->orderBy('resultat_quiz.created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
