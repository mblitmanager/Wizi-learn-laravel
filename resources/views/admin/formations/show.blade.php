@extends('admin.layout')
@section('title', 'Détails du domaine de formation')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-book-open me-2"></i>Détails du domaine de formation
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('formations.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Domaines de formation
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $formation->titre }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('formations.edit', $formation->id) }}" class="btn btn-warning">
                            <i class="bx bx-edit me-1"></i> Modifier
                        </a>
                        <a href="{{ route('formations.index') }}" class="btn btn-outline-primary ms-2">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Carte principale des informations -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-info-circle me-2"></i>Détails du domaine : {{ $formation->titre }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Colonne Informations principales -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-detail me-2"></i>Informations générales
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Titre :</div>
                                            <div class="col-sm-8">
                                                <span class="fw-medium text-dark">{{ $formation->titre }}</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Catégorie :</div>
                                            <div class="col-sm-8">
                                                <span class="badge bg-info text-dark">{{ $formation->categorie }}</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Durée :</div>
                                            <div class="col-sm-8">
                                                <span class="badge bg-primary">{{ $formation->duree }} heures</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">ID :</div>
                                            <div class="col-sm-8">
                                                <span class="text-muted">#{{ $formation->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne Métadonnées -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-calendar me-2"></i>Métadonnées
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Créé le :</div>
                                            <div class="col-sm-8">
                                                <span
                                                    class="text-dark">{{ $formation->created_at->format('d/m/Y à H:i') }}</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Modifié le :</div>
                                            <div class="col-sm-8">
                                                <span
                                                    class="text-dark">{{ $formation->updated_at->format('d/m/Y à H:i') }}</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Statut :</div>
                                            <div class="col-sm-8">
                                                <span class="badge bg-success">Actif</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Description -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h6 class="mb-0 text-dark fw-semibold">
                                    <i class="bx bx-book-content me-2"></i>Description
                                </h6>
                            </div>
                            <div class="card-body">
                                @if ($formation->description)
                                    <div class="text-muted">
                                        {!! $formation->description !!}
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="bx bx-note fs-1 text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucune description disponible</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-body text-center">
                                <div class="btn-group">
                                    <a href="{{ route('formations.edit', $formation->id) }}" class="btn btn-warning">
                                        <i class="bx bx-edit me-1"></i> Modifier
                                    </a>
                                    <a href="{{ route('formations.index') }}" class="btn btn-outline-primary ms-2">
                                        <i class="bx bx-arrow-back me-1"></i> Retour à la liste
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection

<style>
    .card {
        border-radius: 12px;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .badge {
        border-radius: 6px;
        font-weight: 500;
    }
</style>
