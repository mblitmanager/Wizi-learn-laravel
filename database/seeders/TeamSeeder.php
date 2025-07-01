<?php

namespace Database\Seeders;

use App\Models\Commercial;
use App\Models\Formateur;
use App\Models\PoleRelationClient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Commercial::factory()->count(5)->create();

        // Créer 3 formateurs
        Formateur::factory()->count(3)->create();

        // Créer 4 membres du pôle relation client
        PoleRelationClient::factory()->count(4)->create();
    }
}
