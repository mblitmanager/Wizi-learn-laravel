<?php

namespace App\Repositories;

use App\Models\Stagiaire;
use App\Repositories\Interfaces\StagiaireRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StagiaireRepository implements StagiaireRepositoryInterface
{
    public function all(): Collection
    {
        return Stagiaire::with(['formations'])->get();
    }

    public function find($id): ?Stagiaire
    {
        return Stagiaire::with(['formations'])->find($id);
    }

    public function create(array $data): Stagiaire
    {
        return Stagiaire::create($data);
    }

    public function update($id, array $data): bool
    {
        $stagiaire = Stagiaire::find($id);
        return $stagiaire ? $stagiaire->update($data) : false;
    }

    public function delete($id): bool
    {
        $stagiaire = Stagiaire::find($id);
        return $stagiaire ? $stagiaire->delete() : false;
    }

    public function desactive($id): bool
    {
        $stagiaire = Stagiaire::find($id);
        if ($stagiaire) {
            $stagiaire->statut = false;
            return $stagiaire->save();
        }
        return false;
    }

    public function active($id): bool
    {
        $stagiaire = Stagiaire::find($id);
        if ($stagiaire) {
            $stagiaire->statut = true;
            return $stagiaire->save();
        }
        return false;
    }

    public function getStagiaireStats($id)
    {
        $stagiaire = $this->find($id);
        
        // Get quizzes through formations
        $quizzes = collect();
        foreach ($stagiaire->formations as $formation) {
            $quizzes = $quizzes->merge($formation->quizzes);
        }
        
        return [
            'total_formations' => $stagiaire->formations->count(),
            'completed_formations' => $stagiaire->formations->where('completed', true)->count(),
            'total_quizzes' => $quizzes->count(),
            'average_quiz_score' => $quizzes->avg('score'),
            'total_points' => $quizzes->sum('points')
        ];
    }

    public function getStagiaireRankings($id)
    {
        $globalRanking = DB::table('stagiaires')
            ->select('stagiaires.id', DB::raw('SUM(quizzes.points) as total_points'))
            ->leftJoin('formations', 'stagiaires.formation_id', '=', 'formations.id')
            ->leftJoin('quizzes', 'formations.id', '=', 'quizzes.formation_id')
            ->groupBy('stagiaires.id')
            ->orderBy('total_points', 'desc')
            ->get();

        $position = $globalRanking->search(function($item) use ($id) {
            return $item->id == $id;
        }) + 1;

        return [
            'global_ranking' => $position,
            'total_stagiaires' => $globalRanking->count()
        ];
    }

    public function getStagiaireProgress($id)
    {
        $stagiaire = $this->find($id);
        
        return [
            'formations_progress' => $stagiaire->formations->map(function($formation) {
                return [
                    'formation_id' => $formation->id,
                    'title' => $formation->title,
                    'progress' => $formation->pivot->progress,
                    'completed' => $formation->pivot->completed
                ];
            }),
            'quizzes_progress' => $stagiaire->formations->flatMap(function($formation) {
                return $formation->quizzes->map(function($quiz) {
                    return [
                        'quiz_id' => $quiz->id,
                        'title' => $quiz->title,
                        'score' => $quiz->score,
                        'completed' => $quiz->completed
                    ];
                });
            })
        ];
    }
}
