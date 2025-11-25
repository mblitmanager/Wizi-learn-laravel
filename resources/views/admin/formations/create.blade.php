@extends('admin.layout')
@section('title', 'Créer un domaine de formation')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-book-open me-2"></i>Création d'un domaine de formation
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('formations.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Domaines de formation
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Création
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('formations.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if ($errors->any())
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">Veuillez corriger les erreurs ci-dessous</span>
                </div>
                <ul class="mt-2 mb-0 ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form class="row g-3" action="{{ route('formations.store') }}" method="POST">
                            @csrf

                            <!-- Section Informations générales -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-info-circle me-2"></i>Informations générales
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="titre" class="form-label fw-semibold text-dark">Titre</label>
                                                <input type="text" name="titre" id="titre"
                                                    class="form-control @error('titre') is-invalid @enderror"
                                                    value="{{ old('titre') }}" placeholder="Entrez le titre du domaine"
                                                    required>
                                                @error('titre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="categorie"
                                                    class="form-label fw-semibold text-dark">Catégorie</label>
                                                <input type="text" name="categorie" id="categorie"
                                                    class="form-control @error('categorie') is-invalid @enderror"
                                                    value="{{ old('categorie') }}" placeholder="Entrez la catégorie">
                                                @error('categorie')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="duree" class="form-label fw-semibold text-dark">Durée
                                                    (heures)</label>
                                                <input type="number" name="duree" id="duree"
                                                    class="form-control @error('duree') is-invalid @enderror"
                                                    value="{{ old('duree') }}" placeholder="Durée en heures" min="1"
                                                    required>
                                                @error('duree')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="statut"
                                                    class="form-label fw-semibold text-dark">Statut</label>
                                                <select name="statut" id="statut"
                                                    class="form-select @error('statut') is-invalid @enderror" required>
                                                    <option value="">Sélectionnez un statut</option>
                                                    <option value="1" {{ old('statut') == '1' ? 'selected' : '' }}>
                                                        Actif</option>
                                                    <option value="0" {{ old('statut') == '0' ? 'selected' : '' }}>
                                                        Inactif</option>
                                                </select>
                                                @error('statut')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Description -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-book-content me-2"></i>Description
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="description"
                                                    class="form-label fw-semibold text-dark">Description</label>
                                                <textarea name="description" id="description" rows="4"
                                                    class="form-control @error('description') is-invalid @enderror" placeholder="Décrivez le domaine de formation">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons de soumission -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 me-3">
                                    <i class="bx bx-save me-2"></i> Créer le domaine
                                </button>
                                <a href="{{ route('formations.index') }}" class="btn btn-outline-secondary px-5 py-2">
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
        $(document).ready(function() {
            // Initialisation des tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection

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
</style>
