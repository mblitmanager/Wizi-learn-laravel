<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;

class VideoAchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Succès : première vidéo vue
        Achievement::updateOrCreate(
            [ 'code' => 'first_video' ],
            [
                'name' => 'Première vidéo',
                'description' => 'A regardé sa première vidéo',
                'type' => 'video',
                'condition' => 1,
                'level' => null,
                'quiz_id' => null,
                'code' => 'first_video',
            ]
        );

        // Succès : toutes les vidéos vues
        Achievement::updateOrCreate(
            [ 'code' => 'all_videos' ],
            [
                'name' => 'Toutes les vidéos',
                'description' => 'A regardé toutes les vidéos de la plateforme',
                'type' => 'video',
                'condition' => 0,
                'level' => null,
                'quiz_id' => null,
                'code' => 'all_videos',
            ]
        );
    }
}
