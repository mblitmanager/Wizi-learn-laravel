<?php
use App\Models\Stagiaire;
use App\Models\QuizParticipation;
use App\Models\Classement;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$herizoId = 8;
$s = Stagiaire::find($herizoId);
if (!$s) {
    echo "Herizo not found\n";
    exit;
}

echo "Herizo Stagiaire ID: " . $s->id . "\n";
echo "User ID: " . $s->user_id . "\n";

echo "\n--- Quiz Participations ---\n";
$participations = QuizParticipation::where('user_id', $s->user_id)->get();
foreach ($participations as $p) {
    echo "ID: {$p->id}, Quiz: {$p->quiz_id}, Score: {$p->score}, Status: {$p->status}\n";
}

echo "\n--- Classements ---\n";
$classements = Classement::where('stagiaire_id', $herizoId)->get();
foreach ($classements as $c) {
    echo "ID: {$c->id}, Quiz: {$c->quiz_id}, Points: {$c->points}\n";
}

$sumBest = QuizParticipation::where('user_id', $s->user_id)
    ->where('status', 'completed')
    ->groupBy('quiz_id')
    ->selectRaw('MAX(score) as best_score')
    ->get()
    ->sum('best_score');

echo "\nSum of Best Scores (Calculated): " . $sumBest . "\n";
echo "Sum of Classement Points: " . $classements->sum('points') . "\n";
