<?php

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

Route::post('login', [JWTAuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::get('user', [JWTAuthController::class, 'getUser']);
    Route::get('me', [JWTAuthController::class, 'getMe']);
    Route::get('/formation/categories/', [FormationStagiaireController::class, 'getCategories']);
    Route::get('/formations/categories/{categoryId}', [FormationStagiaireController::class, 'getFormationsByCategory']);
    Route::get('/stagiaire/formations', [FormationStagiaireController::class, 'getFormations']);
    Route::get('/stagiaire/{id}/formations', [FormationController::class, 'getFormationsByStagiaireId']);
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
    Route::get('/stagiaire/parrainage/link', [ParrainageController::class, 'getParrainageLink']);
    Route::post('/stagiaire/parrainage/generate-link', [ParrainageController::class, 'generateParrainageLink']);
    Route::get('/stagiaire/parrainage/filleuls', [ParrainageController::class, 'getFilleuls']);
    Route::get('/stagiaire/parrainage/stats', [ParrainageController::class, 'getParrainageStats']);

    // Routes pour les stagiaires
    Route::prefix('stagiaire')->group(function () {
        Route::get('/formations', [FormationStagiaireController::class, 'getFormations']);
        Route::get('/show', [ProfileController::class, 'show']);
        Route::post('/profile/photo', [ProfileController::class, 'uploadAvatar']);
    });

    // New route for getting responses to a question
    Route::get('/questions/{questionId}/reponses', [ReponseController::class, 'getReponsesByQuestion']);

    // New route for submitting a quiz
    Route::post('/quizzes/{quizId}/submit', [QuizStagiaireController::class, 'submitQuiz']);

    // Routes de parrainage
    Route::prefix('stagiaire/parrainage')->group(function () {
        Route::get('stats', [ParrainageController::class, 'getParrainageStats']);
        Route::get('filleuls', [ParrainageController::class, 'getFilleuls']);
        Route::post('accept', [ParrainageController::class, 'acceptParrainage']);
        Route::get('rewards', [ParrainageController::class, 'getParrainageRewards']);
        Route::get('history', [ParrainageController::class, 'getParrainageHistory']);
    });

    // Routes de gestion des catalogue de formation
    Route::prefix('catalogueFormations')->group(function () {
        Route::get('formations', [CatalogueFormationController::class, 'getAllCatalogueFormations']);
        Route::get('stagiaire/{id}', [CatalogueFormationController::class, 'getFormationsAndCatalogues']);
        Route::get('/formations/{id}', [CatalogueFormationController::class, 'getCatalogueFormationById']);
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
});


Route::get('/media/stream/{path}', [MediaController::class, 'stream'])
    ->withoutMiddleware([JwtMiddleware::class])
    ->middleware('throttle:60,1')
    ->where('path', '.*');

// Routes d'authentification
Route::post('refresh-token', [App\Http\Controllers\Auth\AuthController::class, 'refresh']);
