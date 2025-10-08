<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CatalogueFormationController;
use App\Http\Controllers\Admin\CommercialController;
use App\Http\Controllers\Admin\DemandeHistoriqueController;
use App\Http\Controllers\Admin\FormateurController;
use App\Http\Controllers\Admin\FormationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ParametreAdminController;
use App\Http\Controllers\Admin\ParrainageController;
use App\Http\Controllers\Admin\PoleRelationClientController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\PartenaireController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\StagiaireController;
use App\Http\Controllers\Stagiaire\DashboardController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\Formateur\FormateurDashboardController;
use App\Http\Controllers\Formateur\FormateurStagiaireController;
use App\Http\Controllers\Formateur\FormateurClassementController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

Route::get('/', function () {
    return redirect('/administrateur/login');
});

Route::get('/administrateur', function () {
    return redirect()->route('dashboard');
});

Route::get('/admin', function () {
    return redirect()->route('dashboard');
});

// Routes d'authentification publiques
Route::prefix('administrateur')->group(function () {
    Route::get('/register', [AdminController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AdminController::class, 'register'])->name('register.post');

    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
});

// Routes de réinitialisation de mot de passe
Route::get('/forgot-password', [AdminController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AdminController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AdminController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AdminController::class, 'resetPassword'])->name('password.update');

// Route dashboard principale qui redirige selon le rôle (PROTÉGÉE)
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    if ($user->role === 'administrateur') {
        return app('App\Http\Controllers\Admin\AdminController')->index();
    } elseif ($user->role === 'formateur') {
        return redirect()->route('formateur.dashboard');
    } else {
        return redirect('/');
    }
})->name('dashboard');

Route::middleware(['auth'])->get('/logout', [AdminController::class, 'logout'])->name('logout');


// Routes pour les administrateurs
Route::middleware(['auth', 'isAdmin'])->prefix('administrateur')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('dashboard/activity-user', [AdminController::class, 'getUserActivity'])->name('dashboard.activity-user');
    Route::get('dashboard/activity', [AdminController::class, 'showLoginStats'])->name('dashboard.activity');

    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::post('roles/{role}/toggle-status', [\App\Http\Controllers\Admin\RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    
    // Permissions
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    Route::post('permissions/{permission}/toggle-status', [\App\Http\Controllers\Admin\PermissionController::class, 'toggleStatus'])->name('permissions.toggle-status');
    /**
     * Route Stagiaire
     */
    Route::resource('stagiaires', StagiaireController::class);
    Route::patch('/stagiaires/{id}/desactive', [StagiaireController::class, 'desactive'])->name('stagiaires.desactive');
    Route::patch('/stagiaires/{id}/active', [StagiaireController::class, 'active'])->name('stagiaires.active');
    Route::post('/import/stagiaires', [StagiaireController::class, 'import'])->name('stagiaires.import');
    Route::get('/telecharger-modele-stagiaire', [StagiaireController::class, 'downloadStagiaireModel'])->name('download.stagiaire.model');

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

    Route::post('/import/commercials', [CommercialController::class, 'import'])->name('commercials.import');
    Route::post('/import/formateur', [FormateurController::class, 'import'])->name('formateur.import');
    Route::post('/import/quiz', [QuizController::class, 'import'])->name('quiz.import');

    Route::resource('question', QuestionController::class);
    Route::resource('catalogue_formation', CatalogueFormationController::class);
    Route::post('/catalogue_formation/{id}/duplicate', [CatalogueFormationController::class, 'duplicate'])->name('catalogue_formation.duplicate');
    Route::get('catalogue_formation/{id}/download-pdf', [CatalogueFormationController::class, 'downloadPdf'])->name('catalogue_formation.download-pdf');

    // Route pour le manuel interactif admin
    Route::get('/manual', function () {
        return view('admin.manual');
    })->name('admin.manual');

    // Routes pour la réinitialisation des données
    Route::get('/parametre/reset-data', [ParametreAdminController::class, 'showResetData'])->name('admin.parametre.reset-data');
    Route::post('/parametre/reset-data', [ParametreAdminController::class, 'resetData'])->name('admin.parametre.reset-data.post');

    Route::resource('parametre', ParametreAdminController::class);
    Route::put('/parametre/{id}/update-image', [ParametreAdminController::class, 'updateImage'])->name('parametre.updateImage');

    Route::post('/import/prc', [PoleRelationClientController::class, 'import'])->name('prc.import');
    Route::post('/quiz-question/new', [QuizController::class, 'storeNewQuestion'])->name('quiz_question.new');
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
    Route::get('/achievements/detailed-stats', [\App\Http\Controllers\Admin\AchievementController::class, 'detailedStats'])->name('admin.achievements.detailed-stats');
    Route::get('/achievements/{achievement}/edit', [\App\Http\Controllers\Admin\AchievementController::class, 'edit'])->name('admin.achievements.edit');
    Route::put('/achievements/{achievement}', [\App\Http\Controllers\Admin\AchievementController::class, 'update'])->name('admin.achievements.update');
    Route::delete('/achievements/{achievement}', [\App\Http\Controllers\Admin\AchievementController::class, 'destroy'])->name('admin.achievements.destroy');
    Route::post('/achievements/reset', [\App\Http\Controllers\Admin\AchievementController::class, 'apiResetAchievements'])->name('admin.achievements.reset');

    // Web views for statistics and trends
    Route::get('/achievements/statistics', [\App\Http\Controllers\Admin\AchievementController::class, 'statistics'])->name('admin.achievements.statistics');
    Route::get('/achievements/trends', [\App\Http\Controllers\Admin\AchievementController::class, 'trends'])->name('admin.achievements.trends');

    Route::resource('partenaires', PartenaireController::class);
    Route::get('partenaires/{partenaire}/classements', [\App\Http\Controllers\Admin\ClassementController::class, 'show'])->name('classements.show');
    Route::get('classements', [\App\Http\Controllers\Admin\ClassementController::class, 'index'])->name('classement.index');
    Route::get('demande/historique', [DemandeHistoriqueController::class, 'index'])->name('demande.historique.index');
    Route::get('demande/historique/{id}', [DemandeHistoriqueController::class, 'show'])->name('demande.historique.show');
    Route::post('partenaires/import', [PartenaireController::class, 'import'])->name('partenaires.import');

    Route::resource('parrainage_events', \App\Http\Controllers\ParrainageEventController::class);
});

// Routes pour les formateurs
Route::middleware(['auth'])->prefix('formateur')->name('formateur.')->group(function () {
    // Tableau de bord formateur
    Route::get('/dashboard', [FormateurDashboardController::class, 'index'])->name('dashboard');

    // Routes stagiaires
    Route::get('/stagiaires', [FormateurStagiaireController::class, 'tousLesStagiaires'])->name('stagiaires.index');
    Route::get('/stagiaires/en-cours', [FormateurStagiaireController::class, 'stagiairesEnCours'])->name('stagiaires.en-cours');
    Route::get('/stagiaires/termines', [FormateurStagiaireController::class, 'stagiairesTerminesRecent'])->name('stagiaires.termines');
    Route::get('/stagiaires/{id}', [FormateurStagiaireController::class, 'show'])->name('stagiaires.show');

    // Routes classement et application
    Route::get('/classement', [FormateurClassementController::class, 'classementGeneral'])->name('classement');
    
    // CORRECTION : Route pour les utilisateurs de l'application
    Route::get('/stagiaires-application', [FormateurClassementController::class, 'stagiairesAvecApplication'])->name('stagiaires.application');
    
    // CORRECTION : Une seule route pour les détails de classement
    Route::get('/stagiaires/{id}/classement', [FormateurClassementController::class, 'detailsClassement'])->name('stagiaires.details-classement');

    // Routes formations
    Route::get('/formations', [FormateurController::class, 'mesFormations'])->name('formations.index');
    Route::get('/formations/{id}', [FormateurController::class, 'showFormation'])->name('formations.show');
    Route::get('/catalogue', [FormateurController::class, 'catalogueFormations'])->name('catalogue.index'); 
    
    // Route profil
    Route::get('/profile', [FormateurController::class, 'profile'])->name('profile');
    Route::post('/profile', [FormateurController::class, 'updateProfile'])->name('profile.update');
});


// Routes de fallback
Route::fallback(function () {
    return redirect('/login');
});

// Catch-all route for React Router (SPA) - DOIT ÊTRE LA DERNIÈRE
Route::get('/{any}', function () {
    return view('stagiaire');
})->where('any', '.*');