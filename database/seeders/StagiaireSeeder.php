<?php

namespace Database\Seeders;

use App\Models\CatalogueFormation;
use App\Models\Commercial;
use App\Models\Partenaire;
use App\Models\PoleRelationClient;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StagiaireSeeder extends Seeder
{
    public function run(): void
    {
        // [civilité, prénom, nom, date de naissance, adresse, ville, code postal]
        $stagiaires = [
            ['Mme', 'Julie', 'Martin', '1992-03-14', '8 rue Victor Hugo', 'Lyon', '69002'],
            ['M.', 'Lucas', 'Bernard', '1988-07-22', '14 boulevard Guist\'hau', 'Nantes', '44000'],
            ['Mme', 'Sarah', 'Dubois', '1995-11-05', '3 cours de l\'Intendance', 'Bordeaux', '33000'],
            ['M.', 'Thomas', 'Leroy', '1985-01-30', '27 rue de Rivoli', 'Paris', '75004'],
            ['Mme', 'Amina', 'Diallo', '1999-06-18', '41 La Canebière', 'Marseille', '13001'],
            ['M.', 'Pierre', 'Moreau', '1979-09-09', '6 rue d\'Alsace-Lorraine', 'Toulouse', '31000'],
            ['Mme', 'Clara', 'Simon', '1993-12-02', '19 rue Saint-Malo', 'Rennes', '35000'],
            ['M.', 'Mehdi', 'Benali', '1990-04-27', '55 rue Faidherbe', 'Lille', '59000'],
            ['Mme', 'Laura', 'Klein', '1987-08-11', '2 rue des Grandes Arcades', 'Strasbourg', '67000'],
            ['M.', 'Enzo', 'Rossi', '2001-02-16', '10 rue de France', 'Nice', '06000'],
        ];

        $catalogue = CatalogueFormation::with('formateurs')->orderBy('id')->get();
        $partenaires = Partenaire::orderBy('id')->get();
        $commerciaux = Commercial::orderBy('id')->get();
        $prcs = PoleRelationClient::orderBy('id')->get();

        foreach ($stagiaires as $i => [$civilite, $prenom, $nom, $naissance, $adresse, $ville, $cp]) {
            $user = User::updateOrCreate(
                ['email' => 'stagiaire'.($i + 1).'@wizi-learn.com'],
                [
                    'name' => $nom,
                    'password' => Hash::make('password'),
                    'role' => 'stagiaire',
                    'adresse' => "{$adresse}, {$cp} {$ville}",
                    'email_verified_at' => now(),
                ]
            );

            $debut = now()->subDays(60 - $i * 5)->startOfDay();
            $partenaire = $partenaires->isNotEmpty() ? $partenaires[$i % $partenaires->count()] : null;

            $stagiaire = Stagiaire::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'civilite' => $civilite,
                    'prenom' => $prenom,
                    'telephone' => '06'.str_pad((string) (50607080 + $i), 8, '0', STR_PAD_LEFT),
                    'adresse' => $adresse,
                    'date_naissance' => $naissance,
                    'ville' => $ville,
                    'code_postal' => $cp,
                    'date_inscription' => $debut->copy()->subDays(14),
                    'date_debut_formation' => $debut,
                    'date_fin_formation' => $debut->copy()->addMonths(6),
                    'role' => 'stagiaire',
                    'statut' => true,
                    'onboarding_seen' => $i % 3 !== 0,
                    'partenaire_id' => $partenaire?->id,
                ]
            );

            // Inscription à une formation du catalogue, avec un formateur qui l'anime
            $formation = $catalogue[$i % $catalogue->count()];
            $formateur = $formation->formateurs->first();

            $stagiaire->catalogue_formations()->syncWithoutDetaching([
                $formation->id => [
                    'date_inscription' => $debut->copy()->subDays(14),
                    'date_debut' => $debut,
                    'date_fin' => $debut->copy()->addMonths(6),
                    'formateur_id' => $formateur?->id,
                ],
            ]);

            if ($formateur) {
                $stagiaire->formateurs()->syncWithoutDetaching([$formateur->id]);
            }
            if ($commerciaux->isNotEmpty()) {
                $stagiaire->commercials()->syncWithoutDetaching([$commerciaux[$i % $commerciaux->count()]->id]);
            }
            if ($prcs->isNotEmpty()) {
                $stagiaire->poleRelationClients()->syncWithoutDetaching([$prcs[$i % $prcs->count()]->id]);
            }
            if ($partenaire) {
                $partenaire->stagiaires()->syncWithoutDetaching([$stagiaire->id]);
            }
        }
    }
}
