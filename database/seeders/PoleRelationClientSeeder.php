<?php

namespace Database\Seeders;

use App\Models\PoleRelationClient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PoleRelationClientSeeder extends Seeder
{
    public function run(): void
    {
        $membres = [
            ['Aurélie', 'Rousseau'],
            ['Sébastien', 'Vincent'],
            ['Laura', 'Muller'],
            ['Damien', 'Lefebvre'],
            ['Mélanie', 'Fournier'],
            ['Florian', 'Andre'],
            ['Justine', 'Mathieu'],
            ['Quentin', 'Gauthier'],
            ['Marion', 'Dumont'],
            ['Adrien', 'Lopez'],
        ];

        foreach ($membres as $i => [$prenom, $nom]) {
            $user = User::updateOrCreate(
                ['email' => 'prc'.($i + 1).'@wizi-learn.com'],
                [
                    'name' => $nom,
                    'password' => Hash::make('password'),
                    'role' => 'pole_relation_client',
                    'email_verified_at' => now(),
                ]
            );

            PoleRelationClient::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role' => 'pole_relation_client',
                    'prenom' => $prenom,
                    'telephone' => '01'.str_pad((string) (44556600 + $i), 8, '0', STR_PAD_LEFT),
                ]
            );
        }
    }
}
