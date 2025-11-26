@extends('admin.layout')
@section('title', 'Détails du catalogue de formations')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-book-open me-2"></i>Détails du catalogue de formations
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('catalogue_formation.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Catalogue de formations
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $catalogueFormations->titre }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('catalogue_formation.edit', $catalogueFormations->id) }}" class="btn btn-warning">
                            <i class="bx bx-edit me-1"></i> Modifier
                        </a>
                        <a href="{{ route('catalogue_formation.index') }}" class="btn btn-outline-primary ms-2">
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
                            <i class="bx bx-info-circle me-2"></i>Détails de la formation :
                            {{ $catalogueFormations->titre }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Colonne Informations -->
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
                                            <div class="col-sm-8">{{ $catalogueFormations->titre }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Durée :</div>
                                            <div class="col-sm-8">
                                                <span class="badge bg-primary">{{ $catalogueFormations->duree }}
                                                    heures</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Tarif :</div>
                                            <div class="col-sm-8">
                                                <span
                                                    class="fw-semibold text-dark">{{ number_format($catalogueFormations->tarif, 2) }}
                                                    €</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Statut :</div>
                                            <div class="col-sm-8">
                                                @if ($catalogueFormations->statut == '1')
                                                    <span class="badge bg-success">Actif</span>
                                                @elseif ($catalogueFormations->statut == '0')
                                                    <span class="badge bg-secondary">Inactif</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Certification :</div>
                                            <div class="col-sm-8">{{ $catalogueFormations->certification ?? 'Aucune' }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Formation associée :</div>
                                            <div class="col-sm-8">
                                                <span
                                                    class="badge bg-info text-dark">{{ $formation->titre ?? 'Aucune' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-map me-2"></i>Localisation & Public
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Lieu :</div>
                                            <div class="col-sm-8">{{ $catalogueFormations->lieu ?? 'Non renseigné' }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Niveau :</div>
                                            <div class="col-sm-8">{{ $catalogueFormations->niveau ?? 'Non renseigné' }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Public cible :</div>
                                            <div class="col-sm-8">
                                                {{ $catalogueFormations->public_cible ?? 'Non renseigné' }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4 fw-semibold text-dark">Participants max :</div>
                                            <div class="col-sm-8">{{ $catalogueFormations->nombre_participants ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne Description & Médias -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-book-content me-2"></i>Description
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-muted">
                                            {!! $catalogueFormations->description ?? 'Aucune description fournie.' !!}
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Médias -->
                                <div class="card border-0 bg-light mb-4">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-image me-2"></i>Médias
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if ($catalogueFormations->image_url)
                                            @php
                                                $extension = strtolower(
                                                    pathinfo($catalogueFormations->image_url, PATHINFO_EXTENSION),
                                                );
                                            @endphp

                                            <div class="text-center">
                                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                    <img src="{{ asset($catalogueFormations->image_url) }}"
                                                        alt="Image de la formation" class="img-fluid rounded shadow-sm"
                                                        style="max-height: 200px;">
                                                @elseif (in_array($extension, ['mp4', 'webm', 'ogg']))
                                                    <video controls class="rounded shadow-sm"
                                                        style="max-height: 200px; width: 100%;">
                                                        <source
                                                            src="{{ asset('storage/' . $catalogueFormations->image_url) }}"
                                                            type="video/{{ $extension }}">
                                                        Votre navigateur ne supporte pas la lecture de vidéos.
                                                    </video>
                                                @elseif (in_array($extension, ['mp3', 'wav', 'ogg']))
                                                    <div class="card p-3 rounded shadow-sm bg-light">
                                                        <div class="mb-2 text-center">
                                                            <i class="bx bx-music fs-1 text-primary"></i>
                                                        </div>
                                                        <audio controls class="w-100">
                                                            <source src="{{ asset($catalogueFormations->image_url) }}"
                                                                type="audio/{{ $extension }}">
                                                            Votre navigateur ne supporte pas la lecture d'audio.
                                                        </audio>
                                                    </div>
                                                @else
                                                    <a href="{{ asset($catalogueFormations->image_url) }}"
                                                        target="_blank" class="btn btn-outline-primary">
                                                        <i class="bx bx-download me-1"></i>Télécharger le fichier
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-center py-4">
                                                <i class="bx bx-image fs-1 text-muted mb-3"></i>
                                                <p class="text-muted mb-0">Aucun média disponible</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section PDF -->
                        <div class="card border-0 bg-light">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h6 class="mb-0 text-dark fw-semibold">
                                    <i class="bx bx-file me-2"></i>Cursus PDF
                                </h6>
                            </div>
                            <div class="card-body">
                                @if ($catalogueFormations->cursus_pdf)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex gap-2 flex-wrap align-items-center mb-3">
                                                <a href="{{ asset($catalogueFormations->cursus_pdf) }}" target="_blank"
                                                    class="btn btn-primary text-white d-flex align-items-center">
                                                    <i class="bx bx-link-external me-2"></i> Ouvrir le PDF
                                                </a>
                                                <a href="{{ route('catalogue_formation.download-pdf', $catalogueFormations->id) }}"
                                                    class="btn btn-outline-primary d-flex align-items-center">
                                                    <i class="bx bx-download me-2"></i> Télécharger
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <div class="border rounded">
                                                <iframe src="{{ asset($catalogueFormations->cursus_pdf) }}"
                                                    style="width:100%;min-height:400px;max-height:500px;border:none;"
                                                    title="PDF Viewer" allowfullscreen>
                                                </iframe>
                                            </div>
                                            <div class="text-center text-muted mt-2 small">
                                                Si le PDF ne s'affiche pas, <a
                                                    href="{{ asset($catalogueFormations->cursus_pdf) }}"
                                                    target="_blank">cliquez ici pour l'ouvrir dans un nouvel onglet</a>.
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="bx bx-file fs-1 text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Aucun PDF disponible</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-body text-center">
                                <div class="btn-group">
                                    <a href="{{ route('catalogue_formation.edit', $catalogueFormations->id) }}"
                                        class="btn btn-warning">
                                        <i class="bx bx-edit me-1"></i> Modifier
                                    </a>
                                    <a href="{{ route('catalogue_formation.index') }}"
                                        class="btn btn-outline-primary ms-2">
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
