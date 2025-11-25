@extends('admin.layout')
@section('title', 'Réinitialisation des données')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Tableau de bord
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Réinitialisation des données
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('parametre.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('success'))
            <div class="alert alert-success border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Section Avertissement -->
                        <div class="card border-warning bg-light-warning mb-4">
                            <div class="card-header bg-transparent border-warning py-3">
                                <h6 class="mb-0 text-dark fw-semibold">
                                    <i class="bx bx-error-circle me-2"></i>Avertissement important
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning border-0 mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-info-circle me-2 fs-4"></i>
                                        <div>
                                            <h6 class="alert-heading fw-bold mb-2">Action critique</h6>
                                            <p class="mb-2">Cette opération va supprimer définitivement les données
                                                sélectionnées. Veuillez vérifier soigneusement votre sélection avant de
                                                continuer.</p>
                                            <small class="text-muted">
                                                <i class="bx bx-shield-x me-1"></i>
                                                Cette action est irréversible et ne peut pas être annulée.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire de réinitialisation -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h6 class="mb-0 text-dark fw-semibold">
                                    <i class="bx bx-list-check me-2"></i>Sélection des données à réinitialiser
                                </h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.parametre.reset-data') }}" method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir réinitialiser les données sélectionnées ? Cette action est irréversible.');">
                                    @csrf

                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Données de progression -->
                                            <div class="card border-0 mb-4">
                                                <div class="card-header bg-transparent border-bottom-0 py-2">
                                                    <h6 class="mb-0 fw-semibold text-dark">
                                                        <i class="bx bx-trending-up me-2"></i>Progression et statistiques
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                            value="classements" id="resetClassements">
                                                        <label class="form-check-label fw-medium text-dark"
                                                            for="resetClassements">
                                                            Classements
                                                        </label>
                                                        <div class="form-text text-muted">
                                                            Supprime tous les classements et scores des stagiaires
                                                        </div>
                                                    </div>

                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                            value="progression" id="resetProgression">
                                                        <label class="form-check-label fw-medium text-dark"
                                                            for="resetProgression">
                                                            Progression des stagiaires
                                                        </label>
                                                        <div class="form-text text-muted">
                                                            Réinitialise l'avancement dans les formations
                                                        </div>
                                                    </div>

                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                            value="achievements" id="resetAchievements">
                                                        <label class="form-check-label fw-medium text-dark"
                                                            for="resetAchievements">
                                                            Succès et statistiques
                                                        </label>
                                                        <div class="form-text text-muted">
                                                            Efface les succès débloqués et réinitialise les statistiques
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- Données de quiz -->
                                            <div class="card border-0 mb-4">
                                                <div class="card-header bg-transparent border-bottom-0 py-2">
                                                    <h6 class="mb-0 fw-semibold text-dark">
                                                        <i class="bx bx-question-mark me-2"></i>Données des quiz
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" name="reset_data[]"
                                                            value="participations" id="resetParticipations">
                                                        <label class="form-check-label fw-medium text-dark"
                                                            for="resetParticipations">
                                                            Participations aux quiz
                                                        </label>
                                                        <div class="form-text text-muted">
                                                            Supprime l'historique des participations aux quiz
                                                        </div>
                                                    </div>

                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="reset_data[]" value="reponses" id="resetReponses">
                                                        <label class="form-check-label fw-medium text-dark"
                                                            for="resetReponses">
                                                            Réponses aux quiz
                                                        </label>
                                                        <div class="form-text text-muted">
                                                            Efface toutes les réponses données aux questions
                                                        </div>
                                                    </div>

                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="reset_data[]" value="quiz_history"
                                                            id="resetQuizHistory">
                                                        <label class="form-check-label fw-medium text-dark"
                                                            for="resetQuizHistory">
                                                            Historique des quiz joués
                                                        </label>
                                                        <div class="form-text text-muted">
                                                            Supprime l'historique complet des sessions de quiz
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sélection globale -->
                                    <div class="card border-primary bg-light-primary mb-4">
                                        <div class="card-header bg-transparent border-primary py-3">
                                            <h6 class="mb-0 text-dark fw-semibold">
                                                <i class="bx bx-check-shield me-2"></i>Confirmation finale
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                                <label class="form-check-label fw-bold text-dark" for="selectAll">
                                                    Sélectionner toutes les données
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="confirmReset"
                                                    required>
                                                <label class="form-check-label fw-semibold text-dark" for="confirmReset">
                                                    Je comprends que cette action est irréversible et je confirme vouloir
                                                    réinitialiser les données sélectionnées
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Boutons d'action -->
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-danger px-5 py-2 me-3" id="submitBtn"
                                            disabled>
                                            <i class="bx bx-reset me-2"></i> Réinitialiser les données sélectionnées
                                        </button>
                                        <a href="{{ route('parametre.index') }}"
                                            class="btn btn-outline-secondary px-5 py-2">
                                            <i class="bx bx-x me-2"></i> Annuler
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const confirmResetCheckbox = document.getElementById('confirmReset');
            const submitBtn = document.getElementById('submitBtn');
            const checkboxes = document.querySelectorAll('input[name="reset_data[]"]');

            // Sélectionner/désélectionner toutes les cases
            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSubmitButton();
            });

            // Mettre à jour la case "Sélectionner tout" si toutes les cases sont cochées
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    const someChecked = Array.from(checkboxes).some(cb => cb.checked);

                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;

                    updateSubmitButton();
                });
            });

            // Activer/désactiver le bouton de soumission
            confirmResetCheckbox.addEventListener('change', updateSubmitButton);

            function updateSubmitButton() {
                const hasSelectedData = Array.from(checkboxes).some(cb => cb.checked);
                const isConfirmed = confirmResetCheckbox.checked;

                submitBtn.disabled = !(hasSelectedData && isConfirmed);
            }

            // Confirmation finale renforcée
            document.querySelector('form').addEventListener('submit', function(e) {
                const selectedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

                if (!confirm(
                        `Êtes-vous ABSOLUMENT sûr de vouloir réinitialiser ${selectedCount} type(s) de données ?\n\nCette action est IRREVERSIBLE et supprimera définitivement les données sélectionnées.`
                    )) {
                    e.preventDefault();
                    return false;
                }

                // Afficher un indicateur de chargement
                submitBtn.innerHTML =
                    '<i class="bx bx-loader bx-spin me-2"></i> Réinitialisation en cours...';
                submitBtn.disabled = true;
            });
        });
    </script>

    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .alert {
            border-radius: 10px;
        }

        .bg-light-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .bg-light-primary {
            background-color: rgba(13, 110, 253, 0.05) !important;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-left: 1.5rem;
        }

        #submitBtn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
@endsection
