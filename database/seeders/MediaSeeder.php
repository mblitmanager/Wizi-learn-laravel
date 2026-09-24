<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\Media;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * 10 vidéos (tutoriels et astuces) rattachées aux catégories de formation,
 * puis quelques visionnages par les stagiaires.
 *
 * Les fichiers vidéo ne sont pas fournis : video_file_path pointe vers
 * storage/app/videos/seed/*.mp4, à déposer ou remplacer via l'admin.
 */
class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $medias = [
            ['Intelligence Artificielle', 'Rédiger un prompt efficace', 'Les 5 composantes d\'un bon prompt : rôle, contexte, tâche, format et contraintes.', 'tutoriel', 420],
            ['Intelligence Artificielle', 'Protéger ses données avec l\'IA', 'Les bons réflexes pour ne pas exposer d\'informations confidentielles.', 'astuce', 180],
            ['Bureautique', 'Créer une table des matières dans Word', 'Utiliser les styles de titres pour générer une table des matières automatique.', 'tutoriel', 360],
            ['Bureautique', 'Maîtriser RECHERCHEV dans Excel', 'Retrouver une information dans un tableau avec la fonction RECHERCHEV.', 'tutoriel', 540],
            ['Bureautique', '3 raccourcis PowerPoint indispensables', 'Gagnez du temps avec les raccourcis clavier les plus utiles.', 'astuce', 150],
            ['Internet', 'Installer une extension WordPress', 'Rechercher, installer et activer une extension depuis le tableau de bord.', 'tutoriel', 300],
            ['Création', 'Détourer une image avec Photoshop', 'Sélection du sujet, affinage des contours et masque de fusion.', 'tutoriel', 600],
            ['Création', 'L\'outil Plume d\'Illustrator en 3 minutes', 'Tracer des courbes précises avec les points d\'ancrage.', 'astuce', 200],
            ['Langues', 'Rédiger un e-mail professionnel en anglais', 'Structure, formules d\'ouverture et de clôture en anglais.', 'tutoriel', 480],
            ['Les Bases du Digital', 'Repérer un e-mail de phishing', 'Les signaux qui doivent vous alerter avant de cliquer.', 'astuce', 210],
        ];

        $adminId = User::where('role', 'administrateur')->value('id');
        $ordreParFormation = [];

        foreach ($medias as [$categorie, $titre, $description, $type, $duree]) {
            $formation = Formation::where('titre', $categorie)->firstOrFail();
            $ordre = $ordreParFormation[$formation->id] = ($ordreParFormation[$formation->id] ?? 0) + 1;

            Media::updateOrCreate(
                ['titre' => $titre],
                [
                    'description' => $description,
                    'type' => 'video',
                    'categorie' => $type,
                    'url' => null,
                    'video_platform' => 'server',
                    'video_file_path' => 'videos/seed/'.Str::slug($titre).'.mp4',
                    'subtitle_language' => 'fr',
                    'mime' => 'video/mp4',
                    'duree' => $duree,
                    'ordre' => $ordre,
                    'formation_id' => $formation->id,
                    'uploaded_by' => $adminId,
                ]
            );
        }

        // Visionnages : chaque stagiaire a regardé une vidéo, en totalité ou partiellement
        $mediaList = Media::orderBy('id')->get();
        foreach (Stagiaire::orderBy('id')->take(10)->get() as $i => $stagiaire) {
            $media = $mediaList[$i % $mediaList->count()];
            $termine = $i % 2 === 0;
            $position = $termine ? $media->duree : intdiv($media->duree, 2);

            $stagiaire->medias()->syncWithoutDetaching([
                $media->id => [
                    'is_watched' => $termine,
                    'watched_at' => $termine ? now()->subDays(10 - $i) : null,
                    'current_time' => $position,
                    'duration' => $media->duree,
                    'percentage' => round($position / $media->duree * 100, 2),
                ],
            ]);
        }
    }
}
