<?php

namespace Database\Seeders;

use App\Models\Commercial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CommercialSeeder extends Seeder
{
    public function run(): void
    {
        $commerciaux = [
            ['M.', 'Romain', 'Bernard'],
            ['Mme.', 'Chloé', 'Petit'],
            ['M.', 'Kevin', 'Robert'],
            ['Mme.', 'Manon', 'Richard'],
            ['M.', 'Alexandre', 'Durand'],
            ['Mme.', 'Inès', 'Mercier'],
            ['M.', 'Yanis', 'Blanc'],
            ['Mme.', 'Pauline', 'Guerin'],
            ['M.', 'Mathieu', 'Boyer'],
            ['Mlle.', 'Océane', 'Garnier'],
        ];

        foreach ($commerciaux as $i => [$civilite, $prenom, $nom]) {
            $user = User::updateOrCreate(
                ['email' => 'commercial'.($i + 1).'@wizi-learn.com'],
                [
                    'name' => $nom,
                    'password' => Hash::make('password'),
                    'role' => 'commercial',
                    'email_verified_at' => now(),
                ]
            );

            Commercial::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role' => 'commercial',
                    'civilite' => $civilite,
                    'prenom' => $prenom,
                    'telephone' => '06'.str_pad((string) (31427800 + $i), 8, '0', STR_PAD_LEFT),
                ]
            );
        }
    }
}
