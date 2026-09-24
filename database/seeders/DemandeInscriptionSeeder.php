<?php

namespace Database\Seeders;

use App\Models\CatalogueFormation;
use App\Models\DemandeInscription;
use App\Models\Parrainage;
use Illuminate\Database\Seeder;

/**
 * 10 demandes d'inscription, une par parrainage.
 */
class DemandeInscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $motifs = [
            'Évolution professionnelle',
            'Reconversion',
            'Recherche d\'emploi',
            'Obtenir une certification',
            'Développement personnel',
        ];
        $statuts = ['en_attente', 'complete', 'en_attente', 'complete', 'refuse'];

        $catalogue = CatalogueFormation::orderBy('id')->get();
        $parrainages = Parrainage::with('filleul')->orderBy('id')->take(10)->get();

        foreach ($parrainages as $i => $parrainage) {
            $formation = $catalogue[($i + 3) % $catalogue->count()];

            DemandeInscription::updateOrCreate(
                ['parrain_id' => $parrainage->parrain_id, 'filleul_id' => $parrainage->filleul_id],
                [
                    'formation_id' => $formation->id,
                    'statut' => $statuts[$i % count($statuts)],
                    'motif' => $motifs[$i % count($motifs)],
                    'lien_parrainage' => null,
                    'donnees_formulaire' => [
                        'nom' => $parrainage->filleul?->name,
                        'email' => $parrainage->filleul?->email,
                        'formation' => $formation->titre,
                    ],
                    'date_demande' => $parrainage->date_parrainage,
                    'date_inscription' => $parrainage->date_parrainage,
                ]
            );
        }
    }
}
