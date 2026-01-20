<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

// Controllers
use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\BroadcastingController;
use App\Http\Controllers\DailyNotificationController;
use App\Http\Controllers\Api\DailyFormationNotificationController;
use App\Http\Controllers\Api\StagiaireProfileController;
use App\Http\Controllers\Api\UserSettingsController;
use App\Http\Controllers\Api\ParrainageEventApiController;
use App\Http\Controllers\Api\OnlineUsersController;
use App\Http\Controllers\Api\NotificationHistoryController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AutoReminderController;
use App\Http\Controllers\Api\UserAppUsageController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\PushNotificationController;
use App\Http\Controllers\Api\ContactController as ApiContactController;
use App\Http\Controllers\Api\TestFcmController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\NotificationAPIController;
use App\Http\Controllers\Api\NotificationController as UserNotificationController;
use App\Http\Controllers\Api\CalendarSyncController;

// Role Controllers
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Api\Commercial\CommercialStatisticsController;
// use App\Http\Controllers\Api\Admin\UserClientStatsController as AdminUserClientStatsController;
use App\Http\Controllers\Api\Admin\StatisticsController as AdminStatisticsController;
use App\Http\Controllers\Api\Formateur\FormateurStatisticsController;
use App\Http\Controllers\Formateur\FormateurStagiaireController;
use App\Http\Controllers\Formateur\FormateurFormationController;
use App\Http\Controllers\Formateur\FormateurAnalyticsController;
use App\Http\Controllers\Formateur\FormateurAlertsController;
use App\Http\Controllers\Formateur\FormateurQuizController;

// Stagiaire Controllers
use App\Http\Controllers\Stagiaire\AchievementController as StagiaireAchievementController;
use App\Http\Controllers\Stagiaire\FormationStagiaireController;
use App\Http\Controllers\Stagiaire\FormationController;
use App\Http\Controllers\Stagiaire\QuizStagiaireController;
use App\Http\Controllers\Stagiaire\ContactController;
use App\Http\Controllers\Stagiaire\RankingController;
use App\Http\Controllers\Stagiaire\CatalogueFormationController;
use App\Http\Controllers\Stagiaire\StagiaireController;
use App\Http\Controllers\Stagiaire\InscriptionCatalogueFormationController;
use App\Http\Controllers\Stagiaire\ParrainageController;
use App\Http\Controllers\Stagiaire\PartnerController;
use App\Http\Controllers\Stagiaire\MediaController;
use App\Http\Controllers\Stagiaire\ProfileController;

// Admin/Shared Controllers
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\Admin\ReponseController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\FormationClassementController;
// REMOVED INVALID ALIASES
// use App\Http\Controllers\Api\AuthController; // This was already replaced by JWTAuthController usage, can trigger warning if logic uses it but I'll stick to cleaning imports

use App\Http\Middleware\JwtMiddleware;
use App\Events\TestNotification;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ==============================================================================
// PUBLIC ROUTES
// ==============================================================================

// Authentication
Route::post('login', [JWTAuthController::class, 'login']);
Route::post('refresh', [JWTAuthController::class, 'refresh']);
Route::post('refresh-token', [App\Http\Controllers\Auth\AuthController::class, 'refresh']);

// Password Reset
Route::post('/forgot-password', [JWTAuthController::class, 'sendResetLink']);
Route::post('/reset-password', [JWTAuthController::class, 'resetPassword']);

// Parrainage (Public)
Route::prefix('parrainage')->group(function () {
    Route::get('/get-data/{token}', [ParrainageController::class, 'getParrainData']);
    Route::post('/register-filleul', [ParrainageController::class, 'registerFilleul']);
});
Route::get('formationParrainage', [CatalogueFormationController::class, 'getAllCatalogueFormations']);

// Media Streams (No JWT required, throttled)
Route::get('/media/stream/{path}', [MediaController::class, 'stream'])
    ->withoutMiddleware([JwtMiddleware::class])
    ->middleware('throttle:60,1')
    ->where('path', '.*');

Route::get('/media/subtitle/{path}', [MediaController::class, 'streamSubtitle'])
    ->withoutMiddleware([JwtMiddleware::class])
    ->middleware('throttle:60,1')
    ->where('path', '.*');

// Pusher Auth (Public endpoint wrapping Broadcast::auth)
Route::post('/pusher/auth', function (Request $request) {
    return Broadcast::auth($request);
});

// Test Endpoint (Development)
Route::get('/test-fcm', function () {
    $user = \App\Models\User::whereNotNull('fcm_token')->first();
    if (!$user) return 'No user with FCM token found';
    return app(\App\Services\NotificationService::class)->sendFcmToUser(
        $user,
        'Test FCM',
        'Ceci est un test FCM via route',
        ['type' => 'test']
    ) ? 'OK' : 'Erreur';
});
Route::post('/test-fcm', [TestFcmController::class, 'send']);
Route::get('/test-notif', function () {
    $data = ['title' => 'Nouvelle notification', 'message' => 'Une notification vient d’être envoyée !'];
    event(new TestNotification($data));
    return 'Notification envoyée !';
});


// ==============================================================================
// AUTHENTICATED ROUTES
// ==============================================================================

Route::middleware(['auth:api', 'detectClient'])->group(function () {

    // Google Calendar Sync
    Route::post('/calendar/sync', [CalendarSyncController::class, 'sync']);

    // --------------------------------------------------------------------------
    // USER & CORE
    // --------------------------------------------------------------------------
    Route::post('/logout', [JWTAuthController::class, 'logout']);
    Route::get('/user', [JWTAuthController::class, 'getUser']); // getUser is in JWTAuthController
    Route::get('/me', [JWTAuthController::class, 'getMe']);
    
    // User Settings
    Route::get('/user/settings', [UserSettingsController::class, 'show']);
    Route::put('/user/settings', [UserSettingsController::class, 'update']);
    
    // App Usage & Onboarding
    Route::post('/user-app-usage', [UserAppUsageController::class, 'report']);
    Route::post('/stagiaire/onboarding-seen', [StagiaireController::class, 'setOnboardingSeen']);
    
    // FCM & Notifications
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);
    Route::post('/send-notification', [NotificationAPIController::class, 'send']); // Send (Pusher+FCM)
    
    // User Notifications Management
    Route::get('/notifications', [UserNotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [UserNotificationController::class, 'markAsRead']);
    Route::post('/notifications/mark-all-read', [UserNotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [UserNotificationController::class, 'delete']);
    Route::get('/notifications/unread-count', [UserNotificationController::class, 'getUnreadCount']);

    // Broadcasting Auth (Authenticated)
    Route::post('/broadcasting/auth', [BroadcastingController::class, 'auth']);

    // --------------------------------------------------------------------------
    // STAGIAIRE (Student)
    // --------------------------------------------------------------------------
    
    // Profile
    Route::get('/stagiaire/profile', [StagiaireProfileController::class, 'getProfile']);
    Route::put('/stagiaire/profile', [StagiaireProfileController::class, 'updateProfile']);
    Route::patch('/stagiaire/profile', [StagiaireProfileController::class, 'updateProfile']);
    Route::post('/user/photo', [StagiaireController::class, 'updateProfilePhoto']);
    Route::get('/stagiaire/show', [ProfileController::class, 'show']);
    Route::post('/stagiaire/profile/photo', [ProfileController::class, 'uploadAvatar']);
    Route::get('/user-status', [ProfileController::class, 'onlineUsers']);

    // Dashboard - Consolidated Home Page Data
    Route::get('/stagiaire/dashboard/home', [App\Http\Controllers\Stagiaire\DashboardController::class, 'getHomeData']);

    // Achievements
    Route::get('/stagiaire/achievements', [StagiaireAchievementController::class, 'getAchievements']);
    Route::get('/stagiaire/achievements/all', [StagiaireAchievementController::class, 'getAllAchievements']);
    Route::post('/stagiaire/achievements/check', [StagiaireAchievementController::class, 'checkAchievements']);

    // Contacts
    Route::get('/stagiaire/contacts', [ContactController::class, 'getContacts']);
    Route::get('/stagiaire/contacts/formateurs', [ContactController::class, 'getFormateurs']);
    Route::get('/stagiaire/contacts/commerciaux', [ContactController::class, 'getCommerciaux']);
    Route::get('/stagiaire/contacts/pole-relation', [ContactController::class, 'getPoleRelation']);
    Route::get('/stagiaire/contacts/pole-save', [ContactController::class, 'getPoleSav']);
    Route::post('/contact', [ApiContactController::class, 'sendContactForm']);
    Route::get('/stagiaire/partner', [PartnerController::class, 'getMyPartner']);

    // Formations (Stagiaire View)
    Route::get('/formation/categories/', [FormationStagiaireController::class, 'getCategories']);
    Route::get('/formations/categories/{categoryId}', [FormationStagiaireController::class, 'getFormationsByCategory']);
    Route::get('/stagiaire/formations', [FormationStagiaireController::class, 'getFormations']);
    Route::get('/stagiaire/{id}/formations', [FormationController::class, 'getFormationsByStagiaire']);
    Route::get('/stagiaire/{id}/catalogueFormations', [StagiaireController::class, 'getFormationsByStagiaire']);
    Route::get('/formation/listFormation', [FormationController::class, 'getAllFormations']);

    // Catalogue
    Route::prefix('catalogueFormations')->group(function () {
        Route::get('formations', [CatalogueFormationController::class, 'getAllCatalogueFormations']);
        Route::get('with-formations', [CatalogueFormationController::class, 'getCataloguesWithFormations']);
        Route::get('stagiaire', [CatalogueFormationController::class, 'getFormationsAndCatalogues']);
        Route::get('/formations/{id}', [CatalogueFormationController::class, 'getCatalogueFormationById']);
        Route::get('/formations/{id}/pdf', [CatalogueFormationController::class, 'getCataloguePdf']);
    });
    Route::post('/stagiaire/inscription-catalogue-formation', [InscriptionCatalogueFormationController::class, 'inscrire']);

    // Quizzes (Stagiaire View)
    Route::get('/stagiaire/quizzes', [QuizStagiaireController::class, 'getStagiaireQuizzes']);
    Route::get('/formations/{stagiaireId}/quizzes', [QuizStagiaireController::class, 'getQuizzesByStagiaire']);
    Route::get('/quiz/categories', [QuizController::class, 'getCategories']);
    Route::get('/quiz/{quizId}/questions', [QuizStagiaireController::class, 'getQuestionsByQuizId']);
    Route::get('/questions/{questionId}/reponses', [ReponseController::class, 'getReponsesByQuestion']);
    
    // Quiz Actions
    Route::prefix('quiz')->group(function () {
        Route::get('/categories', [QuizController::class, 'getCategories']);
        Route::get('/category/{categoryId}', [QuizController::class, 'getQuizzesByCategory']);
        
        // Detailed Stats (Must be before /{id})
        Route::get('/stats/categories', [QuizController::class, 'getGlobalCategoryStats']);
        Route::get('/stats/progress', [QuizController::class, 'getProgressStats']);
        Route::get('/stats/trends', [QuizController::class, 'getQuizTrends']);
        Route::get('/stats/performance', [QuizController::class, 'getPerformanceStats']);
        Route::get('/by-formations', [QuizController::class, 'getQuizzesGroupedByFormation']);
        
        // Statistics (Must be before /{id})
        Route::get('/history', [QuizController::class, 'getQuizHistory']);
        Route::get('/stats', [QuizController::class, 'getQuizStats']);

        // Rankings
        Route::get('/classement/global', [QuizController::class, 'getGlobalClassement']);
        
        // Detailed methods requiring IDs but having specific subpaths can stay, but generic /{id} must handle only IDs
        // Moving generic /{id} to the BOTTOM to avoid shadowing specific routes like /history, /stats
        
        // Participation
        Route::get('/{quizId}/participation', [QuizController::class, 'getCurrentParticipation']);
        Route::get('/{quizId}/participation/resume', [QuizController::class, 'resumeParticipation']);
        Route::post('/{quizId}/participation', [QuizController::class, 'startParticipation']);
        Route::post('/{quizId}/participation/progress', [QuizController::class, 'saveProgress']);
        Route::post('/{quizId}/complete', [QuizController::class, 'completeParticipation']);
        Route::post('/{quizId}/submit', [QuizStagiaireController::class, 'submitQuiz']);
        Route::post('/{id}/result', [QuizController::class, 'submitQuizResult']); 
        
        Route::get('/{quizId}/statistics', [QuizController::class, 'getQuizStatistics']);
        Route::get('/{quizId}/user-participations', [QuizController::class, 'getUserParticipations']);
        
        // Generic ID route - MUST BE LAST
        Route::get('/{id}', [QuizController::class, 'getQuizById']);
    });
    Route::post('/quizzes/{quizId}/submit', [QuizStagiaireController::class, 'submitQuiz']); // Alias

    // Ranking & Rewards
    Route::get('/stagiaire/ranking/global', [RankingController::class, 'getGlobalRanking']);
    Route::get('/stagiaire/ranking/formation/{formationId}', [RankingController::class, 'getFormationRanking']);
    Route::get('/stagiaire/rewards', [RankingController::class, 'getMyRewards']);
    Route::get('/stagiaire/progress', [RankingController::class, 'getMyProgress']);
    Route::get('/stagiaires/{stagiaireId}/details', [RankingController::class, 'getStagiaireDetails']);
    Route::get('/users/me/points', [RankingController::class, 'getUserPoints']);
    
    // Ranking Routes (Cleaned up)
    Route::get('/formations/{formationId}/classement', [FormationClassementController::class, 'getClassement']);
    Route::get('/stagiaire/formations/{formationId}/classement', [FormationClassementController::class, 'getMyRanking']);
    Route::get('/formations/classement/summary', [FormationClassementController::class, 'getFormationsWithTopRanking']);
    
    // Parrainage
    Route::prefix('stagiaire/parrainage')->group(function () {
        Route::get('stats', [ParrainageController::class, 'getStatsParrain']);
        Route::get('filleuls', [ParrainageController::class, 'getFilleuls']);
        Route::post('accept', [ParrainageController::class, 'acceptParrainage']);
        Route::get('rewards', [ParrainageController::class, 'getParrainageRewards']);
        Route::get('history', [ParrainageController::class, 'getParrainageHistory']);
    });
    Route::post('/parrainage/generate-link', [ParrainageController::class, 'generateLink']);
    Route::get('/parrainage/stats/{parrain_id}', [ParrainageController::class, 'getStatsParrain']);
    Route::get('/parrainage-events', [ParrainageEventApiController::class, 'index']); // FIXED: Corrected controller and method

    // Medias (Tutoriels & Astuces)
    Route::prefix('medias')->group(function () {
        Route::get('tutoriels', [MediaController::class, 'getTutoriels']);
        Route::get('astuces', [MediaController::class, 'getAstuces']);
        Route::get('formations/{formationId}/tutoriels', [MediaController::class, 'getTutorielsByFormation']);
        Route::get('formations/{formationId}/astuces', [MediaController::class, 'getAstucesByFormation']);
        Route::get('formations/interactives', [MediaController::class, 'getInteractiveFormations']);
        Route::post('/{mediaId}/watched', [MediaController::class, 'markAsWatched']);
        Route::get('/formations-with-status', [MediaController::class, 'getFormationsWithWatchedStatus']);
        Route::post('/upload-video', [MediaController::class, 'uploadVideo']);
        Route::get('/server', [MediaController::class, 'listServerVideos']);
    });

    // --------------------------------------------------------------------------
    // FORMATEUR
    // --------------------------------------------------------------------------
    Route::prefix('formateur')->middleware('auth:api')->group(function () {
        Route::get('/dashboard/stats', [FormateurController::class, 'getDashboardStats']);
        Route::get('/stats/dashboard', [FormateurStatisticsController::class, 'dashboard']); // Specific stats controller
        
        Route::get('/stagiaires', [FormateurController::class, 'getStagiaires']);
        Route::get('/stagiaires/online', [FormateurController::class, 'getOnlineStagiaires']);
        Route::post('/stagiaires/disconnect', [FormateurController::class, 'disconnectStagiaires']);
        Route::get('/stagiaires/inactive', [FormateurController::class, 'getInactiveStagiaires']);
        Route::get('/stagiaires/never-connected', [FormateurController::class, 'getNeverConnected']);
        Route::get('/stagiaires/performance', [FormateurController::class, 'getStudentsPerformance']);
        Route::get('/stagiaire/{id}/stats', [FormateurController::class, 'getStagiaireStats']);
        
        // Student Profile API
        Route::get('/stagiaire/{id}/profile', [FormateurStagiaireController::class, 'getProfileApi']);
        Route::get('/stagiaire/{id}/notes', [FormateurStagiaireController::class, 'getNotesApi']);
        Route::post('/stagiaire/{id}/note', [FormateurStagiaireController::class, 'addNoteApi']);
        
        // Formation Management API
        Route::get('/formations/available', [FormateurFormationController::class, 'getAvailable']);
        Route::get('/formations/{id}/stagiaires', [FormateurFormationController::class, 'getStagiairesByFormation']);
        Route::post('/formations/{id}/assign', [FormateurFormationController::class, 'assignStagiaires']);
        Route::get('/formations/{id}/stats', [FormateurFormationController::class, 'getFormationStats']);
        Route::get('/stagiaires/unassigned/{formationId}', [FormateurFormationController::class, 'getUnassignedStagiaires']);
        Route::put('/formations/{id}/schedule', [FormateurFormationController::class, 'updateSchedule']);
        
        // Analytics API
        Route::get('/analytics/quiz-success-rate', [FormateurAnalyticsController::class, 'getQuizSuccessRate']);
        Route::get('/analytics/completion-time', [FormateurAnalyticsController::class, 'getCompletionTime']);
        Route::get('/analytics/activity-heatmap', [FormateurAnalyticsController::class, 'getActivityHeatmap']);
        Route::get('/analytics/dropout-rate', [FormateurAnalyticsController::class, 'getDropoutRate']);
        Route::get('/analytics/performance', [FormateurController::class, 'getStudentsPerformance']);
        Route::get('/analytics/formations/performance', [FormateurAnalyticsController::class, 'getFormationsPerformance']);
        Route::get('/analytics/dashboard', [FormateurAnalyticsController::class, 'getDashboard']);
        
        // Alerts API
        Route::get('/alerts', [FormateurAlertsController::class, 'getAlerts']);
        Route::get('/alerts/stats', [FormateurAlertsController::class, 'getAlertStats']);
        
        // Quiz Creator API
        Route::get('/quizzes', [FormateurQuizController::class, 'index']);
        Route::get('/quizzes/{id}', [FormateurQuizController::class, 'show']);
        Route::post('/quizzes', [FormateurQuizController::class, 'store']);
        Route::put('/quizzes/{id}', [FormateurQuizController::class, 'update']);
        Route::delete('/quizzes/{id}', [FormateurQuizController::class, 'destroy']);
        Route::post('/quizzes/{id}/questions', [FormateurQuizController::class, 'addQuestion']);
        Route::put('/quizzes/{quizId}/questions/{questionId}', [FormateurQuizController::class, 'updateQuestion']);
        Route::delete('/quizzes/{quizId}/questions/{questionId}', [FormateurQuizController::class, 'deleteQuestion']);
        Route::post('/quizzes/{id}/publish', [FormateurQuizController::class, 'publish']);
        Route::get('/formations-list', [FormateurQuizController::class, 'getFormations']);
        
        Route::post('/send-notification', [FormateurController::class, 'sendNotification']);
        Route::post('/send-email', [FormateurController::class, 'sendEmail']);
        
        Route::get('/classement/formation/{formationId}', [FormateurController::class, 'getFormationRanking']);
        Route::get('/classement/mes-stagiaires', [FormateurController::class, 'getMesStagiairesRanking']);
        
        Route::get('/videos', [FormateurController::class, 'getAllVideos']);
        Route::get('/video/{id}/stats', [FormateurController::class, 'getVideoStats']);
        Route::get('/formations', [FormateurController::class, 'getFormations']);
        Route::get('/trends', [FormateurController::class, 'getTrends']);
    });
    
    // Manually triggered notifications
    Route::get('/send-daily-notification', [DailyNotificationController::class, 'send']);
    Route::post('/notify-daily-formation', [DailyFormationNotificationController::class, 'notify']);

    // --------------------------------------------------------------------------
    // COMMERCIAL
    // --------------------------------------------------------------------------
    Route::middleware(['role:commercial'])->group(function () {
        Route::get('/commercial/stats/dashboard', [CommercialStatisticsController::class, 'dashboard']);
    });
    
    // Shared Commercial/Admin endpoints
    Route::post('/email', [EmailController::class, 'send']);
    Route::post('/notify', [PushNotificationController::class, 'send']);
    Route::get('/online-users', [OnlineUsersController::class, 'index']);
    Route::get('/notification-history', [NotificationHistoryController::class, 'index']);
    
    // Announcements
    Route::get('announcements/recipients', [AnnouncementController::class, 'getRecipients']);
    Route::apiResource('announcements', AnnouncementController::class);
    
    // Auto-reminders
    Route::get('auto-reminders/stats', [AutoReminderController::class, 'getStats']);
    Route::get('auto-reminders/history', [AutoReminderController::class, 'getHistory']);
    Route::get('auto-reminders/targeted', [AutoReminderController::class, 'getTargetedUsers']);
    Route::post('auto-reminders/run', [AutoReminderController::class, 'runManualReminders']);

    // --------------------------------------------------------------------------
    // ADMIN
    // --------------------------------------------------------------------------
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/stats/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/admin/stats/dashboard-api', [AdminStatisticsController::class, 'dashboard']); // Alias
        
        Route::get('/admin/stats/quiz', [AdminController::class, 'quizStats']);
        Route::get('/admin/stats/formation', [AdminController::class, 'formationStats']);
        Route::get('/admin/stats/online-users', [AdminController::class, 'onlineUsers']);
        Route::get('/admin/stats/affluence', [AdminController::class, 'affluence']);
        
        Route::post('/admin/stats/export/pdf', [AdminController::class, 'exportPdf']);
        Route::post('/admin/stats/export/excel', [AdminController::class, 'exportExcel']);
        
        Route::get('/admin/achievements', [AdminAchievementController::class, 'apiIndex']);
        // Route::get('/admin/user-client-stats', [UserClientStatsController::class, 'index']);
        
        // Admin StatisticsController aliases
        Route::get('/admin/stats/quiz-api', [AdminStatisticsController::class, 'quizStats']);
        Route::get('/admin/stats/formation-api', [AdminStatisticsController::class, 'formationStats']);
    });
});
