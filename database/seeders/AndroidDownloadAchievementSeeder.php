<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class AndroidDownloadAchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Achievement::updateOrCreate(
            [ 'code' => 'android_download' ],
            [
                'name' => 'Téléchargement de l\'application Android',
                'description' => 'A téléchargé l\'application Android depuis l\'accueil',
                'type' => 'action',
                'condition' => 1,
                'level' => null,
                'quiz_id' => null,
                'code' => 'android_download',
            ]
        );
    }
}
