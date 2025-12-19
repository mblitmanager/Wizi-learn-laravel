<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\Stagiaire\AchievementController as StagiaireAchievementController;
use App\Http\Controllers\Api\StagiaireProfileController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\Api\Commercial\CommercialStatisticsController;
use App\Http\Controllers\Api\UserSettingsController;
use App\Http\Controllers\Stagiaire\FormationStagiaireController;
use App\Http\Controllers\Stagiaire\FormationController;
use App\Http\Controllers\Stagiaire\QuizStagiaireController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Stagiaire\ContactController;
use App\Http\Controllers\Stagiaire\RankingController;
use App\Http\Controllers\Stagiaire\CatalogueFormationController;
use App\Http\Controllers\Stagiaire\StagiaireController;
use App\Http\Controllers\Stagiaire\InscriptionCatalogueFormationController;
use App\Http\Controllers\Api\Admin\UserClientStatsController;

// Authentication routes
Route::post('login', [JWTAuthController::class, 'login']);
Route::post('refresh', [JWTAuthController::class, 'refresh']);
Route::post('/forgot-password', [JWTAuthController::class, 'sendResetLink']);
Route::post('/reset-password', [JWTAuthController::class, 'resetPassword']);

// Authenticated routes
Route::middleware(['auth:api', 'detectClient'])->group(function () {
    // Stagiaire routes
    Route::get('/stagiaire/achievements', [StagiaireAchievementController::class, 'getAchievements']);
    Route::post('/stagiaire/achievements/check', [StagiaireAchievementController::class, 'checkAchievements']);
    Route::get('/stagiaire/profile', [StagiaireProfileController::class, 'getProfile']);
    Route::put('/stagiaire/profile', [StagiaireProfileController::class, 'updateProfile']);
    Route::patch('/stagiaire/profile', [StagiaireProfileController::class, 'updateProfile']);

    // Formateur routes
    Route::prefix('formateur')->middleware('auth:api')->group(function () {
        Route::get('/dashboard/stats', [FormateurController::class, 'getDashboardStats']);
        Route::get('/stagiaires', [FormateurController::class, 'getStagiaires']);
        Route::get('/stagiaires/online', [FormateurController::class, 'getOnlineStagiaires']);
        Route::post('/stagiaires/disconnect', [FormateurController::class, 'disconnectStagiaires']);
        Route::get('/stagiaires/inactive', [FormateurController::class, 'getInactiveStagiaires']);
        Route::get('/stagiaires/never-connected', [FormateurController::class, 'getNeverConnected']);
        Route::get('/stagiaires/performance', [FormateurController::class, 'getStudentsPerformance']);
        Route::get('/stagiaire/{id}/stats', [FormateurController::class, 'getStagiaireStats']);
        Route::post('/send-notification', [FormateurController::class, 'sendNotification']);
        Route::post('/send-email', [FormateurController::class, 'sendEmail']);
        Route::get('/classement/formation/{formationId}', [FormateurController::class, 'getFormationRanking']);
        Route::get('/classement/mes-stagiaires', [FormateurController::class, 'getMesStagiairesRanking']);
        Route::get('/videos', [FormateurController::class, 'getAllVideos']);
        Route::get('/video/{id}/stats', [FormateurController::class, 'getVideoStats']);
        Route::get('/formations', [FormateurController::class, 'getFormations']);
    });

    // Commercial routes
    Route::middleware(['role:commercial'])->group(function () {
        Route::get('/commercial/stats/dashboard', [CommercialStatisticsController::class, 'dashboard']);
    });

    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/stats/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/admin/stats/quiz', [AdminController::class, 'quizStats']);
        Route::get('/admin/stats/formation', [AdminController::class, 'formationStats']);
        Route::get('/admin/stats/online-users', [AdminController::class, 'onlineUsers']);
        Route::get('/admin/stats/affluence', [AdminController::class, 'affluence']);
        Route::post('/admin/stats/export/pdf', [AdminController::class, 'exportPdf']);
        Route::post('/admin/stats/export/excel', [AdminController::class, 'exportExcel']);
        Route::get('/admin/achievements', [AdminAchievementController::class, 'apiIndex']);
        Route::get('/admin/user-client-stats', [UserClientStatsController::class, 'index']);
    });

    // User settings (optional, kept for completeness)
    Route::get('/user/settings', [UserSettingsController::class, 'show']);
    Route::put('/user/settings', [UserSettingsController::class, 'update']);

    // Formation & quiz routes (core to stagiaire/formateur)
    Route::get('/formation/categories/', [FormationStagiaireController::class, 'getCategories']);
    Route::get('/formations/categories/{categoryId}', [FormationStagiaireController::class, 'getFormationsByCategory']);
    Route::get('/stagiaire/formations', [FormationStagiaireController::class, 'getFormations']);
    Route::get('/stagiaire/{id}/formations', [FormationController::class, 'getFormationsByStagiaire']);
    Route::get('/stagiaire/{id}/catalogueFormations', [StagiaireController::class, 'getFormationsByStagiaire']);
    Route::get('/formations/{stagiaireId}/quizzes', [QuizStagiaireController::class, 'getQuizzesByStagiaire']);
    Route::get('/quiz/categories', [QuizController::class, 'getCategories']);
    Route::get('/quiz/{quizId}/questions', [QuizStagiaireController::class, 'getQuestionsByQuizId']);
    Route::get('/stagiaire/quizzes', [QuizStagiaireController::class, 'getStagiaireQuizzes']);

    // Contact routes (core for stagiaire)
    Route::get('/stagiaire/contacts', [ContactController::class, 'getContacts']);
    Route::get('/stagiaire/contacts/formateurs', [ContactController::class, 'getFormateurs']);
    Route::get('/stagiaire/contacts/commerciaux', [ContactController::class, 'getCommerciaux']);
    Route::get('/stagiaire/contacts/pole-relation', [ContactController::class, 'getPoleRelation']);
    Route::get('/stagiaire/contacts/pole-save', [ContactController::class, 'getPoleSav']);
    Route::post('/user/photo', [StagiaireController::class, 'updateProfilePhoto']);

    // Ranking & rewards (core)
    Route::get('/stagiaire/ranking/global', [RankingController::class, 'getGlobalRanking']);
    Route::get('/stagiaire/ranking/formation/{formationId}', [RankingController::class, 'getFormationRanking']);
    Route::get('/stagiaire/rewards', [RankingController::class, 'getMyRewards']);
    Route::get('/stagiaire/progress', [RankingController::class, 'getMyProgress']);
    Route::get('/stagiaires/{stagiaireId}/details', [RankingController::class, 'getStagiaireDetails']);
    Route::get('/users/me/points', [RankingController::class, 'getUserPoints']);

    // Formation ranking routes (core)
    Route::get('/formations/{formationId}/classement', [App\Http\Controllers\FormationClassementController::class, 'getClassement']);
    Route::get('/stagiaire/formations/{formationId}/classement', [App\Http\Controllers\FormationClassementController::class, 'getMyRanking']);
    Route::get('/formations/classement/summary', [App\Http\Controllers\FormationClassementController::class, 'getFormationsWithTopRanking']);

    // Classements
    Route::get('/quiz/classement/global', [App\Http\Controllers\Api\QuizController::class, 'getGlobalRanking']);
    Route::get('/stagiaire/ranking/formation/{id}', [App\Http\Controllers\Api\RankingController::class, 'getFormationRanking']);

    // Parrainage Events
    Route::get('/parrainage-events', [App\Http\Controllers\Api\ParrainageController::class, 'getEvents']);

    // Logout & User Info
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
});

?>
