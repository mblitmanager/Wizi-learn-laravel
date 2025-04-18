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

    Route::prefix('catalogue_formations')->group(function () {
        Route::get('formations', [CatalogueFormationController::class, 'getAllCatalogueFormations']);
        Route::get('stagiaire/{id}', [CatalogueFormationController::class, 'getFormationsAndCatalogues']);
        Route::get('{id}', [CatalogueFormationController::class, 'getCatalogueFormationById']);
    });
});

// Routes d'authentification
Route::post('refresh-token', [App\Http\Controllers\Auth\AuthController::class, 'refresh']);
