<?php

namespace Database\Seeders;

use App\Models\Formation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class FormationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $formations = [
            [
                'titre' => 'Bureautique',
                'slug' => Str::slug('Bureautique'),
                'description' => 'Formation sur les outils bureautiques.',
                'categorie' => 'Informatique',
                'icon' => 'fa-file-word',
                'image' => null,
                'statut' => true,
                'duree' => 20,
            ],
            [
                'titre' => 'Langues',
                'slug' => Str::slug('Langues'),
                'description' => 'Formation sur les langues étrangères.',
                'categorie' => 'Langues',
                'icon' => 'fa-language',
                'image' => null,
                'statut' => true,
                'duree' => 30,
            ],
            [
                'titre' => 'Internet',
                'slug' => Str::slug('Internet'),
                'description' => 'Formation sur l\'utilisation d\'Internet.',
                'categorie' => 'Informatique',
                'icon' => 'fa-globe',
                'image' => null,
                'statut' => true,
                'duree' => 15,
            ],
            [
                'titre' => 'Création',
                'slug' => Str::slug('Création'),
                'description' => 'Formation sur la création numérique.',
                'categorie' => 'Créatif',
                'icon' => 'fa-paint-brush',
                'image' => null,
                'statut' => true,
                'duree' => 25,
            ],
        ];

        foreach ($formations as $formation) {
            Formation::create($formation);
        }
    }
}
