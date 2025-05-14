<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CatalogueFormationController;
use App\Http\Controllers\Admin\CommercialController;
use App\Http\Controllers\Admin\FormateurController;
use App\Http\Controllers\Admin\FormationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ParametreAdminController;
use App\Http\Controllers\Admin\PoleRelationClientController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\StagiaireController;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

Route::get('/', function () {
    return view('stagiaire');
});
Route::prefix('administrateur')->group(function () {
    Route::get('/register', [AdminController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AdminController::class, 'register'])->name('register.post');

    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
});

Route::middleware(['auth', 'isAdmin'])->prefix('administrateur')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::resource('stagiaires', StagiaireController::class);
    Route::patch('/stagiaires/{id}/desactive', [StagiaireController::class, 'desactive'])->name('stagiaires.desactive');
    Route::patch('/stagiaires/{id}/active', [StagiaireController::class, 'active'])->name('stagiaires.active');

    Route::resource('quiz', QuizController::class);
    Route::post('/quiz/{id}/duplicate', [QuizController::class, 'duplicate'])->name('quiz.duplicate');
    Route::post('/quiz/{quiz}/disable', [QuizController::class, 'disable'])->name('quiz.disable');
    Route::post('/quiz/{quiz}/enable', [QuizController::class, 'enable'])->name('quiz.enable');
    Route::get('/quiz/{quiz}/export', [QuizController::class, 'exportQuiz'])->name('quiz.export');
    Route::post('/quiz/export-multiple', [QuizController::class, 'exportMultipleQuizzes'])->name('quiz.exportMultiple');

    Route::resource('formateur', FormateurController::class);

    Route::resource('commercials', CommercialController::class);

    Route::resource('formations', FormationController::class);
    Route::post('/formations/{id}/duplicate', [FormationController::class, 'duplicate'])->name('formations.duplicate');

    Route::resource('medias', MediaController::class);

    Route::resource('pole_relation_clients', PoleRelationClientController::class);

    Route::post('/quiz/create-all', [QuizController::class, 'storeAll'])->name('quiz.storeAll');
    Route::post('/quiz/question-import', [QuizController::class, 'importQuestionReponseForQuiz'])->name('quiz_question.import');

    Route::post('/import/stagiaires', [StagiaireController::class, 'import'])->name('stagiaires.import');
    Route::post('/import/commercials', [CommercialController::class, 'import'])->name('commercials.import');
    Route::post('/import/formateur', [FormateurController::class, 'import'])->name('formateur.import');
    Route::post('/import/quiz', [QuizController::class, 'import'])->name('quiz.import');

    Route::resource('question', QuestionController::class);

    Route::resource('catalogue_formation', CatalogueFormationController::class);
    Route::post('/catalogue_formation/{id}/duplicate', [CatalogueFormationController::class, 'duplicate'])->name('catalogue_formation.duplicate');

    Route::resource('parametre', ParametreAdminController::class);
    Route::put('/parametre/{id}/update-image', [ParametreAdminController::class, 'updateImage'])->name('parametre.updateImage');
    Route::post('/import/prc', [PoleRelationClientController::class, 'import'])->name('prc.import');
    Route::post('/quiz-question/new', [QuizController::class, 'storeNewQuestion'])->name('quiz_question.new');
});
