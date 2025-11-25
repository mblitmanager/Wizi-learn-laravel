@extends('admin.layout')
@section('title', 'Détails du formateur')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-user-voice me-2"></i>Détails du formateur
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('formateur.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Formateurs
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $formateur->user->name }} {{ $formateur->prenom }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('formateur.edit', $formateur->id) }}" class="btn btn-warning">
                            <i class="bx bx-edit me-1"></i> Modifier
                        </a>
                        <a href="{{ route('formateur.index') }}" class="btn btn-outline-primary ms-2">
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
                            @if (isset($formateur->user->image) && $formateur->user->image)
                                <img src="{{ asset($formateur->user->image) }}" class="rounded-circle shadow" width="160"
                                    height="160" alt="Avatar" style="object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                    style="width:160px;height:160px;font-weight:bold;font-size:32px;">
                                    {{ strtoupper(substr($formateur->user->name, 0, 1)) }}{{ strtoupper(substr($formateur->prenom, 0, 1)) }}
                                </div>
                            @endif
                            <h3 class="mt-3 mb-2 text-dark fw-bold">{{ $formateur->user->name }} {{ $formateur->prenom }}
                            </h3>
                            <span class="badge bg-primary px-3 py-2 fs-6">{{ ucfirst($formateur->user->role) }}</span>
                            @if ($formateur->civilite)
                                <div class="mt-2">
                                    <span class="text-muted">{{ $formateur->civilite }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Informations du formateur -->
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
                                            <div class="col-sm-8">{{ $formateur->user->name }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Prénom :</div>
                                            <div class="col-sm-8">{{ $formateur->prenom }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Email :</div>
                                            <div class="col-sm-8">
                                                <a href="mailto:{{ $formateur->user->email }}"
                                                    class="text-decoration-none text-dark">
                                                    <i class="bx bx-envelope me-1"></i>{{ $formateur->user->email }}
                                                </a>
                                            </div>
                                        </div>
                                        @if ($formateur->telephone)
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-semibold text-dark">Téléphone :</div>
                                                <div class="col-sm-8">
                                                    <a href="tel:{{ $formateur->telephone }}"
                                                        class="text-decoration-none text-dark">
                                                        <i class="bx bx-phone me-1"></i>{{ $formateur->telephone }}
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
                @if ($formateur->stagiaires->count() > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent border-bottom-0 py-3">
                            <h6 class="mb-0 text-dark fw-semibold">
                                <i class="bx bx-group me-2"></i>Stagiaires associés
                                <span class="badge bg-info text-dark ms-2">{{ $formateur->stagiaires->count() }}</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">Nom & Prénom</th>
                                            <th class="border-0">Email</th>
                                            <th class="border-0">Téléphone</th>
                                            <th class="border-0 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($formateur->stagiaires as $stagiaire)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark">
                                                        {{ strtoupper($stagiaire->user->name) }} {{ $stagiaire->prenom }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($stagiaire->user->email)
                                                        <a href="mailto:{{ $stagiaire->user->email }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ $stagiaire->user->email }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($stagiaire->telephone)
                                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $stagiaire->telephone) }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ $stagiaire->telephone }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('stagiaires.show', $stagiaire->id) }}"
                                                        class="btn btn-sm btn-info text-white">
                                                        <i class="bx bx-show me-1"></i>Voir
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Section Formations -->
                <!-- Section Formations -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-book-reader me-2"></i>Formations associées
                            <span class="badge bg-primary ms-2">{{ $formateur->catalogue_formations->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($formateur->catalogue_formations->count() > 0)
                            <div class="accordion" id="formationsAccordion">
                                @foreach ($formateur->catalogue_formations as $key => $catalogueFormation)
                                    @php
                                        // Récupérer la catégorie depuis la formation parente
                                        $categorie = $catalogueFormation->formation->categorie ?? 'Non spécifiée';

                                        $bgColor = '';
                                        switch ($categorie) {
                                            case 'Bureautique':
                                                $bgColor = '#3D9BE9';
                                                break;
                                            case 'Langues':
                                                $bgColor = '#A55E6E';
                                                break;
                                            case 'Internet':
                                                $bgColor = '#FFC533';
                                                break;
                                            case 'Création':
                                                $bgColor = '#9392BE';
                                                break;
                                            default:
                                                $bgColor = '#6c757d';
                                        }
                                    @endphp
                                    <div class="accordion-item border-0 mb-3">
                                        <h2 class="accordion-header" id="heading{{ $key }}">
                                            <button class="accordion-button collapsed shadow-sm p-3" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}"
                                                aria-expanded="false" aria-controls="collapse{{ $key }}"
                                                style="background: {{ $bgColor }}; color: white; border-radius: 8px;">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <div>
                                                        <i class="bx bx-book-open me-2"></i>
                                                        <strong>{{ $catalogueFormation->titre }}</strong>
                                                    </div>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $categorie }}
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $key }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $key }}"
                                            data-bs-parent="#formationsAccordion">
                                            <div class="accordion-body bg-light">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="fw-semibold text-dark mb-2">Description :</h6>
                                                        <div class="text-muted">
                                                            {!! $catalogueFormation->description ?? 'Aucune description disponible' !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="fw-semibold text-dark mb-2">Informations :</h6>
                                                        <div class="mb-2">
                                                            <strong>Durée :</strong>
                                                            <span
                                                                class="text-muted">{{ $catalogueFormation->duree ?? 'Non spécifiée' }}</span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <strong>Catégorie :</strong>
                                                            <span class="badge"
                                                                style="background: {{ $bgColor }}; color: white;">
                                                                {{ $categorie }}
                                                            </span>
                                                        </div>
                                                        @if ($catalogueFormation->formation)
                                                            <div class="mb-2">
                                                                <strong>Formation parente :</strong>
                                                                <span
                                                                    class="text-muted">{{ $catalogueFormation->formation->titre }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bx bx-book-open fs-1 text-muted mb-3"></i>
                                <p class="text-muted mb-0">Aucune formation associée</p>
                                <a href="{{ route('formateur.edit', $formateur->id) }}" class="btn btn-primary mt-3">
                                    <i class="bx bx-link me-1"></i>Associer des formations
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body text-center">
                        <div class="">
                            <a href="{{ route('formateur.edit', $formateur->id) }}" class="btn btn-warning">
                                <i class="bx bx-edit me-1"></i> Modifier
                            </a>
                            <form action="{{ route('formateur.destroy', $formateur->id) }}" method="POST"
                                class="d-inline ms-2"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?');">
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

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.04) !important;
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
