<?php

namespace Database\Seeders;

use App\Models\ParrainageEvent;
use Illuminate\Database\Seeder;

class ParrainageEventSeeder extends Seeder
{
    public function run(): void
    {
        // [titre, prime en €, mois de début, durée en mois]
        $events = [
            ['Parrainage de la rentrée', 50, 9, 1],
            ['Octobre numérique', 60, 10, 1],
            ['Challenge Black Friday', 80, 11, 1],
            ['Parrainage de Noël', 100, 12, 1],
            ['Bonnes résolutions', 60, 1, 1],
            ['Février Bureautique', 50, 2, 1],
            ['Printemps des langues', 70, 3, 2],
            ['Mois de l\'IA', 90, 5, 1],
            ['Parrainage d\'été', 50, 6, 2],
            ['Semaine spéciale CPF', 120, 8, 1],
        ];

        // Saison septembre -> août commençant à la rentrée en cours
        $anneeRentree = now()->month >= 9 ? now()->year : now()->year - 1;

        foreach ($events as [$titre, $prix, $mois, $duree]) {
            $debut = now()->setDate($mois >= 9 ? $anneeRentree : $anneeRentree + 1, $mois, 1)->startOfDay();

            ParrainageEvent::updateOrCreate(
                ['titre' => $titre],
                [
                    'prix' => $prix,
                    'date_debut' => $debut->toDateString(),
                    'date_fin' => $debut->copy()->addMonths($duree)->subDay()->toDateString(),
                ]
            );
        }
    }
}
