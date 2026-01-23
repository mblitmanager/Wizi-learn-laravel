<?php
use App\Models\QuizParticipation;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$quizId = 303;
$ps = QuizParticipation::where('quiz_id', $quizId)->get();
echo "Found " . $ps->count() . " participations for quiz $quizId\n";
foreach ($ps as $p) {
    echo "User ID: {$p->user_id}, Status: {$p->status}, Started: {$p->started_at}\n";
}
