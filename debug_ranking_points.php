<?php
use App\Models\Stagiaire;
use App\Models\QuizParticipation;
use App\Models\Classement;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$herizoId = 8;
$s = Stagiaire::find($herizoId);

echo "Herizo Stagiaire ID: {$s->id}\n";

echo "\n--- Quiz Participations (Best Score per Quiz) ---\n";
$bestScores = QuizParticipation::where('user_id', $s->user_id)
    ->where('status', 'completed')
    ->select('quiz_id', DB::raw('MAX(score) as best_score'))
    ->groupBy('quiz_id')
    ->get();

foreach ($bestScores as $p) {
    echo "Quiz ID: {$p->quiz_id}, Score: {$p->best_score}\n";
}
echo "Total Participations Best: " . $bestScores->sum('best_score') . "\n";

echo "\n--- Classements ---\n";
$classements = Classement::where('stagiaire_id', $herizoId)->get();
foreach ($classements as $c) {
    echo "Quiz ID: {$c->quiz_id}, Points: {$c->points}\n";
}
echo "Total Classement Points: " . $classements->sum('points') . "\n";
