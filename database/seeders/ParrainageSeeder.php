<?php

namespace Database\Seeders;

use App\Models\Parrainage;
use App\Models\Stagiaire;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 10 parrainages : chaque stagiaire parraine un filleul. Comme dans
 * ParrainageController, le filleul est un utilisateur "stagiaire" avec un
 * profil stagiaire pas encore actif.
 */
class ParrainageSeeder extends Seeder
{
    public function run(): void
    {
        $filleuls = [
            ['Mme', 'Nora', 'Haddad', 'Villeurbanne', '69100'],
            ['M.', 'Hugo', 'Perez', 'Saint-Herblain', '44800'],
            ['Mme', 'Alice', 'Masson', 'Mérignac', '33700'],
            ['M.', 'Samuel', 'Caron', 'Montreuil', '93100'],
            ['Mme', 'Yasmine', 'Brahimi', 'Aubagne', '13400'],
            ['M.', 'Louis', 'Picard', 'Blagnac', '31700'],
            ['Mme', 'Zoé', 'Renaud', 'Cesson-Sévigné', '35510'],
            ['M.', 'Rayan', 'Mansour', 'Roubaix', '59100'],
            ['Mme', 'Lina', 'Schmitt', 'Schiltigheim', '67300'],
            ['M.', 'Noah', 'Ferrari', 'Antibes', '06600'],
        ];

        // Parrains = stagiaires créés par StagiaireSeeder
        $parrains = User::where('email', 'like', 'stagiaire%@wizi-learn.com')->orderBy('id')->take(10)->get();

        foreach ($filleuls as $i => [$civilite, $prenom, $nom, $ville, $cp]) {
            $parrain = $parrains[$i] ?? null;
            if (! $parrain) {
                break;
            }

            $date = now()->subDays(10 - $i);

            $user = User::updateOrCreate(
                ['email' => 'filleul'.($i + 1).'@wizi-learn.com'],
                [
                    'name' => $nom,
                    'password' => Hash::make('password'),
                    'role' => 'stagiaire',
                ]
            );

            Stagiaire::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'civilite' => $civilite,
                    'prenom' => $prenom,
                    'telephone' => '07'.str_pad((string) (80901020 + $i), 8, '0', STR_PAD_LEFT),
                    'ville' => $ville,
                    'code_postal' => $cp,
                    'date_inscription' => $date,
                    'role' => 'stagiaire',
                    'statut' => false,
                ]
            );

            Parrainage::updateOrCreate(
                ['parrain_id' => $parrain->id, 'filleul_id' => $user->id],
                [
                    'date_parrainage' => $date,
                    'points' => 2,
                    'gains' => 50.00,
                ]
            );
        }
    }
}
