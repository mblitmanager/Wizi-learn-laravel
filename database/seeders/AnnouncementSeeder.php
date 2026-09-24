<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('role', 'administrateur')->value('id');
        if (! $adminId) {
            return;
        }

        // [titre, message, public ciblé, statut, décalage en jours]
        $annonces = [
            ['Bienvenue sur Wizi Learn', 'Découvrez vos quiz, vidéos et votre classement depuis votre tableau de bord.', 'all', 'sent', -30],
            ['Nouvelle formation IA Générative', 'La formation certifiante IA Générative (RS 6776) est désormais disponible au catalogue.', 'stagiaires', 'sent', -20],
            ['Maintenance planifiée', 'La plateforme sera indisponible dimanche de 2h à 4h pour maintenance.', 'all', 'sent', -15],
            ['Nouveaux quiz Bureautique', 'Des quiz Word, Excel et PowerPoint ont été ajoutés : testez-vous !', 'stagiaires', 'sent', -10],
            ['Réunion pédagogique', 'Réunion des formateurs jeudi à 14h pour préparer les sessions du trimestre.', 'formateurs', 'sent', -7],
            ['Parrainage de la rentrée', 'Parrainez un proche et gagnez 50 € par inscription validée.', 'stagiaires', 'sent', -5],
            ['Mise à jour de l\'application mobile', 'Une nouvelle version de l\'application Android est disponible.', 'all', 'scheduled', 3],
            ['Sessions de certification', 'Les prochaines sessions TOSA et TOEIC sont ouvertes à la réservation.', 'stagiaires', 'scheduled', 7],
            ['Point commercial mensuel', 'Bilan des inscriptions et objectifs du mois.', 'autres', 'draft', 10],
            ['Enquête de satisfaction', 'Donnez votre avis sur votre formation en 2 minutes.', 'stagiaires', 'draft', 14],
        ];

        foreach ($annonces as [$titre, $message, $public, $statut, $jours]) {
            $date = now()->addDays($jours)->setTime(9, 0);

            Announcement::updateOrCreate(
                ['title' => $titre],
                [
                    'message' => $message,
                    'target_audience' => $public,
                    'recipient_ids' => null,
                    'status' => $statut,
                    'scheduled_at' => $statut === 'draft' ? null : $date,
                    'sent_at' => $statut === 'sent' ? $date : null,
                    'created_by' => $adminId,
                ]
            );
        }
    }
}
