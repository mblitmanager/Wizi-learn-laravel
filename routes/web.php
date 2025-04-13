<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CommercialController;
use App\Http\Controllers\Admin\FormateurController;
use App\Http\Controllers\Admin\FormationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PoleRelationClientController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\StagiaireController;
use Illuminate\Support\Facades\Route;




Route::get('/register', [AdminController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AdminController::class, 'register'])->name('register.post');

Route::get('/', [AdminController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminController::class, 'login'])->name('login.post');

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::resource('stagiaires', StagiaireController::class);
    Route::patch('/stagiaires/{id}/desactive', [StagiaireController::class, 'desactive'])->name('stagiaires.desactive');
    Route::patch('/stagiaires/{id}/active', [StagiaireController::class, 'active'])->name('stagiaires.active');

    Route::resource('quiz', QuizController::class);

    Route::resource('formateur', FormateurController::class);

    Route::resource('commercials', CommercialController::class);


    Route::resource('formations', FormationController::class);

    Route::resource('medias', MediaController::class);

    Route::resource('pole_relation_clients', PoleRelationClientController::class);

    Route::post('/quiz/create-all', [QuizController::class, 'storeAll'])->name('quiz.storeAll');

});
