<?php

use App\Http\Controllers\JWTAuthController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Stagiaire\FormationStagiaireController;
use App\Http\Controllers\Admin\FormateurController;




Route::post('login', [JWTAuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [JWTAuthController::class, 'logout']);
    Route::get('user', [JWTAuthController::class, 'getUser']);
    Route::get('/formation/categories/', [FormationStagiaireController::class, 'getCategories']);
    Route::get('/formations/categories/{categoryId}', [FormationStagiaireController::class, 'getFormationsByCategory']);
    Route::get('/stagiaire/formations', [FormationStagiaireController::class, 'getMyFormations']);
});
