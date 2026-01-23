<?php
use App\Models\Stagiaire;
use App\Models\QuizParticipation;
use App\Models\Classement;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = "--- Ranking Data for Herizo (Stagiaire ID 8, User ID 19) ---\n\n";

$h8 = Stagiaire::find(8);
$output .= "Stagiaire ID 8: User ID " . ($h8->user_id ?? 'N/A') . "\n";

$output .= "\n--- Quiz Participations (All) ---\n";
$participations = QuizParticipation::where('user_id', 19)->get();
foreach ($participations as $p) {
    $output .= "ID: {$p->id}, Quiz: {$p->quiz_id}, Score: {$p->score}, Status: {$p->status}, Created: {$p->created_at}\n";
}

$output .= "\n--- Classements ---\n";
$classements = Classement::where('stagiaire_id', 8)->get();
foreach ($classements as $c) {
    $output .= "ID: {$c->id}, Quiz: {$c->quiz_id}, Points: {$c->points}, Updated: {$c->updated_at}\n";
}

$sumBest = QuizParticipation::where('user_id', 19)
    ->where('status', 'completed')
    ->groupBy('quiz_id')
    ->selectRaw('MAX(score) as best_score')
    ->get()
    ->sum('best_score');

$output .= "\nCalculated Sum of Best Participations: $sumBest\n";
$output .= "Sum of Classement Points: " . $classements->sum('points') . "\n";

file_put_contents('ranking_debug_output.txt', $output);
echo "Done! Output in ranking_debug_output.txt\n";
