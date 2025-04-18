<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CatalogueFormationController;
use App\Http\Controllers\Admin\CommercialController;
use App\Http\Controllers\Admin\FormateurController;
use App\Http\Controllers\Admin\FormationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\Admin\PoleRelationClientController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\StagiaireController;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

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

    Route::post('/import/stagiaires', [StagiaireController::class, 'import'])->name('stagiaires.import');
    Route::post('/import/commercials', [CommercialController::class, 'import'])->name('commercials.import');
    Route::post('/import/formateur', [FormateurController::class, 'import'])->name('formateur.import');
    Route::post('/import/quiz', [QuizController::class, 'import'])->name('quiz.import');

    Route::resource('question', QuestionController::class);

    Route::resource('catalogue_formation', CatalogueFormationController::class);

    Route::get('parametre', [ParametreController::class, 'index'])->name('parametre.index');
});
