<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CatalogueFormationController;
use App\Http\Controllers\Admin\CommercialController;
use App\Http\Controllers\Admin\FormateurController;
use App\Http\Controllers\Admin\FormationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ParametreAdminController;
use App\Http\Controllers\Admin\ParrainageController;
use App\Http\Controllers\Admin\PoleRelationClientController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\StagiaireController;
use App\Http\Controllers\Stagiaire\DashboardController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

Route::get('/', function () {
    return view('stagiaire');
});
Route::get('/administrateur', function () {
    return redirect()->route('dashboard');
});
Route::get('/admin', function () {
    return redirect()->route('dashboard');
});

// Demande de réinitialisation
Route::get('/forgot-password', [AdminController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AdminController::class, 'sendResetLink'])->name('password.email');

// Réinitialisation effective
Route::get('/reset-password/{token}', [AdminController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AdminController::class, 'resetPassword'])->name('password.update');

Route::prefix('administrateur')->group(function () {
    Route::get('/register', [AdminController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AdminController::class, 'register'])->name('register.post');

    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
});

Route::middleware(['auth', 'isAdmin'])->prefix('administrateur')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('dashboard/activity-user', [AdminController::class, 'getUserActivity'])->name('dashboard.activity-user');

    Route::get('dashboard/activity', [AdminController::class, 'showLoginStats'])->name('dashboard.activity');


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
    Route::get('catalogue_formation/{id}/download-pdf', [CatalogueFormationController::class, 'downloadPdf'])->name('catalogue_formation.download-pdf');


    // Route pour le manuel interactif admin
    Route::get('/manual', function() {
        return view('admin.manual');
    })->name('admin.manual');

    // Routes pour la réinitialisation des données
    Route::get('/parametre/reset-data', [ParametreAdminController::class, 'showResetData'])->name('admin.parametre.reset-data');
    Route::post('/parametre/reset-data', [ParametreAdminController::class, 'resetData'])->name('admin.parametre.reset-data.post');

    Route::resource('parametre', ParametreAdminController::class);
    Route::put('/parametre/{id}/update-image', [ParametreAdminController::class, 'updateImage'])->name('parametre.updateImage');

    Route::post('/import/prc', [PoleRelationClientController::class, 'import'])->name('prc.import');
    Route::post('/quiz-question/new', [QuizController::class, 'storeNewQuestion'])->name('quiz_question.new');
    Route::get('/telecharger-modele-stagiaire', [StagiaireController::class, 'downloadStagiaireModel'])->name('download.stagiaire.model');
    Route::get('/telecharger-modele-commercial', [CommercialController::class, 'downloadCommercialModel'])->name('download.commercial.model');
    Route::get('/telecharger-modele-prc', [PoleRelationClientController::class, 'downloadPrcModel'])->name('download.prc.model');
    Route::get('/telecharger-modele-formateur', [FormateurController::class, 'downloadFormateurModel'])->name('download.formateur.model');
    Route::get('/telecharger-modele-quiz', [QuizController::class, 'downloadQuizModel'])->name('download.quiz.model');
    Route::get('parrainage', [ParrainageController::class, 'index'])->name('parrainage.index');
    Route::get('parrainage/{id}', [ParrainageController::class, 'show'])->name('parrainage.show');

    // Gestion des succès (achievements) en back office
    Route::get('/achievements', [\App\Http\Controllers\Admin\AchievementController::class, 'index'])->name('admin.achievements.index');
    Route::get('/achievements/create', [\App\Http\Controllers\Admin\AchievementController::class, 'create'])->name('admin.achievements.create');
    Route::post('/achievements', [\App\Http\Controllers\Admin\AchievementController::class, 'store'])->name('admin.achievements.store');
    Route::get('/achievements/{achievement}/edit', [\App\Http\Controllers\Admin\AchievementController::class, 'edit'])->name('admin.achievements.edit');
    Route::put('/achievements/{achievement}', [\App\Http\Controllers\Admin\AchievementController::class, 'update'])->name('admin.achievements.update');
    Route::delete('/achievements/{achievement}', [\App\Http\Controllers\Admin\AchievementController::class, 'destroy'])->name('admin.achievements.destroy');
    Route::post('/achievements/reset', [\App\Http\Controllers\Admin\AchievementController::class, 'apiResetAchievements'])->name('admin.achievements.reset');
    // Web views for statistics and trends
    Route::get('/achievements/statistics', [\App\Http\Controllers\Admin\AchievementController::class, 'statistics'])->name('admin.achievements.statistics');
    Route::get('/achievements/trends', [\App\Http\Controllers\Admin\AchievementController::class, 'trends'])->name('admin.achievements.trends');


    // Vue de gestion des paramètres (ajout lien vers achievements)
    // Route::get('/parametre/achievements', [\App\Http\Controllers\Admin\ParametreAdminController::class, 'achievements'])->name('admin.parametre.achievements');
});

// // Route pour enregistrer le token FCM
// Route::middleware(['auth'])->post('/fcm-token', [FcmTokenController::class, 'store']);

// Route pour envoyer une notification (Pusher + FCM)
// Route::middleware(['auth'])->post('/send-notification', [NotificationController::class, 'send']);

Route::fallback(function () {
    return redirect('/');
});

// Catch-all route for React Router (SPA)
Route::get('/{any}', function () {
    return view('stagiaire'); // Assurez-vous que la vue correspond à votre build React
})->where('any', '.*');


