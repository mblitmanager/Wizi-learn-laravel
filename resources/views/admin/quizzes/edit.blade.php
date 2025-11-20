@extends('admin.layout')
@section('title', 'Modifier un Quiz')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page amélioré -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-edit me-2"></i>Modification du quiz
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('quiz.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Quiz
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $quiz->titre }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="bx bx-upload me-1"></i> Importer
                        </button>
                        <a href="{{ route('quiz.index') }}" class="btn btn-outline-primary btn-sm ms-2">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                        <form action="{{ route('quiz.duplicate', $quiz->id) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm"
                                onclick="return confirm('Dupliquer ce quiz ?')">
                                <i class="bx bx-copy me-1"></i> Dupliquer
                            </button>
                        </form>
                        @if ($quiz->status === 'inactif')
                            <form action="{{ route('quiz.enable', $quiz->id) }}" method="POST" class="d-inline ms-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm"
                                    onclick="return confirm('Réactiver ce quiz ?')">
                                    <i class="bx bx-check-circle me-1"></i> Réactiver
                                </button>
                            </form>
                        @else
                            <form action="{{ route('quiz.disable', $quiz->id) }}" method="POST" class="d-inline ms-2">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm"
                                    onclick="return confirm('Désactiver ce quiz ?')">
                                    <i class="bx bx-ban me-1"></i> Désactiver
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('quiz.destroy', $quiz->id) }}" method="POST" class="d-inline ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Supprimer ce quiz ?')">
                                <i class="bx bx-trash me-1"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes améliorées -->
        @if ($errors->any())
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ $errors->first() }}</span>
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
                        <form action="{{ route('quiz.update', $quiz->id) }}" method="POST" enctype="multipart/form-data"
                            novalidate>
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">

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
                                                value="{{ old('quiz.titre', $quiz->titre) }}" required>
                                            @error('quiz.titre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-semibold text-dark">Description</label>
                                            <textarea name="quiz[description]" placeholder="Description du quiz"
                                                class="form-control @error('quiz.description') is-invalid @enderror" rows="3">{{ old('quiz.description', $quiz->description) }}</textarea>
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
                                                    {{ old('quiz.niveau', $quiz->niveau) == 'débutant' ? 'selected' : '' }}>
                                                    Débutant</option>
                                                <option value="intermédiaire"
                                                    {{ old('quiz.niveau', $quiz->niveau) == 'intermédiaire' ? 'selected' : '' }}>
                                                    Intermédiaire</option>
                                                <option value="avancé"
                                                    {{ old('quiz.niveau', $quiz->niveau) == 'avancé' ? 'selected' : '' }}>
                                                    Avancé</option>
                                            </select>
                                            @error('quiz.niveau')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Durée (minutes)</label>
                                            <input type="number" name="quiz[duree]" placeholder="Durée en minutes"
                                                class="form-control @error('quiz.duree') is-invalid @enderror"
                                                value="{{ old('quiz.duree', $quiz->duree) }}">
                                            @error('quiz.duree')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-semibold text-dark">Points totaux</label>
                                            <input type="number" name="quiz[nb_points_total]"
                                                placeholder="Points totaux"
                                                class="form-control @error('quiz.nb_points_total') is-invalid @enderror"
                                                value="{{ old('quiz.nb_points_total', $quiz->nb_points_total) }}">
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
                                                        {{ old('quiz.formation_id', $quiz->formation_id) == $formation->id ? 'selected' : '' }}>
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

                            <!-- Section Gestion des Questions -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-gradient-primary text-white py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="mb-0 text-white fw-semibold">
                                                Gestion des Questions
                                                <span
                                                    class="badge bg-light text-primary ms-2">{{ $questions->count() }}</span>
                                            </h5>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#NewQuestionModal">
                                                <i class="bx bx-plus me-1"></i> Nouvelle Question
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- Barre de Recherche Améliorée -->
                                    <div class="row mb-4">
                                        <div class="col-md-8">
                                            <div class="search-box p-3 bg-light rounded-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-end-0">
                                                            <i class="bx bx-search text-muted"></i>
                                                        </span>
                                                        <input type="text" id="searchInput" name="search"
                                                            placeholder="Rechercher une question..."
                                                            class="form-control border-start-0" value="">
                                                        <button id="searchBtn" class="btn btn-primary px-4">
                                                            <i class="bx bx-search me-1"></i> Rechercher
                                                        </button>
                                                        <button id="resetBtn" class="btn btn-outline-secondary">
                                                            <i class="bx bx-reset me-1"></i> Effacer
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Liste des Questions -->
                                    <div class="accordion" id="accordionQuestions">
                                        @foreach ($questions as $qIndex => $question)
                                            <div class="accordion-item question-item border-0 mb-3 shadow-sm">
                                                <input type="hidden" name="questions[{{ $qIndex }}][_delete]"
                                                    value="0" class="delete-flag">

                                                <!-- En-tête de Question -->
                                                <div class="accordion-header">
                                                    <div class="accordion-button collapsed bg-light text-dark d-flex justify-content-between align-items-center p-4 rounded-3"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse{{ $qIndex }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse{{ $qIndex }}">
                                                        <div class="d-flex align-items-center flex-grow-1">
                                                            <div class="question-number me-3">
                                                                <span class="badge bg-primary rounded-circle p-2">
                                                                    #{{ $question->id }}
                                                                </span>
                                                            </div>
                                                            <div class="question-content flex-grow-1">
                                                                <h6 class="mb-1 fw-semibold text-dark">
                                                                    {{ Str::limit($question->text, 100) }}</h6>
                                                                <div class="question-meta">
                                                                    <small class="text-muted">
                                                                        <i class="bx bx-time me-1"></i>
                                                                        Créé le
                                                                        {{ $question->created_at->format('d/m/Y') }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="question-actions d-flex align-items-center">
                                                            <span class="badge bg-info me-3">
                                                                <i class="bx bx-tag me-1"></i>{{ $question->type }}
                                                            </span>
                                                            <span class="badge bg-light text-dark me-3">
                                                                <i
                                                                    class="bx bx-star me-1"></i>{{ $question->points ?? 2 }}
                                                                pts
                                                            </span>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger remove-question-btn"
                                                                title="Supprimer cette question">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Contenu de la Question -->
                                                <div id="collapse{{ $qIndex }}" class="accordion-collapse collapse"
                                                    data-bs-parent="#accordionQuestions">
                                                    <div class="accordion-body bg-light rounded-bottom-3 p-4">
                                                        <input type="hidden" name="questions[{{ $qIndex }}][id]"
                                                            value="{{ $question->id }}">

                                                        <!-- Informations Principales -->
                                                        <div class="row mb-4">
                                                            <div class="col-md-8 mb-3">
                                                                <label class="form-label fw-semibold text-dark">
                                                                    <i class="bx bx-edit me-2"></i>Texte de la question
                                                                </label>
                                                                <textarea name="questions[{{ $qIndex }}][text]" class="form-control" rows="3"
                                                                    placeholder="Saisissez le texte de la question..." required>{{ old("questions.$qIndex.text", $question->text) }}</textarea>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-semibold text-dark">
                                                                    <i class="bx bx-image me-2"></i>Média associé
                                                                </label>
                                                                <input type="file"
                                                                    name="questions[{{ $qIndex }}][media_file]"
                                                                    class="form-control"
                                                                    accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.mp4,.mp3">
                                                                @if ($question->media_url)
                                                                    <div class="mt-2">
                                                                        <a href="{{ asset($question->media_url) }}"
                                                                            target="_blank"
                                                                            class="btn btn-sm btn-outline-primary w-100">
                                                                            <i class="bx bx-link-external me-1"></i>Voir le
                                                                            fichier actuel
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Configuration de la Question -->
                                                        <div class="row mb-4">
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-semibold text-dark">
                                                                    <i class="bx bx-category me-2"></i>Type de question
                                                                </label>
                                                                <select name="questions[{{ $qIndex }}][type]"
                                                                    class="form-select" required>
                                                                    <option value="">Sélectionner un type</option>
                                                                    @foreach (['question audio', 'remplir le champ vide', 'carte flash', 'correspondance', 'choix multiples', 'rearrangement', 'vrai/faux', 'banque de mots'] as $type)
                                                                        <option value="{{ $type }}"
                                                                            {{ old("questions.$qIndex.type", $question->type) == $type ? 'selected' : '' }}>
                                                                            {{ ucfirst($type) }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-semibold text-dark">
                                                                    <i class="bx bx-star me-2"></i>Points attribués
                                                                </label>
                                                                <input type="number"
                                                                    name="questions[{{ $qIndex }}][points]"
                                                                    class="form-control" min="1" max="10"
                                                                    value="{{ old("questions.$qIndex.points", $question->points ?: 2) }}">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-semibold text-dark">
                                                                    <i class="bx bx-check-circle me-2"></i>Réponse correcte
                                                                </label>
                                                                <input type="text"
                                                                    name="questions[{{ $qIndex }}][reponse_correct]"
                                                                    class="form-control" placeholder="Réponse attendue..."
                                                                    value="{{ old("questions.$qIndex.reponse_correct", $question->reponse_correct) }}">
                                                            </div>
                                                        </div>

                                                        <!-- Explication -->
                                                        <div class="mb-4">
                                                            <label class="form-label fw-semibold text-dark">
                                                                <i class="bx bx-info-circle me-2"></i>Explication détaillée
                                                            </label>
                                                            <textarea name="questions[{{ $qIndex }}][explication]" class="form-control" rows="3"
                                                                placeholder="Ajoutez une explication pour cette question...">{{ old("questions.$qIndex.explication", $question->explication) }}</textarea>
                                                        </div>

                                                        <!-- Section des Réponses -->
                                                        <div class="reponses-section">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3">
                                                                <div>
                                                                    <h6 class="mb-0 text-dark fw-semibold">
                                                                        <i class="bx bx-list-check me-2"></i>Réponses
                                                                        <span
                                                                            class="badge bg-primary ms-2">{{ $question->reponses->count() }}</span>
                                                                    </h6>
                                                                    <small class="text-muted">Configurez les différentes
                                                                        options de réponse</small>
                                                                </div>
                                                                <button type="button"
                                                                    class="btn btn-primary add-reponse-btn"
                                                                    data-question-index="{{ $qIndex }}">
                                                                    <i class="bx bx-plus me-1"></i> Ajouter une réponse
                                                                </button>
                                                            </div>

                                                            <!-- Conteneur des Réponses -->
                                                            <div id="reponses-container-{{ $qIndex }}"
                                                                class="reponses-grid">
                                                                @foreach ($question->reponses as $rIndex => $reponse)
                                                                    <div class="card reponse-form mb-3 border-0 shadow-sm">
                                                                        <div class="card-body p-4">
                                                                            <input type="hidden"
                                                                                name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][id]"
                                                                                value="{{ $reponse->id }}">
                                                                            <input type="hidden" class="delete-flag"
                                                                                name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][_delete]"
                                                                                value="0">

                                                                            <!-- En-tête de la réponse -->
                                                                            <div
                                                                                class="d-flex justify-content-between align-items-center mb-3">
                                                                                <h6 class="mb-0 text-dark fw-semibold">
                                                                                    Réponse #{{ $rIndex + 1 }}
                                                                                    @if ($reponse->is_correct)
                                                                                        <span
                                                                                            class="badge bg-success ms-2">
                                                                                            <i
                                                                                                class="bx bx-check me-1"></i>Correcte
                                                                                        </span>
                                                                                    @endif
                                                                                </h6>
                                                                                <button type="button"
                                                                                    class="btn btn-sm btn-outline-danger remove-reponse-btn">
                                                                                    <i class="bx bx-trash me-1"></i>
                                                                                    Supprimer
                                                                                </button>
                                                                            </div>

                                                                            <!-- Contenu de la réponse -->
                                                                            <div class="row g-3">
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label fw-semibold text-dark">Texte
                                                                                        de la réponse</label>
                                                                                    <input type="text"
                                                                                        name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][text]"
                                                                                        class="form-control"
                                                                                        placeholder="Saisissez le texte de la réponse..."
                                                                                        value="{{ old("questions.$qIndex.reponses.$rIndex.text", $reponse->text) }}">
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <label
                                                                                        class="form-label fw-semibold text-dark">Statut</label>
                                                                                    <select
                                                                                        name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][is_correct]"
                                                                                        class="form-select">
                                                                                        <option value="1"
                                                                                            {{ $reponse->is_correct ? 'selected' : '' }}>
                                                                                            ✅ Réponse correcte
                                                                                        </option>
                                                                                        <option value="0"
                                                                                            {{ !$reponse->is_correct ? 'selected' : '' }}>
                                                                                            ❌ Réponse incorrecte
                                                                                        </option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Options avancées -->
                                                                            <div class="row g-3 mt-2">
                                                                                <div class="col-md-3">
                                                                                    <label
                                                                                        class="form-label fw-semibold text-dark">Position</label>
                                                                                    <input type="number"
                                                                                        name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][position]"
                                                                                        class="form-control"
                                                                                        value="{{ old("questions.$qIndex.reponses.$rIndex.position", $reponse->position) }}">
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <label
                                                                                        class="form-label fw-semibold text-dark">Pair
                                                                                        correspondante</label>
                                                                                    <input type="text"
                                                                                        name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][match_pair]"
                                                                                        class="form-control"
                                                                                        value="{{ old("questions.$qIndex.reponses.$rIndex.match_pair", $reponse->match_pair) }}">
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <label
                                                                                        class="form-label fw-semibold text-dark">Groupe</label>
                                                                                    <input type="text"
                                                                                        name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][bank_group]"
                                                                                        class="form-control"
                                                                                        value="{{ old("questions.$qIndex.reponses.$rIndex.bank_group", $reponse->bank_group) }}">
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <label
                                                                                        class="form-label fw-semibold text-dark">Dos
                                                                                        de carte</label>
                                                                                    <input type="text"
                                                                                        name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][flashcard_back]"
                                                                                        class="form-control"
                                                                                        value="{{ old("questions.$qIndex.reponses.$rIndex.flashcard_back", $reponse->flashcard_back) }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <!-- Template pour nouvelle réponse -->
                                                            <template id="reponse-template-{{ $qIndex }}">
                                                                <div
                                                                    class="card reponse-form mb-3 border-0 shadow-sm new-reponse">
                                                                    <div class="card-body p-4">
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                                            <h6 class="mb-0 text-dark fw-semibold">
                                                                                Nouvelle Réponse
                                                                                <span
                                                                                    class="badge bg-warning text-dark ms-2">Nouveau</span>
                                                                            </h6>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-danger remove-reponse-btn">
                                                                                <i class="bx bx-trash me-1"></i> Supprimer
                                                                            </button>
                                                                        </div>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label fw-semibold text-dark">Texte
                                                                                    de la réponse</label>
                                                                                <input type="text"
                                                                                    name="questions[{{ $qIndex }}][reponses][__RINDEX__][text]"
                                                                                    class="form-control"
                                                                                    placeholder="Saisissez le texte de la réponse...">
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label
                                                                                    class="form-label fw-semibold text-dark">Bonne
                                                                                    réponse</label>
                                                                                <select
                                                                                    name="questions[{{ $qIndex }}][reponses][__RINDEX__][is_correct]"
                                                                                    class="form-select">
                                                                                    <option value="1">✅ Réponse
                                                                                        correcte</option>
                                                                                    <option value="0" selected>❌
                                                                                        Réponse incorrecte</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row g-3 mt-2">
                                                                            <div class="col-md-3">
                                                                                <label
                                                                                    class="form-label fw-semibold text-dark">Position</label>
                                                                                <input type="number"
                                                                                    name="questions[{{ $qIndex }}][reponses][__RINDEX__][position]"
                                                                                    class="form-control" value="0">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label
                                                                                    class="form-label fw-semibold text-dark">Pair
                                                                                    correspondante</label>
                                                                                <input type="text"
                                                                                    name="questions[{{ $qIndex }}][reponses][__RINDEX__][match_pair]"
                                                                                    class="form-control"
                                                                                    placeholder="Match pair">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label
                                                                                    class="form-label fw-semibold text-dark">Groupe</label>
                                                                                <input type="text"
                                                                                    name="questions[{{ $qIndex }}][reponses][__RINDEX__][bank_group]"
                                                                                    class="form-control"
                                                                                    placeholder="Groupe banque">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label
                                                                                    class="form-label fw-semibold text-dark">Dos
                                                                                    de carte</label>
                                                                                <input type="text"
                                                                                    name="questions[{{ $qIndex }}][reponses][__RINDEX__][flashcard_back]"
                                                                                    class="form-control"
                                                                                    placeholder="Dos de la flashcard">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Message Aucun Résultat -->
                                    <div id="noResults" class="alert alert-warning d-none text-center py-4">
                                        <div class="py-3">
                                            <i class="bx bx-search-alt display-4 text-warning mb-3"></i>
                                            <h5 class="text-dark">Aucune question trouvée</h5>
                                            <p class="text-muted mb-0">Aucune question ne correspond à votre recherche.</p>
                                        </div>
                                    </div>

                                    <!-- Pagination Améliorée -->
                                    <div
                                        class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light rounded-3">
                                        <div class="text-muted">
                                            <small>Affichage de <span id="currentItems">0</span> sur
                                                {{ $questions->count() }} questions</small>
                                        </div>
                                        <nav>
                                            <ul class="pagination mb-0" id="paginationNumbersWrapper">
                                                <li class="page-item disabled" id="prevPage">
                                                    <a class="page-link" href="#">
                                                        <i class="bx bx-chevron-left me-1"></i> Précédent
                                                    </a>
                                                </li>
                                                <li class="page-item next" id="nextPage">
                                                    <a class="page-link" href="#">
                                                        Suivant <i class="bx bx-chevron-right ms-1"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton de soumission -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success px-5 py-2">
                                    <i class="bx bx-save me-2"></i> Mettre à jour le quiz
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'import amélioré -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bx bx-upload me-2"></i>Importer des questions
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('quiz_question.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                        <div class="mb-4">
                            <label for="file" class="form-label fw-semibold">Fichier Excel (.xlsx, .xls)</label>
                            <input type="file" name="file" id="file" class="form-control" required
                                accept=".xlsx,.xls">
                            <div class="form-text text-muted">
                                <small>Format accepté : Excel (.xlsx, .xls)</small>
                            </div>
                        </div>
                        <div class="progress mb-3 d-none" id="progressBarWrapper">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                role="progressbar" style="width: 100%;">
                                Importation en cours...
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-upload me-1"></i> Importer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nouvelle Question amélioré -->
    <div class="modal fade" id="NewQuestionModal" tabindex="-1" aria-labelledby="newQuestionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="newQuestionModalLabel">
                        <i class="bx bx-plus me-2"></i>Ajouter une nouvelle question
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('quiz_question.new') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Texte de la question</label>
                                <input type="text" name="text" class="form-control"
                                    placeholder="Entrez votre question..." required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Type de question</label>
                                <select name="question[type]" class="form-select" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="question audio">Question audio</option>
                                    <option value="remplir le champ vide">Remplir le champ vide</option>
                                    <option value="carte flash">Carte flash</option>
                                    <option value="correspondance">Correspondance</option>
                                    <option value="choix multiples">Choix multiples</option>
                                    <option value="rearrangement">Rearrangement</option>
                                    <option value="vrai/faux">Vrai / Faux</option>
                                    <option value="banque de mots">Banque de mots</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Explication</label>
                                <textarea name="explication" class="form-control" rows="3" placeholder="Explication de la question..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Astuce</label>
                                <textarea name="astuce" class="form-control" rows="2" placeholder="Astuce pour la question..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Points</label>
                                <input type="number" name="points" class="form-control" value="1" min="1"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark">Média</label>
                                <input type="file" name="media_file" class="form-control">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-dark fw-semibold mb-3">
                            <i class="bx bx-list-check me-2"></i>Réponses
                        </h6>

                        <div id="reponses-container">
                            <!-- Les réponses seront ajoutées ici dynamiquement -->
                        </div>

                        <div class="text-end mt-3">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addReponse()">
                                <i class="bx bx-plus me-1"></i> Ajouter une réponse
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log('DOM chargé - initialisation des scripts');

            // DÉSACTIVER IMMÉDIATEMENT TOUS LES CHAMPS CACHÉS
            function disableAllHiddenFields() {
                console.log('Désactivation de tous les champs cachés...');

                // Désactiver les questions cachées
                document.querySelectorAll('.question-item[style*="display: none"]').forEach(question => {
                    question.querySelectorAll('input, select, textarea, button').forEach(field => {
                        field.disabled = true;
                        field.removeAttribute('required');
                    });
                });

                // Désactiver les réponses cachées
                document.querySelectorAll('.reponse-form[style*="display: none"]').forEach(reponse => {
                    reponse.querySelectorAll('input, select, textarea, button').forEach(field => {
                        field.disabled = true;
                        field.removeAttribute('required');
                    });
                });

                console.log('Tous les champs cachés désactivés');
            }

            // Exécuter immédiatement au chargement
            disableAllHiddenFields();

            // Initialiser le compteur
            const totalQuestions = document.querySelectorAll('.question-item').length;
            document.getElementById('currentItems').textContent = totalQuestions;

            // Gestion de l'ajout de réponses
            document.querySelectorAll('.add-reponse-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const qIndex = this.dataset.questionIndex;
                    const container = document.getElementById(`reponses-container-${qIndex}`);
                    const template = document.getElementById(`reponse-template-${qIndex}`);

                    if (!template) {
                        console.error('Template non trouvé pour la question:', qIndex);
                        return;
                    }

                    const existingReponses = container.querySelectorAll('.reponse-form').length;
                    const newIndex = existingReponses;

                    const newReponse = template.content.cloneNode(true);

                    newReponse.querySelectorAll('[name]').forEach(input => {
                        const name = input.getAttribute('name');
                        const newName = name.replace('__RINDEX__', newIndex);
                        input.setAttribute('name', newName);
                    });

                    container.appendChild(newReponse);

                    const newReponseElement = container.lastElementChild;
                    const removeBtn = newReponseElement.querySelector('.remove-reponse-btn');
                    if (removeBtn) {
                        removeBtn.addEventListener('click', function() {
                            newReponseElement.remove();
                        });
                    }
                });
            });

            // Gestion de la suppression des réponses
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-reponse-btn') ||
                    e.target.closest('.remove-reponse-btn')) {

                    const button = e.target.classList.contains('remove-reponse-btn') ?
                        e.target : e.target.closest('.remove-reponse-btn');
                    const reponseForm = button.closest('.reponse-form');

                    if (reponseForm) {
                        const deleteInput = reponseForm.querySelector('.delete-flag');
                        if (deleteInput) {
                            deleteInput.value = '1';

                            // Désactiver immédiatement tous les champs
                            reponseForm.querySelectorAll('input, select, textarea').forEach(field => {
                                field.disabled = true;
                                field.removeAttribute('required');
                            });

                            reponseForm.style.display = 'none';
                        } else {
                            reponseForm.remove();
                        }
                    }
                }
            });

            // Gestion de la suppression des questions
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-question-btn')) {
                    const button = e.target.closest('.remove-question-btn');
                    const questionItem = button.closest('.question-item');
                    const deleteInput = questionItem.querySelector('input.delete-flag');

                    if (deleteInput) {
                        deleteInput.value = '1';

                        // DÉSACTIVER IMMÉDIATEMENT TOUS LES CHAMPS
                        questionItem.querySelectorAll('input, select, textarea, button').forEach(field => {
                            field.disabled = true;
                            field.removeAttribute('required');
                        });

                        questionItem.style.display = 'none';

                        // Mettre à jour le compteur
                        const visibleItems = Array.from(document.querySelectorAll('.question-item'))
                            .filter(item => item.style.display !== 'none');
                        document.getElementById('currentItems').textContent = visibleItems.length;

                        console.log('Question marquée pour suppression et champs désactivés');
                    }
                }
            });

            // GESTION AMÉLIORÉE DE LA SOUMISSION
            const form = document.querySelector('form');
            if (form) {
                // Intercepter le clic sur le bouton submit
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.addEventListener('click', function(e) {
                        console.log('Clic sur le bouton submit - préparation des données');

                        // Désactiver tous les champs cachés AVANT la validation
                        disableAllHiddenFields();

                        // Forcer la désactivation une deuxième fois après un court délai
                        setTimeout(disableAllHiddenFields, 100);
                    });
                }

                // Également intercepter l'événement submit du formulaire
                form.addEventListener('submit', function(e) {
                    console.log('Événement submit - désactivation finale');
                    disableAllHiddenFields();

                    // Petit délai pour s'assurer que tout est désactivé
                    setTimeout(() => {
                        console.log('Soumission finale du formulaire');
                    }, 50);
                });
            }

            // [Le reste du code pour la pagination...]
            const itemsPerPage = 10;
            const items = Array.from(document.querySelectorAll(".question-item"));
            const totalItems = items.length;
            let filteredItems = [...items];
            let currentPage = 1;
            const maxVisiblePages = 5;

            const searchInput = document.getElementById("searchInput");
            const searchBtn = document.getElementById("searchBtn");
            const resetBtn = document.getElementById("resetBtn");
            const noResults = document.getElementById("noResults");

            function showPage(page, itemsToShow = filteredItems) {
                const start = (page - 1) * itemsPerPage;
                const end = page * itemsPerPage;

                items.forEach(item => item.style.display = "none");
                itemsToShow.forEach((item, index) => {
                    if (index >= start && index < end) {
                        item.style.display = "block";
                    }
                });

                noResults.classList.toggle("d-none", itemsToShow.length > 0);
                updatePaginationButtons(itemsToShow.length);

                // Réactiver les champs des questions visibles
                itemsToShow.forEach(item => {
                    if (item.style.display === "block") {
                        item.querySelectorAll('input, select, textarea').forEach(field => {
                            field.disabled = false;
                        });
                    }
                });
            }

            function updatePaginationButtons(itemCount = filteredItems.length) {
                const container = document.getElementById("paginationNumbersWrapper");
                const totalPages = Math.ceil(itemCount / itemsPerPage);

                while (container.children.length > 2) {
                    container.removeChild(container.children[1]);
                }

                let startPage = Math.floor((currentPage - 1) / maxVisiblePages) * maxVisiblePages + 1;
                let endPage = Math.min(startPage + maxVisiblePages - 1, totalPages);

                for (let i = startPage; i <= endPage; i++) {
                    const li = document.createElement("li");
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;

                    const a = document.createElement("a");
                    a.href = "#";
                    a.className = "page-link";
                    a.innerText = i;
                    a.addEventListener("click", (e) => {
                        e.preventDefault();
                        currentPage = i;
                        showPage(currentPage);
                    });

                    li.appendChild(a);
                    container.insertBefore(li, document.getElementById("nextPage"));
                }

                const prev = document.getElementById("prevPage");
                const next = document.getElementById("nextPage");

                prev.classList.toggle("disabled", currentPage === 1);
                next.classList.toggle("disabled", currentPage === totalPages);

                document.getElementById('currentItems').textContent = itemCount;
            }

            // [Les écouteurs d'événements pour la pagination...]
            const prevPageBtn = document.getElementById("prevPage");
            const nextPageBtn = document.getElementById("nextPage");

            if (prevPageBtn) {
                prevPageBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                    }
                });
            }

            if (nextPageBtn) {
                nextPageBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
                    if (currentPage < totalPages) {
                        currentPage++;
                        showPage(currentPage);
                    }
                });
            }

            if (searchBtn) {
                searchBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    const query = searchInput.value.trim().toLowerCase();

                    filteredItems = items.filter(item => {
                        const questionTextElement = item.querySelector('.question-content h6');
                        if (!questionTextElement) return false;

                        const questionText = questionTextElement.textContent.toLowerCase();
                        return questionText.includes(query);
                    });

                    currentPage = 1;
                    showPage(currentPage);
                });
            }

            if (resetBtn) {
                resetBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    if (searchInput) searchInput.value = "";
                    filteredItems = [...items];
                    currentPage = 1;
                    showPage(currentPage);
                });
            }

            showPage(currentPage);
        });

        // Fonctions pour le modal de nouvelle question
        let reponseIndex = 1;

        function addReponse() {
            const container = document.getElementById('reponses-container');
            if (!container) return;

            const html = `
            <div class="row g-3 reponse-item mb-3 py-3" style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">
                <div class="col-md-6">
                    <label>Texte de la réponse</label>
                    <input type="text" name="reponses[${reponseIndex}][text]" class="form-control" placeholder="Texte de la réponse" required>
                </div>
                <div class="col-md-6">
                    <label>Bonne réponse ?</label>
                    <select name="reponses[${reponseIndex}][is_correct]" class="form-select" required>
                        <option value="0">Non</option>
                        <option value="1">Oui</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Position</label>
                    <input type="number" name="reponses[${reponseIndex}][position]" class="form-control" placeholder="Position">
                </div>
                <div class="col-md-6">
                    <label>Match pair</label>
                    <input type="text" name="reponses[${reponseIndex}][match_pair]" class="form-control" placeholder="Match pair">
                </div>
                <div class="col-md-6">
                    <label>Groupe</label>
                    <input type="text" name="reponses[${reponseIndex}][bank_group]" class="form-control" placeholder="Groupe banque">
                </div>
                <div class="col-md-6">
                    <label>Dos de la flashcard</label>
                    <input type="text" name="reponses[${reponseIndex}][flashcard_back]" class="form-control" placeholder="Dos de la flashcard">
                </div>
                <div class="col-md-12">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeReponse(this)">Supprimer</button>
                </div>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', html);
            reponseIndex++;
        }

        function removeReponse(button) {
            button.closest('.reponse-item').remove();
        }
    </script>

    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .question-item[style*="display: none"] {
            display: none !important;
        }

        .reponse-form[style*="display: none"] {
            display: none !important;
        }

        template {
            display: none !important;
        }

        .badge {
            border-radius: 4px;
            font-weight: 500;
        }

        .modal-content {
            border-radius: 12px;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
        }

        .alert {
            border-radius: 8px;
        }

        .accordion-button {
            border-radius: 8px !important;
        }

        .accordion-button:not(.collapsed) {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .search-box {
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }

        .rounded-3 {
            border-radius: 0.75rem !important;
        }

        .question-item {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .question-item:hover {
            transform: translateY(-2px);
        }

        .reponses-grid {
            display: grid;
            gap: 1rem;
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
    </style>
@endsection
