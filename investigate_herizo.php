<?php
use App\Models\Stagiaire;
use App\Models\Quiz;
use App\Models\QuizParticipation;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Stagiaires named Herizo ---\n";
$herizos = Stagiaire::where('prenom', 'like', 'Herizo%')->get();
foreach ($herizos as $h) {
    echo "ID: {$h->id}, Name: {$h->prenom} {$h->nom}, User ID: {$h->user_id}, Email: " . ($h->user->email ?? 'N/A') . "\n";
}

echo "\n--- Quizzes taken by Herizo (ID 8) ---\n";
$participations = QuizParticipation::where('user_id', 19) // Herizo ID 8 has User ID 19
    ->where('status', 'completed')
    ->groupBy('quiz_id')
    ->pluck('quiz_id');

foreach ($participations as $qid) {
    $q = Quiz::find($qid);
    echo "Quiz ID: {$qid}, Title: {$q->titre}, Formation ID: " . ($q->formation_id ?? 'NULL') . "\n";
}
