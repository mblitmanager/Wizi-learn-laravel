@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')

    <div class="container-fluid">
        <!-- En-tête de page amélioré -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-plus-circle me-2"></i>Création d'un nouveau quiz
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('quiz.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Quiz
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Nouveau quiz
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('quiz.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes améliorées -->
        @if ($errors->any())
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <div>
                        <span class="fw-medium">Veuillez corriger les erreurs suivantes :</span>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
                        <form action="{{ route('quiz.storeAll') }}" method="POST" enctype="multipart/form-data" novalidate>
                            @csrf

                            <!-- Section Informations du Quiz -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-primary text-white py-3">
                                    <h6 class="mb-0">
                                        <i class="bx bx-info-circle me-2"></i>Informations générales du quiz
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-dark">Titre du quiz</label>
                                            <input type="text" name="quiz[titre]" placeholder="Titre du quiz"
                                                class="form-control @error('quiz.titre') is-invalid @enderror"
                                                value="{{ old('quiz.titre') }}" required>
                                            @error('quiz.titre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-dark">Description</label>
                                            <textarea name="quiz[description]" placeholder="Description du quiz"
                                                class="form-control @error('quiz.description') is-invalid @enderror" rows="3">{{ old('quiz.description') }}</textarea>
                                            @error('quiz.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Niveau</label>
                                            <select name="quiz[niveau]"
                                                class="form-select @error('quiz.niveau') is-invalid @enderror">
                                                <option value="">Sélectionner un niveau</option>
                                                <option value="débutant"
                                                    {{ old('quiz.niveau') == 'débutant' ? 'selected' : '' }}>Débutant
                                                </option>
                                                <option value="intermédiaire"
                                                    {{ old('quiz.niveau') == 'intermédiaire' ? 'selected' : '' }}>
                                                    Intermédiaire</option>
                                                <option value="avancé"
                                                    {{ old('quiz.niveau') == 'avancé' ? 'selected' : '' }}>Avancé</option>
                                            </select>
                                            @error('quiz.niveau')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Durée (minutes)</label>
                                            <input type="number" name="quiz[duree]" placeholder="Durée en minutes"
                                                class="form-control @error('quiz.duree') is-invalid @enderror"
                                                value="{{ old('quiz.duree') }}">
                                            @error('quiz.duree')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Points totaux</label>
                                            <input type="number" name="quiz[nb_points_total]" placeholder="Points totaux"
                                                class="form-control @error('quiz.nb_points_total') is-invalid @enderror"
                                                value="{{ old('quiz.nb_points_total') }}">
                                            @error('quiz.nb_points_total')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark">Formation associée</label>
                                            <select name="quiz[formation_id]"
                                                class="form-select @error('quiz.formation_id') is-invalid @enderror">
                                                @foreach ($formations as $formation)
                                                    <option value="{{ $formation->id }}"
                                                        {{ old('quiz.formation_id') == $formation->id ? 'selected' : '' }}>
                                                        {{ $formation->titre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('quiz.formation_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Question -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-gradient-primary text-white py-3">
                                    <h6 class="mb-0 text-white fw-semibold">
                                        <i class="bx bx-question-mark me-2"></i>Informations de la question
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label class="form-label fw-semibold text-dark">Texte de la question</label>
                                            <textarea name="question[text]" class="form-control @error('question.text') is-invalid @enderror" rows="3"
                                                placeholder="Saisissez le texte de la question..." required>{{ old('question.text') }}</textarea>
                                            @error('question.text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Média associé</label>
                                            <input type="file" name="question[media_url]"
                                                class="form-control @error('question.media_url') is-invalid @enderror"
                                                accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.mp4,.mp3">
                                            @error('question.media_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Type de question</label>
                                            <select name="question[type]"
                                                class="form-select @error('question.type') is-invalid @enderror" required>
                                                <option value="">Sélectionner un type</option>
                                                <option value="question audio"
                                                    {{ old('question.type') == 'question audio' ? 'selected' : '' }}>
                                                    Question audio</option>
                                                <option value="remplir le champ vide"
                                                    {{ old('question.type') == 'remplir le champ vide' ? 'selected' : '' }}>
                                                    Remplir le champ vide</option>
                                                <option value="carte flash"
                                                    {{ old('question.type') == 'carte flash' ? 'selected' : '' }}>Carte
                                                    flash</option>
                                                <option value="correspondance"
                                                    {{ old('question.type') == 'correspondance' ? 'selected' : '' }}>
                                                    Correspondance</option>
                                                <option value="choix multiples"
                                                    {{ old('question.type') == 'choix multiples' ? 'selected' : '' }}>Choix
                                                    multiples</option>
                                                <option value="rearrangement"
                                                    {{ old('question.type') == 'rearrangement' ? 'selected' : '' }}>
                                                    Rearrangement</option>
                                                <option value="vrai/faux"
                                                    {{ old('question.type') == 'vrai/faux' ? 'selected' : '' }}>Vrai / Faux
                                                </option>
                                                <option value="banque de mots"
                                                    {{ old('question.type') == 'banque de mots' ? 'selected' : '' }}>Banque
                                                    de mots</option>
                                            </select>
                                            @error('question.type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Points attribués</label>
                                            <input type="number" name="question[points]"
                                                class="form-control @error('question.points') is-invalid @enderror"
                                                placeholder="Points" value="{{ old('question.points') }}"
                                                min="1">
                                            @error('question.points')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Réponse correcte</label>
                                            <input type="text" name="question[reponse_correct]"
                                                class="form-control @error('question.reponse_correct') is-invalid @enderror"
                                                placeholder="Réponse attendue..."
                                                value="{{ old('question.reponse_correct') }}">
                                            @error('question.reponse_correct')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-dark">Explication détaillée</label>
                                            <textarea name="question[explication]" class="form-control @error('question.explication') is-invalid @enderror"
                                                rows="3" placeholder="Ajoutez une explication pour cette question...">{{ old('question.explication') }}</textarea>
                                            @error('question.explication')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-dark">Astuce</label>
                                            <input type="text" name="question[astuce]"
                                                class="form-control @error('question.astuce') is-invalid @enderror"
                                                placeholder="Astuce pour la question..."
                                                value="{{ old('question.astuce') }}">
                                            @error('question.astuce')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Réponses -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-gradient-primary text-white py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h6 class="mb-0 text-white fw-semibold">
                                                <i class="bx bx-list-check me-2"></i>Gestion des réponses
                                            </h6>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <button type="button" class="btn btn-light btn-sm" id="add-reponse-btn">
                                                <i class="bx bx-plus me-1"></i> Ajouter une réponse
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="reponses-container">
                                        <div class="card reponse-form mb-3 border-0 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 text-dark fw-semibold">Réponse #1</h6>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger remove-reponse-btn" disabled>
                                                        <i class="bx bx-trash me-1"></i> Supprimer
                                                    </button>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-dark">Texte de la
                                                            réponse</label>
                                                        <input type="text" name="reponse[text][]" class="form-control"
                                                            placeholder="Saisissez le texte de la réponse...">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-dark">Statut</label>
                                                        <select name="reponse[is_correct][]" class="form-select">
                                                            <option value="">Sélectionner un statut</option>
                                                            <option value="1">✅ Réponse correcte</option>
                                                            <option value="0">❌ Réponse incorrecte</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold text-dark">Position</label>
                                                        <input type="number" name="reponse[position][]"
                                                            class="form-control" placeholder="Position">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold text-dark">Pair
                                                            correspondante</label>
                                                        <input type="text" name="reponse[match_pair][]"
                                                            class="form-control" placeholder="Match pair">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold text-dark">Groupe</label>
                                                        <input type="text" name="reponse[bank_group][]"
                                                            class="form-control" placeholder="Groupe banque">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold text-dark">Dos de
                                                            carte</label>
                                                        <textarea name="reponse[flashcard_back][]" class="form-control" placeholder="Dos de la flashcard" rows="1"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Template pour nouvelle réponse -->
                                    <template id="reponse-template">
                                        <div class="card reponse-form mb-3 border-0 shadow-sm new-reponse">
                                            <div class="card-body p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 text-dark fw-semibold">
                                                        Nouvelle Réponse
                                                        <span class="badge bg-warning text-dark ms-2">Nouveau</span>
                                                    </h6>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger remove-reponse-btn">
                                                        <i class="bx bx-trash me-1"></i> Supprimer
                                                    </button>
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-dark">Texte de la
                                                            réponse</label>
                                                        <input type="text" name="reponse[text][]" class="form-control"
                                                            placeholder="Saisissez le texte de la réponse...">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold text-dark">Statut</label>
                                                        <select name="reponse[is_correct][]" class="form-select">
                                                            <option value="">Sélectionner un statut</option>
                                                            <option value="1">✅ Réponse correcte</option>
                                                            <option value="0">❌ Réponse incorrecte</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold text-dark">Position</label>
                                                        <input type="number" name="reponse[position][]"
                                                            class="form-control" placeholder="Position" value="0">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold text-dark">Pair
                                                            correspondante</label>
                                                        <input type="text" name="reponse[match_pair][]"
                                                            class="form-control" placeholder="Match pair">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold text-dark">Groupe</label>
                                                        <input type="text" name="reponse[bank_group][]"
                                                            class="form-control" placeholder="Groupe banque">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold text-dark">Dos de
                                                            carte</label>
                                                        <textarea name="reponse[flashcard_back][]" class="form-control" placeholder="Dos de la flashcard" rows="1"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Bouton de soumission -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success px-5 py-2">
                                    <i class="bx bx-save me-2"></i> Créer le quiz
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log('DOM chargé - initialisation des scripts');

            // Gestion de l'ajout de réponses
            document.getElementById('add-reponse-btn').addEventListener('click', function() {
                const container = document.getElementById('reponses-container');
                const template = document.getElementById('reponse-template');

                if (!template) {
                    console.error('Template non trouvé');
                    return;
                }

                // Compter les réponses existantes
                const existingReponses = container.querySelectorAll('.reponse-form').length;
                const newIndex = existingReponses + 1;

                // Cloner le template
                const newReponse = template.content.cloneNode(true);

                // Mettre à jour le titre de la réponse
                const titleElement = newReponse.querySelector('h6');
                if (titleElement) {
                    titleElement.innerHTML =
                        `Réponse #${newIndex} <span class="badge bg-warning text-dark ms-2">Nouveau</span>`;
                }

                // Ajouter au conteneur
                container.appendChild(newReponse);

                // Activer le bouton de suppression de la première réponse s'il y a plus d'une réponse
                const allReponses = container.querySelectorAll('.reponse-form');
                if (allReponses.length > 1) {
                    const firstRemoveBtn = allReponses[0].querySelector('.remove-reponse-btn');
                    if (firstRemoveBtn) {
                        firstRemoveBtn.disabled = false;
                    }
                }

                console.log('Nouvelle réponse ajoutée');
            });

            // Gestion de la suppression des réponses
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-reponse-btn') ||
                    e.target.closest('.remove-reponse-btn')) {

                    const button = e.target.classList.contains('remove-reponse-btn') ?
                        e.target : e.target.closest('.remove-reponse-btn');
                    const reponseForm = button.closest('.reponse-form');

                    if (reponseForm) {
                        const container = document.getElementById('reponses-container');
                        const allReponses = container.querySelectorAll('.reponse-form');

                        if (allReponses.length > 1) {
                            reponseForm.remove();

                            // Mettre à jour les numéros des réponses restantes
                            const remainingReponses = container.querySelectorAll('.reponse-form');
                            remainingReponses.forEach((reponse, index) => {
                                const titleElement = reponse.querySelector('h6');
                                if (titleElement && !titleElement.querySelector('.badge')) {
                                    titleElement.textContent = `Réponse #${index + 1}`;
                                }
                            });

                            // Désactiver le bouton de suppression s'il ne reste qu'une réponse
                            if (remainingReponses.length === 1) {
                                const firstRemoveBtn = remainingReponses[0].querySelector(
                                    '.remove-reponse-btn');
                                if (firstRemoveBtn) {
                                    firstRemoveBtn.disabled = true;
                                }
                            }
                        } else {
                            alert('Vous devez avoir au moins une réponse.');
                        }
                    }
                }
            });

            // Validation des champs du quiz (optionnel)
            function checkQuizFieldsFilled() {
                let isValid = true;
                const quizForm = document.querySelector('#quiz-form');

                if (quizForm) {
                    const requiredFields = quizForm.querySelectorAll('[required]');
                    requiredFields.forEach(field => {
                        if (!field.value || field.value.trim() === '') {
                            isValid = false;
                        }
                    });
                }

                return isValid;
            }

            // Écouter les changements dans le formulaire quiz
            const quizFields = document.querySelectorAll(
            '#quiz-form input, #quiz-form select, #quiz-form textarea');
            quizFields.forEach(field => {
                field.addEventListener('input', function() {
                    if (checkQuizFieldsFilled()) {
                        console.log('Tous les champs requis du quiz sont remplis');
                    }
                });
            });
        });
    </script>

    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .badge {
            border-radius: 4px;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
        }

        .alert {
            border-radius: 8px;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }

        .rounded-3 {
            border-radius: 0.75rem !important;
        }

        .reponse-form {
            transition: all 0.3s ease;
            border-left: 4px solid #0d6efd;
        }

        .reponse-form:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .new-reponse {
            border: 2px solid #4CAF50 !important;
            background-color: #e8f5e9 !important;
        }

        /* Style pour les champs désactivés */
        input:disabled,
        select:disabled,
        textarea:disabled {
            background-color: #f8f9fa !important;
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Animation pour les nouveaux éléments */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .new-reponse {
            animation: slideIn 0.3s ease-out;
        }
    </style>
@endsection
