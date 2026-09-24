<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * L'ordre compte : chaque seeder s'appuie sur les données des précédents.
     * Tous les comptes créés ont le mot de passe "password".
     */
    public function run(): void
    {
        $this->call([
            // Référentiels
            PermissionSeeder::class,
            UserSeeder::class,
            FormationSeeder::class,
            CatalogueFormationSeeder::class,
            PartenaireSeeder::class,
            AchievementSeeder::class,
            AndroidDownloadAchievementSeeder::class,
            VideoAchievementsSeeder::class,

            // Utilisateurs et profils
            FormateurSeeder::class,
            CommercialSeeder::class,
            PoleRelationClientSeeder::class,
            StagiaireSeeder::class,

            // Contenus pédagogiques
            QuizSeeder::class,
            MediaSeeder::class,

            // Activité
            ParticipationSeeder::class,
            ClassementSeeder::class,
            ProgressionSeeder::class,
            ChallengeSeeder::class,
            AgendaSeeder::class,
            LoginHistorySeeder::class,

            // Parrainage
            ParrainageEventSeeder::class,
            ParrainageSeeder::class,
            DemandeInscriptionSeeder::class,

            // Communication
            NotificationSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
