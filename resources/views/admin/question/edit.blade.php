@extends('admin.layout')
@section('title', 'Modifier une Question')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-edit me-2"></i>Modification d'une question
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('quiz.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Quiz
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Modifier la question
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('quiz.index') }}" class="btn btn-outline-primary">
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
                        <form action="{{ route('question.update', $question->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="alert alert-danger border-0 alert-dismissible fade show mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-error-circle me-2 fs-5"></i>
                                        <span class="fw-medium">Veuillez corriger les erreurs ci-dessous</span>
                                    </div>
                                    <ul class="mt-2 mb-0 ps-4">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Section Informations de la question -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-question-mark me-2"></i>Informations de la question
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="text" class="form-label fw-semibold text-dark">Texte de la
                                                    question</label>
                                                <textarea name="text" id="text" class="form-control @error('text') is-invalid @enderror" rows="3"
                                                    placeholder="Entrez le texte de la question..." required>{{ old('text', $question->text) }}</textarea>
                                                @error('text')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="points"
                                                    class="form-label fw-semibold text-dark">Points</label>
                                                <input type="number" name="points" id="points"
                                                    class="form-control @error('points') is-invalid @enderror"
                                                    value="{{ old('points', $question->points) }}" min="1"
                                                    max="100" required>
                                                @error('points')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label fw-semibold text-dark">Type de
                                                    question</label>
                                                <select name="type" id="type"
                                                    class="form-select @error('type') is-invalid @enderror" required>
                                                    <option value="">Sélectionner un type</option>
                                                    <option value="multiple"
                                                        {{ old('type', $question->type) == 'multiple' ? 'selected' : '' }}>
                                                        Choix multiple</option>
                                                    <option value="unique"
                                                        {{ old('type', $question->type) == 'unique' ? 'selected' : '' }}>
                                                        Choix unique</option>
                                                    <option value="vrai_faux"
                                                        {{ old('type', $question->type) == 'vrai_faux' ? 'selected' : '' }}>
                                                        Vrai/Faux</option>
                                                    <option value="texte"
                                                        {{ old('type', $question->type) == 'texte' ? 'selected' : '' }}>
                                                        Réponse texte</option>
                                                </select>
                                                @error('type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="media_url" class="form-label fw-semibold text-dark">Média
                                                    (image/vidéo)</label>
                                                <input type="file" name="media_url" id="media_url"
                                                    class="form-control @error('media_url') is-invalid @enderror"
                                                    accept="image/*,video/*">
                                                <div class="form-text">Formats acceptés : JPG, PNG, GIF, MP4</div>
                                                @error('media_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror

                                                @if ($question->media_url)
                                                    <div class="mt-2">
                                                        <span class="fw-medium text-dark">Média actuel :</span>
                                                        <a href="{{ asset($question->media_url) }}" target="_blank"
                                                            class="text-decoration-none ms-2">
                                                            <i class="bx bx-show me-1"></i>Voir le média
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Aide et explications -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-info-circle me-2"></i>Aide et explications
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="explication"
                                                    class="form-label fw-semibold text-dark">Explication</label>
                                                <textarea name="explication" id="explication" class="form-control @error('explication') is-invalid @enderror"
                                                    rows="3" placeholder="Expliquez la réponse correcte...">{{ old('explication', $question->explication) }}</textarea>
                                                @error('explication')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="astuce"
                                                    class="form-label fw-semibold text-dark">Astuce</label>
                                                <textarea name="astuce" id="astuce" class="form-control @error('astuce') is-invalid @enderror" rows="3"
                                                    placeholder="Donnez un indice pour aider...">{{ old('astuce', $question->astuce) }}</textarea>
                                                @error('astuce')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Réponses -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-list-check me-2"></i>Réponses
                                        <span class="badge bg-primary ms-2">{{ $question->reponses->count() }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @foreach ($question->reponses as $index => $reponse)
                                        <div class="card border mb-3">
                                            <div class="card-header bg-white py-2">
                                                <h6 class="mb-0 fw-semibold text-dark">
                                                    Réponse #{{ $index + 1 }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-dark">Texte de la
                                                                réponse</label>
                                                            <input type="text"
                                                                name="reponses[{{ $reponse->id }}][text]"
                                                                value="{{ old("reponses.{$reponse->id}.text", $reponse->text) }}"
                                                                class="form-control @error("reponses.{$reponse->id}.text") is-invalid @enderror"
                                                                placeholder="Entrez le texte de la réponse..." required>
                                                            @error("reponses.{$reponse->id}.text")
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-semibold text-dark d-block">Réponse
                                                                correcte</label>
                                                            <!-- Trick pour forcer l'envoi de la case même si décochée -->
                                                            <input type="hidden"
                                                                name="reponses[{{ $reponse->id }}][is_correct]"
                                                                value="0">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="reponses[{{ $reponse->id }}][is_correct]"
                                                                    value="1"
                                                                    {{ old("reponses.{$reponse->id}.is_correct", $reponse->is_correct) ? 'checked' : '' }}
                                                                    role="switch">
                                                                <label class="form-check-label fw-medium">
                                                                    {{ $reponse->is_correct ? 'Correcte' : 'Incorrecte' }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Boutons de soumission -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 me-3">
                                    <i class="bx bx-save me-2"></i> Mettre à jour la question
                                </button>
                                <a href="{{ route('quiz.index') }}" class="btn btn-outline-secondary px-5 py-2">
                                    <i class="bx bx-x me-2"></i> Annuler
                                </a>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du type de question
            const typeSelect = document.getElementById('type');

            function updateAnswerFields() {
                const type = typeSelect.value;
                // Vous pouvez ajouter ici une logique pour adapter l'interface
                // selon le type de question sélectionné
                console.log('Type de question:', type);
            }

            typeSelect.addEventListener('change', updateAnswerFields);
            updateAnswerFields(); // Initialisation
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

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
        }

        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .card .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
@endsection
