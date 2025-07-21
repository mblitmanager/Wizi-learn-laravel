<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;

class MissionSeeder extends Seeder
{
    public function run()
    {
        Mission::create([
            'title' => 'Réussir 2 quiz aujourd\'hui',
            'description' => 'Complète 2 quiz pour gagner un badge.',
            'type' => 'daily',
            'goal' => 2,
            'reward' => 'Badge',
            'start_date' => now(),
            'end_date' => now()->addDays(1),
        ]);
        Mission::create([
            'title' => 'Cumuler 10 étoiles cette semaine',
            'description' => 'Obtiens 10 étoiles sur tes quiz cette semaine.',
            'type' => 'weekly',
            'goal' => 10,
            'reward' => 'Badge',
            'start_date' => now()->startOfWeek(),
            'end_date' => now()->endOfWeek(),
        ]);
        Mission::create([
            'title' => 'Terminer un quiz avancé',
            'description' => 'Joue et termine un quiz de niveau avancé.',
            'type' => 'special',
            'goal' => 1,
            'reward' => 'Points',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
        ]);
    }
} 