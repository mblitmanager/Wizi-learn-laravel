<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\QuizParticipation;
use App\Models\Classement;
use App\Models\Stagiaire;
use Illuminate\Support\Facades\DB;

class SyncRankingCommand extends Command
{
    protected $signature = 'ranking:sync';
    protected $description = 'Synchronize Classement table with best scores from QuizParticipation';

    public function handle()
    {
        $this->info('Starting ranking sync...');

        // Clear existing rankings to avoid duplicates or orphans
        DB::table('classements')->truncate();
        $this->info('Truncated classements table.');

        // Get all best scores per (user_id, quiz_id)
        $bestScores = QuizParticipation::where('status', 'completed')
            ->select('user_id', 'quiz_id', DB::raw('MAX(score) as best_score'), DB::raw('MAX(updated_at) as last_updated'))
            ->groupBy('user_id', 'quiz_id')
            ->get();

        $this->info('Found ' . $bestScores->count() . ' best attempts to sync.');

        foreach ($bestScores as $attempt) {
            $stagiaire = Stagiaire::where('user_id', $attempt->user_id)->first();
            
            if (!$stagiaire) {
                $this->warn("No stagiaire found for user_id: {$attempt->user_id}. Skipping.");
                continue;
            }

            Classement::create([
                'stagiaire_id' => $stagiaire->id,
                'quiz_id' => $attempt->quiz_id,
                'points' => $attempt->best_score,
                'created_at' => $attempt->last_updated,
                'updated_at' => $attempt->last_updated,
            ]);
        }

        $this->info('Ranking sync completed successfully!');
        
        // Update ranks for each quiz
        $quizIds = Classement::distinct()->pluck('quiz_id');
        foreach ($quizIds as $qid) {
            $this->updateRanks($qid);
        }
        $this->info('Ranks updated for all quizzes.');
    }

    private function updateRanks($quizId)
    {
        $classements = Classement::where('quiz_id', $quizId)
            ->orderBy('points', 'desc')
            ->get();

        $rank = 1;
        foreach ($classements as $classement) {
            $classement->update(['rang' => $rank++]);
        }
    }
}
