@extends('admin.layout')
@section('title', 'Détails du commercial')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-user-pin me-2"></i>Détails du commercial
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('commercials.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Commerciaux
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $commercial->user->name }} {{ $commercial->prenom }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('commercials.edit', $commercial->id) }}" class="btn btn-warning">
                            <i class="bx bx-edit me-1"></i> Modifier
                        </a>
                        <a href="{{ route('commercials.index') }}" class="btn btn-outline-primary ms-2">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Carte principale des informations -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <!-- Profile Image Section -->
                            @if ($commercial->user->image)
                                <img src="{{ asset($commercial->user->image) }}" class="rounded-circle shadow"
                                    width="160" height="160" alt="Avatar" style="object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                    style="width:160px;height:160px;font-weight:bold;font-size:32px;">
                                    {{ strtoupper(substr($commercial->user->name, 0, 1)) }}{{ strtoupper(substr($commercial->prenom, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="mt-3 mb-2 text-dark fw-bold">{{ $commercial->user->name }} {{ $commercial->prenom }}
                            </h3>
                            <span class="badge bg-primary px-3 py-2 fs-6">{{ ucfirst($commercial->user->role) }}</span>
                            @if ($commercial->civilite)
                                <div class="mt-2">
                                    <span class="text-muted">{{ $commercial->civilite }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Informations du commercial -->
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-info-circle me-2"></i>Informations personnelles
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Nom :</div>
                                            <div class="col-sm-8">{{ $commercial->user->name }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Prénom :</div>
                                            <div class="col-sm-8">{{ $commercial->prenom }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Email :</div>
                                            <div class="col-sm-8">
                                                <a href="mailto:{{ $commercial->user->email }}"
                                                    class="text-decoration-none text-dark">
                                                    <i class="bx bx-envelope me-1"></i>{{ $commercial->user->email }}
                                                </a>
                                            </div>
                                        </div>
                                        @if ($commercial->telephone)
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-semibold text-dark">Téléphone :</div>
                                                <div class="col-sm-8">
                                                    <a href="tel:{{ $commercial->telephone }}"
                                                        class="text-decoration-none text-dark">
                                                        <i class="bx bx-phone me-1"></i>{{ $commercial->telephone }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Stagiaires associés -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-group me-2"></i>Stagiaires associés
                            <span class="badge bg-info text-dark ms-2">{{ $commercial->stagiaires->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($commercial->stagiaires->count() > 0)
                            <div class="accordion" id="stagiairesAccordion">
                                @foreach ($commercial->stagiaires as $key => $stagiaire)
                                    <div class="accordion-item border-0 mb-3">
                                        <h2 class="accordion-header" id="heading{{ $key }}">
                                            <button class="accordion-button collapsed shadow-sm p-3" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}"
                                                aria-expanded="false" aria-controls="collapse{{ $key }}"
                                                style="background: #3D9BE9; color: white; border-radius: 8px;">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bx bx-user-circle me-3 fs-4"></i>
                                                        <div>
                                                            <strong>{{ $stagiaire->user->name }}
                                                                {{ $stagiaire->prenom }}</strong>
                                                            @if ($stagiaire->user->email)
                                                                <div class="small">{{ $stagiaire->user->email }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-light text-dark">
                                                        Stagiaire {{ $key + 1 }}
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $key }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $key }}"
                                            data-bs-parent="#stagiairesAccordion">
                                            <div class="accordion-body bg-light">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="fw-semibold text-dark mb-3">Informations personnelles
                                                        </h6>
                                                        <div class="mb-2">
                                                            <strong>Adresse :</strong>
                                                            <span
                                                                class="text-muted">{{ $stagiaire->adresse ?? 'Non renseignée' }}</span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <strong>Téléphone :</strong>
                                                            <span
                                                                class="text-muted">{{ $stagiaire->telephone ?? 'Non renseigné' }}</span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <strong>Date de naissance :</strong>
                                                            <span
                                                                class="text-muted">{{ $stagiaire->date_naissance ? \Carbon\Carbon::parse($stagiaire->date_naissance)->format('d/m/Y') : 'Non renseignée' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="fw-semibold text-dark mb-3">Localisation</h6>
                                                        <div class="mb-2">
                                                            <strong>Ville :</strong>
                                                            <span
                                                                class="text-muted">{{ $stagiaire->ville ?? 'Non renseignée' }}</span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <strong>Code postal :</strong>
                                                            <span
                                                                class="text-muted">{{ $stagiaire->code_postal ?? 'Non renseigné' }}</span>
                                                        </div>
                                                        <div class="mt-3">
                                                            <a href="{{ route('stagiaires.show', $stagiaire->id) }}"
                                                                class="btn btn-sm btn-info text-white">
                                                                <i class="bx bx-show me-1"></i>Voir le profil
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-user-plus fs-1 text-muted mb-3"></i>
                                <p class="text-muted mb-0">Aucun stagiaire associé à ce commercial</p>
                                <a href="{{ route('commercials.edit', $commercial->id) }}" class="btn btn-primary mt-3">
                                    <i class="bx bx-link me-1"></i>Associer des stagiaires
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body text-center">
                        <div class="btn-group">
                            <a href="{{ route('commercials.edit', $commercial->id) }}" class="btn btn-warning">
                                <i class="bx bx-edit me-1"></i> Modifier
                            </a>
                            <form action="{{ route('commercials.destroy', $commercial->id) }}" method="POST"
                                class="d-inline ms-2"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commercial ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger">
                                    <i class="bx bx-trash me-1"></i> Supprimer
                                </button>
                            </form>
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

    .accordion-button {
        border-radius: 8px !important;
        font-weight: 500;
    }

    .accordion-button:not(.collapsed) {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .accordion-body {
        border-radius: 0 0 8px 8px;
    }
</style>
