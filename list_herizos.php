<?php
use App\Models\Stagiaire;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$herizos = Stagiaire::where('prenom', 'like', 'Herizo%')->get();
$output = "--- Herizo Stagiaires ---\n";
foreach ($herizos as $h) {
    $points = DB::table('classements')->where('stagiaire_id', $h->id)->sum('points');
    $pCount = DB::table('quiz_participations')->where('user_id', $h->user_id)->count();
    $output .= "Stagiaire ID: {$h->id}, Name: {$h->prenom} {$h->nom}, User ID: {$h->user_id}, Total Classement Points: {$points}, Participations: {$pCount}\n";
}

echo $output;
file_put_contents('herizo_list.txt', $output);
