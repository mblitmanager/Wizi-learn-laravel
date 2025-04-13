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




Route::post('login', [JWTAuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::get('user', [JWTAuthController::class, 'getUser']);
    Route::get('/formation/categories/', [FormationStagiaireController::class, 'getCategories']);
    Route::get('/formations/categories/{categoryId}', [FormationStagiaireController::class, 'getFormationsByCategory']);
    Route::get('/stagiaire/formations', [FormationStagiaireController::class, 'getMyFormations']);
    Route::get('/formations/{stagiaireId}/quizzes', [QuizStagiaireController::class, 'getQuizzesByStagiaire']);
    Route::get('/quiz/categories', [QuizController::class, 'getCategories']);
    
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
    Route::get('/stagiaire/parrainage/filleuls', [ParrainageController::class, 'getFilleuls']);
    Route::get('/stagiaire/parrainage/stats', [ParrainageController::class, 'getParrainageStats']);
});
