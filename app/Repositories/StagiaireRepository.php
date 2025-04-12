<?php

namespace App\Repositories;

use App\Models\Stagiaire;
use App\Repositories\Interfaces\StagiaireRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StagiaireRepository implements StagiaireRepositoryInterface
{
    public function all()
    {
        return Stagiaire::with(['formations', 'quizzes'])->get();
    }

    public function find($id): ?Stagiaire
    {
        return Stagiaire::with(['formations', 'quizzes'])->find($id);
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

    public function getStagiaireStats($id)
    {
        $stagiaire = $this->find($id);
        
        return [
            'total_formations' => $stagiaire->formations->count(),
            'completed_formations' => $stagiaire->formations->where('completed', true)->count(),
            'total_quizzes' => $stagiaire->quizzes->count(),
            'average_quiz_score' => $stagiaire->quizzes->avg('score'),
            'total_points' => $stagiaire->quizzes->sum('points')
        ];
    }

    public function getStagiaireRankings($id)
    {
        $globalRanking = DB::table('stagiaires')
            ->select('id', DB::raw('SUM(quizzes.points) as total_points'))
            ->leftJoin('quizzes', 'stagiaires.id', '=', 'quizzes.stagiaire_id')
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
            'quizzes_progress' => $stagiaire->quizzes->map(function($quiz) {
                return [
                    'quiz_id' => $quiz->id,
                    'title' => $quiz->title,
                    'score' => $quiz->score,
                    'completed' => $quiz->completed
                ];
            })
        ];
    }
}
