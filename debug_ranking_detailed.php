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
if (!$s) { echo "Herizo not found\n"; exit; }

echo "Herizo Stagiaire ID: {$s->id}\n";
echo "Count of Formateurs: " . DB::table('formateur_stagiaire')->where('stagiaire_id', $herizoId)->count() . "\n";
echo "Formateur IDs: " . DB::table('formateur_stagiaire')->where('stagiaire_id', $herizoId)->pluck('formateur_id')->implode(', ') . "\n";

echo "\n--- Quiz Participations (best scores only) ---\n";
$bestParticipations = QuizParticipation::where('user_id', $s->user_id)
    ->where('status', 'completed')
    ->select('quiz_id', DB::raw('MAX(score) as best_score'))
    ->groupBy('quiz_id')
    ->get();

foreach ($bestParticipations as $p) {
    echo "Quiz: {$p->quiz_id}, Best Score: {$p->best_score}\n";
}
echo "Total Best Score Sum: " . $bestParticipations->sum('best_score') . "\n";

echo "\n--- Classements ---\n";
$classements = Classement::where('stagiaire_id', $herizoId)->get();
foreach ($classements as $c) {
    echo "Quiz: {$c->quiz_id}, Points: {$c->points}\n";
}
echo "Total Classement Sum: " . $classements->sum('points') . "\n";

// Emulate Arena Ranking Query for this stagiaire
$arenaPoints = DB::table('stagiaires as s')
    ->join('users as su', 'su.id', '=', 's.user_id')
    ->join('formateur_stagiaire as fs', 'fs.stagiaire_id', '=', 's.id')
    ->leftJoin(DB::raw("(SELECT user_id, quiz_id, MAX(score) as best_score FROM quiz_participations WHERE status = 'completed' GROUP BY user_id, quiz_id) as best_attempts"), 'su.id', '=', 'best_attempts.user_id')
    ->where('s.id', $herizoId)
    ->where('fs.formateur_id', 11) // Elodie
    ->select(DB::raw('SUM(best_attempts.best_score) as points'))
    ->first();

echo "\nEmulated Arena Sum for Elodie (id 11): " . ($arenaPoints->points ?? 0) . "\n";

// Check for duplication in the join
$rowCount = DB::table('stagiaires as s')
    ->join('users as su', 'su.id', '=', 's.user_id')
    ->join('formateur_stagiaire as fs', 'fs.stagiaire_id', '=', 's.id')
    ->leftJoin(DB::raw("(SELECT user_id, quiz_id, MAX(score) as best_score FROM quiz_participations WHERE status = 'completed' GROUP BY user_id, quiz_id) as best_attempts"), 'su.id', '=', 'best_attempts.user_id')
    ->where('s.id', $herizoId)
    ->where('fs.formateur_id', 11)
    ->count();

echo "Row count in Arena join for Herizo: $rowCount\n";
