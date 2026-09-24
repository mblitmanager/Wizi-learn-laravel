# Déploiement des notifications sur OVH (hébergement mutualisé)

Ce document explique comment déployer et configurer la partie notifications (FCM + scheduler) de l'application Laravel sur un hébergement OVH mutualisé.

## Aperçu

- Le service envoie des notifications via Firebase Cloud Messaging (FCM) en utilisant un fichier de compte de service JSON.
- La logique d'envoi se trouve dans `app/Services/NotificationService.php` et la commande planifiée principale est `notify:scheduled` (définie dans `app/Console/Commands/SendScheduledNotifications.php`).
- Sur un hébergement mutualisé OVH, il faut :
  - disposer du fichier de compte de service Firebase sur le serveur,
  - définir les variables d'environnement nécessaires dans `.env`,
  - déclencher le scheduler Laravel via une tâche CRON configurée dans l'espace client OVH,
  - (optionnel) gérer les queues si vous voulez décharger l'envoi des notifications.

## Prérequis

- PHP version >= 8.2 (vérifier la version CLI disponible sur votre offre OVH)
- Extensions PHP : curl, openssl, json, mbstring, fileinfo
- Accès FTP/SSH (SSH n'est pas toujours disponible sur toutes les offres mutualisées) — si vous n'avez pas SSH, vous devrez téléverser manuellement les fichiers `vendor/` (voir plus bas).
- Compte Firebase avec fichier de compte de service JSON (service account)

## Étapes détaillées

1) Préparer l'application localement

   - Exécuter en local :

     composer install --no-dev --optimize-autoloader
     npm install && npm run prod

   - Tester la commande localement :

     php artisan notify:scheduled

2) Préparer le fichier de compte de service Firebase

   - Téléchargez le JSON depuis la console Firebase (Project settings -> Service accounts -> Generate new private key).
   - Renommez-le en `firebase-service-account.json`.
   - Placez-le dans `storage/app/firebase-service-account.json` sur votre serveur OVH (chemin attendu par `NotificationService`).
   - Assurez-vous que le fichier est lisible par PHP mais non accessible publiquement (ne le mettez pas dans `public/`).

   Option alternative : si vous préférez stocker le JSON ailleurs, vous pouvez modifier `NotificationService::sendFcmToUser()` pour lire le chemin depuis `.env` (ex: `env('FIREBASE_SERVICE_ACCOUNT_PATH')`).

3) Configurer les variables d'environnement

   Dans le fichier `.env` sur le serveur, ajoutez/validez :

   FIREBASE_PROJECT_ID=your-firebase-project-id
   APP_ENV=production
   APP_DEBUG=false

   - `FIREBASE_PROJECT_ID` est nécessaire pour la construction de l'URL FCM. Si manquant, la méthode essaie de l'extraire du fichier JSON.

4) Déployer le code et les dépendances

   - Si vous avez accès SSH/composer sur le serveur :
     - `composer install --no-dev --optimize-autoloader`
     - `php artisan migrate --force`
     - `php artisan storage:link` (si nécessaire)

   - Si vous n'avez pas accès SSH/composer (typique en mutualisé) :
     - Exécutez `composer install --no-dev` localement sur votre machine.
     - Téléversez l'ensemble du projet + dossier `vendor/` sur le serveur via FTP (ou via git + déploiement automatisé si disponible).
     - Exécutez `php artisan migrate --force` via SSH si disponible ; sinon appliquez vos migrations localement et importez la base de données sur le serveur.

5) Tâches CRON (Scheduler Laravel)

   Laravel recommande d'exécuter `php artisan schedule:run` toutes les minutes ; le scheduler interne se chargera d'exécuter la commande planifiée à 08:00

   - Dans l'espace client OVH (Hébergement > CRON) : créez une tâche CRON qui exécute la commande CLI PHP. Exemple de commande CRON :

     /usr/bin/php /home/username/www/your-domain.fr/artisan schedule:run >> /home/username/logs/cron-schedule.log 2>&1

   - Remarques OVH :
     - Remplacez `/usr/bin/php` par le chemin exact du binaire PHP CLI fourni par OVH (vérifiez la version PHP souhaitée, ex `/usr/local/bin/php74`).
     - `home/username/www/your-domain.fr` correspond au chemin racine de votre hébergement.
     - Configurez la CRON pour s'exécuter toutes les minutes.

6) Optionnel : queues et workers

   - `NotificationService` envoie actuellement les notifications de façon synchrone via Guzzle. Si vous voulez décharger l'envoi :
     - Activez la queue (ex : driver `database` dans `.env`).
     - Modifiez `NotificationService` pour dispatcher un Job qui envoie la FCM.
     - Sur un hébergement mutualisé, vous ne pouvez pas lancer un worker long (`queue:work`) en continu. Deux options :
       - Créer une tâche CRON qui exécute `php artisan queue:work --once --tries=3` toutes les minutes.
       - Utiliser `queue:retry` et `queue:failed` en combinaison avec une CRON.

     Exemple concret d'intégration (Job + CRON OVH)

     1) Configuration `.env`

        - Choisissez `database` comme driver de queue :

          QUEUE_CONNECTION=database

        - Veillez à avoir les paramètres DB correctement configurés (la table `jobs` sera utilisée).

     2) Migration pour la table jobs

        - Si vous n'avez pas encore la migration, créez-la :

          php artisan queue:table
          php artisan migrate

     3) Créer un Job pour l'envoi FCM

        - Exemple de job (fichier `app/Jobs/SendFcmNotificationJob.php`) :

          <?php
          namespace App\Jobs;

          use Illuminate\Bus\Queueable;
          use Illuminate\Contracts\Queue\ShouldQueue;
          use Illuminate\Foundation\Bus\Dispatchable;
          use Illuminate\Queue\InteractsWithQueue;
          use Illuminate\Queue\SerializesModels;
          use App\Services\NotificationService;

          class SendFcmNotificationJob implements ShouldQueue
          {
              use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

              public $user;
              public $title;
              public $body;
              public $data;

              public function __construct($user, $title, $body, $data = [])
              {
                  $this->user = $user;
                  $this->title = $title;
                  $this->body = $body;
                  $this->data = $data;
              }

              public function handle(NotificationService $notificationService)
              {
                  $notificationService->sendFcmToUser($this->user, $this->title, $this->body, $this->data);
              }
          }

     4) Dispatcher le Job depuis `NotificationService`

        - À la place d'appeler `sendFcmToUser()` directement, dispatch le job :

          use App\Jobs\SendFcmNotificationJob;

          // dans NotificationService
          SendFcmNotificationJob::dispatch($user, $title, $body, $data)->onQueue('notifications');

     5) CRON OVH pour exécuter un worker ONE-SHOT

        - Dans l'interface OVH > Hébergement > CRON, créez une tâche récurrente toutes les minutes :

          /usr/local/bin/php74 /home/username/www/your-domain.fr/artisan queue:work --once --queue=notifications --sleep=3 --tries=3 >> /home/username/logs/queue-worker.log 2>&1

        - Explication des options :
          - `--once` : exécute un seul cycle de travail puis s'arrête (convient au mutualisé)
          - `--queue=notifications` : traite uniquement la file `notifications`
          - `--sleep=3` : si la file est vide, attend 3 secondes avant de terminer
          - `--tries=3` : réessaye 3 fois en cas d'échec

     6) Monitoring et logs

        - Vérifiez `storage/logs/laravel.log` et `logs/queue-worker.log` (ou le chemin que vous avez choisi) pour suivre les erreurs.
        - Gérer les jobs échoués :

          php artisan queue:failed-table
          php artisan migrate

        - Pour relancer les jobs échoués (manuellement ou via une autre CRON) :

          php artisan queue:retry all

     7) Notes pratiques

        - Sur OVH mutualisé il est normal de lancer `queue:work --once` via CRON. Le traitement peut être plus lent qu'un worker persistant mais reste fiable pour des charges légères / moyennes.
        - Si vous avez des volumes élevés de notifications, envisagez une solution d'hébergement dédiée (VPS) ou un service géré (Laravel Forge, Vapor, etc.) pour worker en continu.


7) Permissions et sécurité

   - Assurez-vous que `storage/` et `bootstrap/cache` sont inscriptibles par PHP.
   - Le fichier `storage/app/firebase-service-account.json` doit être lisible par PHP et non accessible depuis le web.

8) Tests et vérification

   - Tester la commande manuellement via SSH (si disponible) :

     php /home/username/www/your-domain.fr/artisan notify:scheduled

   - Vérifier les logs : `storage/logs/laravel.log` et le fichier de log CRON si vous en avez configuré un (`cron-schedule.log`).
   - Vérifier que les enregistrements `notifications` sont créés en base.

## Dépannage (erreurs fréquentes)

- Erreur « Service account file missing » : vérifiez que `storage/app/firebase-service-account.json` existe et que le chemin utilisé dans `NotificationService` est correct.
- Erreur « Project ID missing » : ajoutez `FIREBASE_PROJECT_ID` dans `.env` ou incluez `project_id` dans le JSON du service account.
- Problèmes d'envoi HTTP (Guzzle) : vérifiez que l'hébergement autorise les sorties HTTPS vers `fcm.googleapis.com` et que l'extension `curl` est activée.
- Problèmes de permissions : vérifiez que `storage/` est inscriptible et que le fichier JSON n'est pas lisible publiquement.

## Exemple rapide — Checklist

- [ ] Composer install (ou upload vendor)
- [ ] Uploader `storage/app/firebase-service-account.json`
- [ ] Définir `FIREBASE_PROJECT_ID` dans `.env`
- [ ] Configurer la CRON OVH pour `artisan schedule:run` toutes les minutes
- [ ] Vérifier `storage/logs/laravel.log` après exécution

## Annexes (exemples de commandes)

- Cron command (exemple OVH) :

  /usr/local/bin/php74 /home/username/www/your-domain.fr/artisan schedule:run >> /home/username/logs/cron-schedule.log 2>&1

- Test en ligne de commande :

  php /home/username/www/your-domain.fr/artisan notify:scheduled

## Questions fréquentes

- Peut-on exécuter `queue:work` en continu sur OVH mutualisé ?
  Non, OVH mutualisé n'autorise généralement pas de processus PHP long-running. Utilisez plutôt des tâches CRON qui exécutent `queue:work --once` ou laissez la logique synchrone.

- Où stocker le fichier Firebase si `storage/` n'est pas convenable ?
  Vous pouvez le placer hors `public/` dans un dossier protégé (ex : un dossier à côté de `vendor/`) et adapter le code pour pointer vers ce chemin ; évitez toujours `public/`.

Si vous voulez, je peux :

- Générer une version adaptée de `NotificationService` qui lit le chemin du fichier de compte de service depuis `.env` ;
- Ajouter un exemple d'intégration de queue + CRON `queue:work --once` pour OVH ;
- Ajouter un script d'aide (bash / powershell) pour préparer et téléverser le package vers OVH.

---
Fichier créé le: Oct 2025
