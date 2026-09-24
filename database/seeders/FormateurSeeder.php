<?php

namespace Database\Seeders;

use App\Models\CatalogueFormation;
use App\Models\Formateur;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FormateurSeeder extends Seeder
{
    public function run(): void
    {
        // [civilité, prénom, nom, formations du catalogue animées (mots-clés du titre)]
        $formateurs = [
            ['M.', 'Antoine', 'Marchand', ['IA Générative', 'DigComp']],
            ['Mme', 'Isabelle', 'Fontaine', ['Word -', 'Excel']],
            ['M.', 'Nicolas', 'Roussel', ['Excel', 'PowerPoint']],
            ['Mme', 'Camille', 'Girard', ['PowerPoint', 'Word -']],
            ['M.', 'Hugo', 'Lambert', ['WordPress', 'DigComp']],
            ['Mme', 'Léa', 'Chevalier', ['Photoshop', 'Illustrator']],
            ['M.', 'Maxime', 'Perrin', ['Illustrator', 'Photoshop']],
            ['Mme', 'Emma', 'Walker', ['Anglais']],
            ['Mme', 'Sarah', 'Morel', ['Français']],
            ['M.', 'Julien', 'Faure', ['IA Générative', 'WordPress']],
        ];

        foreach ($formateurs as $i => [$civilite, $prenom, $nom, $cours]) {
            $user = User::updateOrCreate(
                ['email' => 'formateur'.($i + 1).'@wizi-learn.com'],
                [
                    'name' => $nom,
                    'password' => Hash::make('password'),
                    'role' => 'formateur',
                    'email_verified_at' => now(),
                ]
            );

            $formateur = Formateur::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role' => 'formateur',
                    'civilite' => $civilite,
                    'prenom' => $prenom,
                    'telephone' => '07'.str_pad((string) (21436500 + $i), 8, '0', STR_PAD_LEFT),
                ]
            );

            $catalogueIds = CatalogueFormation::where(function ($query) use ($cours) {
                foreach ($cours as $motCle) {
                    $query->orWhere('titre', 'like', "{$motCle}%");
                }
            })->pluck('id');

            $formateur->catalogue_formations()->syncWithoutDetaching($catalogueIds);
        }
    }
}
