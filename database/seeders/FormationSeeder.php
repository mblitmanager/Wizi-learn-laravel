<?php

namespace Database\Seeders;

use App\Models\Formation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Catégories de formation (table "formations"), reprises de https://likeformation.fr/nos-formations/
 */
class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $formations = [
            [
                'titre' => 'Intelligence Artificielle',
                'description' => 'Formations certifiantes à l\'usage responsable de l\'IA générative pour créer des contenus rédactionnels et visuels.',
                'categorie' => 'IA',
                'icon' => 'fa-robot',
                'duree' => 17,
            ],
            [
                'titre' => 'Bureautique',
                'description' => 'Maîtrisez les outils Microsoft Office et Google (Word, Excel, PowerPoint, Outlook, Docs, Sheets, Slides) avec une certification TOSA ou ICDL.',
                'categorie' => 'Bureautique',
                'icon' => 'fa-file-word',
                'duree' => 20,
            ],
            [
                'titre' => 'Internet',
                'description' => 'Créez un site avec WordPress et utilisez les outils collaboratifs Google Workspace.',
                'categorie' => 'Internet',
                'icon' => 'fa-globe',
                'duree' => 20,
            ],
            [
                'titre' => 'Création',
                'description' => 'Retouche photo, dessin vectoriel et modélisation 3D avec Photoshop, Illustrator, GIMP et SketchUp.',
                'categorie' => 'Création',
                'icon' => 'fa-paint-brush',
                'duree' => 20,
            ],
            [
                'titre' => 'Langues',
                'description' => 'Comprendre et se faire comprendre en anglais ou en français, avec une certification reconnue (TOEIC, Voltaire…).',
                'categorie' => 'Langues',
                'icon' => 'fa-language',
                'duree' => 20,
            ],
            [
                'titre' => 'Les Bases du Digital',
                'description' => 'Acquérir les compétences numériques essentielles du référentiel européen DigComp.',
                'categorie' => 'Digital',
                'icon' => 'fa-laptop',
                'duree' => 15,
            ],
        ];

        foreach ($formations as $formation) {
            $slug = Str::slug($formation['titre']);

            Formation::updateOrCreate(
                ['slug' => $slug],
                $formation + ['slug' => $slug, 'image' => null, 'statut' => true]
            );
        }
    }
}
