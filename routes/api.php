<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\JWTAuthController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Stagiaire\FormationStagiaireController;
use App\Http\Controllers\Admin\FormateurController;
use App\Http\Controllers\Stagiaire\QuizStagiaireController;
use App\Http\Controllers\Stagiaire\ContactController;
use App\Http\Controllers\Stagiaire\RankingController;
use App\Http\Controllers\Stagiaire\ParrainageController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Stagiaire\FormationController;
use App\Http\Controllers\Stagiaire\ProfileController;
use App\Http\Controllers\Admin\ReponseController;
use App\Http\Controllers\Stagiaire\CatalogueFormationController;
use App\Http\Controllers\Stagiaire\MediaController;
use App\Http\Controllers\BroadcastingController;
use App\Http\Controllers\Stagiaire\StagiaireController;
use App\Http\Controllers\Stagiaire\InscriptionCatalogueFormationController;
use App\Events\TestNotification;
use App\Http\Controllers\DailyNotificationController;
use App\Http\Controllers\Api\DailyFormationNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

Route::post('login', [JWTAuthController::class, 'login']);
Route::prefix('parrainage')->group(function () {
    Route::get('/get-data/{token}', [ParrainageController::class, 'getParrainData']);
    Route::post('/register-filleul', [ParrainageController::class, 'registerFilleul']);
});
Route::get('formationParrainage', [CatalogueFormationController::class, 'getAllCatalogueFormations']);

// Demande de réinitialisation
Route::post('/forgot-password', [JWTAuthController::class, 'sendResetLink']);
Route::post('/reset-password', [JWTAuthController::class, 'resetPassword']);
// Cette route est déjà en dehors du groupe Route::middleware(['auth:api']), donc elle est publique.
Route::middleware(['auth:api'])->group(function () {
    // Succès et récompenses stagiaire
    Route::get('/stagiaire/achievements', [App\Http\Controllers\Stagiaire\AchievementController::class, 'getAchievements']);
    Route::post('/stagiaire/achievements/check', [App\Http\Controllers\Stagiaire\AchievementController::class, 'checkAchievements']);
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::get('user', [JWTAuthController::class, 'getUser']);
    Route::get('me', [JWTAuthController::class, 'getMe']);
    Route::get('/formation/categories/', [FormationStagiaireController::class, 'getCategories']);
    Route::get('/formations/categories/{categoryId}', [FormationStagiaireController::class, 'getFormationsByCategory']);
    Route::get('/stagiaire/formations', [FormationStagiaireController::class, 'getFormations']);
    Route::get('/stagiaire/{id}/formations', [FormationController::class, 'getFormationsByStagiaire']);
    Route::get('/stagiaire/{id}/catalogueFormations', [StagiaireController::class, 'getFormationsByStagiaire']);
    Route::get('/formations/{stagiaireId}/quizzes', [QuizStagiaireController::class, 'getQuizzesByStagiaire']);
    Route::get('/quiz/categories', [QuizController::class, 'getCategories']);
    Route::get('/quiz/{quizId}/questions', [QuizStagiaireController::class, 'getQuestionsByQuizId']);
    Route::get('/stagiaire/quizzes', [QuizStagiaireController::class, 'getStagiaireQuizzes']);
    // Contacts routes
    Route::get('/stagiaire/contacts', [ContactController::class, 'getContacts']);
    Route::get('/stagiaire/contacts/formateurs', [ContactController::class, 'getFormateurs']);
    Route::get('/stagiaire/contacts/commerciaux', [ContactController::class, 'getCommerciaux']);
    Route::get('/stagiaire/contacts/pole-relation', [ContactController::class, 'getPoleRelation']);

    // Ranking and rewards routes
    Route::get('/stagiaire/ranking/global', [RankingController::class, 'getGlobalRanking']);
    Route::get('/stagiaire/ranking/formation/{formationId}', [RankingController::class, 'getFormationRanking']);
    Route::get('/stagiaire/rewards', [RankingController::class, 'getMyRewards']);
    Route::get('/stagiaire/progress', [RankingController::class, 'getMyProgress']);

    // Parrainage routes
    Route::post('/parrainage/generate-link', [ParrainageController::class, 'generateLink']);

    // Routes pour les stagiaires
    Route::prefix('stagiaire')->group(function () {
        // Route::get('/formations', [FormationStagiaireController::class, 'getFormations']);
        Route::get('/show', [ProfileController::class, 'show']);
        Route::post('/profile/photo', [ProfileController::class, 'uploadAvatar']);
    });
    Route::post('/stagiaire/inscription-catalogue-formation', [InscriptionCatalogueFormationController::class, 'inscrire']);
    // New route for getting responses to a question
    Route::get('/questions/{questionId}/reponses', [ReponseController::class, 'getReponsesByQuestion']);

    // New route for submitting a quiz
    Route::post('/quizzes/{quizId}/submit', [QuizStagiaireController::class, 'submitQuiz']);

    // Routes de parrainage
    Route::prefix('stagiaire/parrainage')->group(function () {
        Route::get('stats', [ParrainageController::class, 'getStatsParrain']);
        Route::get('filleuls', [ParrainageController::class, 'getFilleuls']);
        Route::post('accept', [ParrainageController::class, 'acceptParrainage']);
        Route::get('rewards', [ParrainageController::class, 'getParrainageRewards']);
        Route::get('history', [ParrainageController::class, 'getParrainageHistory']);
    });

    // Routes de gestion des catalogue de formation
    Route::prefix('catalogueFormations')->group(function () {
        Route::get('formations', [CatalogueFormationController::class, 'getAllCatalogueFormations']);
        Route::get('with-formations', [CatalogueFormationController::class, 'getCataloguesWithFormations']);
        Route::get('stagiaire', [CatalogueFormationController::class, 'getFormationsAndCatalogues']);
        Route::get('/formations/{id}', [CatalogueFormationController::class, 'getCatalogueFormationById']);
        Route::get('/formations/{id}/pdf', [CatalogueFormationController::class, 'getCataloguePdf']);
    });

    Route::prefix('formation')->group(function () {
        Route::get('listFormation', [FormationController::class, 'getAllFormations']);
    });
    // Quiz routes
    Route::prefix('quiz')->group(function () {
        // Routes de base
        Route::get('/categories', [QuizController::class, 'getCategories']);
        Route::get('/category/{categoryId}', [QuizController::class, 'getQuizzesByCategory']);
        Route::get('/{quizId}/questions', [QuizStagiaireController::class, 'getQuestionsByQuizId']);
        Route::post('/{quizId}/submit', [QuizStagiaireController::class, 'submitQuiz']);

        // Routes de participation
        Route::get('/{quizId}/participation', [QuizController::class, 'getCurrentParticipation']);
        Route::post('/{quizId}/participation', [QuizController::class, 'startParticipation']);
        Route::post('/{quizId}/complete', [QuizController::class, 'completeParticipation']);

        // Routes de statistiques
        Route::get('/history', [QuizController::class, 'getQuizHistory']);
        Route::get('/stats', [QuizController::class, 'getQuizStats']);
        Route::get('/{quizId}/statistics', [QuizController::class, 'getQuizStatistics']);
        Route::get('/classement/global', [QuizController::class, 'getGlobalClassement']);
        Route::get('/{quizId}/user-participations', [QuizController::class, 'getUserParticipations']);

        // Nouvelles routes pour les statistiques détaillées
        Route::get('/stats/categories', [QuizController::class, 'getGlobalCategoryStats']);
        Route::get('/stats/progress', [QuizController::class, 'getProgressStats']);
        Route::get('/stats/trends', [QuizController::class, 'getQuizTrends']);
        Route::get('/stats/performance', [QuizController::class, 'getPerformanceStats']);
    });
    // Routes pour les tutoriels et astuce
    Route::prefix('medias')->group(function () {
        Route::get('tutoriels', [MediaController::class, 'getTutoriels']);
        Route::get('astuces', [MediaController::class, 'getAstuces']);
        Route::get('formations/{formationId}/tutoriels', [MediaController::class, 'getTutorielsByFormation']);
        Route::get('formations/{formationId}/astuces', [MediaController::class, 'getAstucesByFormation']);
        Route::get('formations/interactives', [MediaController::class, 'getInteractiveFormations']);
    });


    // Questions routes
    Route::prefix('questions')->group(function () {
        Route::get('/{questionId}/reponses', [ReponseController::class, 'getReponsesByQuestion']);
        Route::get('questionById/{id}', [ReponseController::class, 'getQuestionById']);
    });
    // Quiz routes
    Route::get('/quiz/category/{category}', [QuizController::class, 'getQuizzesByCategory']);
    Route::get('/quiz/{id}', [QuizController::class, 'getQuizById']);
    Route::post('/quiz/{id}/result', [QuizController::class, 'submitQuizResult']);
    Route::get('/quiz/{id}/participation', [QuizController::class, 'getCurrentParticipation']);
    Route::post('/quiz/{id}/complete', [QuizController::class, 'completeParticipation']);
    Route::get('/quiz-participations/{participation}/resume', [App\Http\Controllers\QuizController::class, 'getParticipationResume']);
    Route::post('/avatar/{id}/update-profile', [FormationStagiaireController::class, 'updateImage']);

    Route::get('/parrainage/stats/{parrain_id}', [ParrainageController::class, 'getStatsParrain']);
    Route::get('/user-status', [ProfileController::class, 'onlineUsers'])->middleware('auth:api');
    Route::get('/test-notif', function () {
        $data = [
            'title' => 'Nouvelle notification',
            'message' => 'Une notification vient d’être envoyée !',
        ];

        event(new TestNotification($data));

        return 'Notification envoyée !';
    });
    Route::get('/send-daily-notification', [DailyNotificationController::class, 'send']);
    Route::middleware('auth:api')->post('/notify-daily-formation', [DailyFormationNotificationController::class, 'notify']);
});

// // Admin Achievement Management
// Route::prefix('admin/achievements')->middleware(['auth:api', 'admin'])->group(function () {
//     Route::get('/', [\App\Http\Controllers\Admin\AdminAchievementController::class, 'index']);
//     Route::post('/', [\App\Http\Controllers\Admin\AdminAchievementController::class, 'store']);
//     Route::put('/{id}', [\App\Http\Controllers\Admin\AdminAchievementController::class, 'update']);
//     Route::delete('/{id}', [\App\Http\Controllers\Admin\AdminAchievementController::class, 'destroy']);
//     Route::post('/reset', [\App\Http\Controllers\Admin\AdminAchievementController::class, 'resetAchievements']);
//     Route::get('/statistics', [\App\Http\Controllers\Admin\AdminAchievementController::class, 'statistics']);
// });

Route::get('/media/stream/{path}', [MediaController::class, 'stream'])
    ->withoutMiddleware([JwtMiddleware::class])
    ->middleware('throttle:60,1')
    ->where('path', '.*');

// Routes d'authentification
Route::post('refresh-token', [App\Http\Controllers\Auth\AuthController::class, 'refresh']);

// Broadcasting Authentication
Route::post('/broadcasting/auth', [BroadcastingController::class, 'auth'])
    ->middleware(['auth:api']);

// Routes de parrainage sans connection

// Routes pour les notifications
Route::middleware(['auth:api'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [App\Http\Controllers\Api\NotificationController::class, 'delete']);
    Route::get('/notifications/unread-count', [App\Http\Controllers\Api\NotificationController::class, 'getUnreadCount']);
});


// Route pour enregistrer le token FCM
Route::middleware(['auth:api'])->post('/fcm-token', [App\Http\Controllers\FcmTokenController::class, 'store']);

// Route pour envoyer une notification (Pusher + FCM)
Route::middleware(['auth:api'])->post('/send-notification', [App\Http\Controllers\NotificationAPIController::class, 'send']);


Route::post('/pusher/auth', function (Request $request) {
    return Broadcast::auth($request);
});


Route::get('/test-fcm', function () {
    $user = \App\Models\User::whereNotNull('fcm_token')->first();
    return app(\App\Services\NotificationService::class)->sendFcmToUser(
        $user,
        'Test FCM',
        'Ceci est un test FCM via route',
        ['type' => 'test']
    ) ? 'OK' : 'Erreur';
});

Route::middleware('auth:api')->post('/stagiaire/onboarding-seen', [StagiaireController::class, 'setOnboardingSeen']);

Route::middleware('auth:api')->group(function () {
    Route::get('avatars', [\App\Http\Controllers\Api\AvatarController::class, 'index']);
    Route::get('my-avatars', [\App\Http\Controllers\Api\AvatarController::class, 'myAvatars']);
    Route::post('avatars/{id}/unlock', [\App\Http\Controllers\Api\AvatarController::class, 'unlock']);
});
