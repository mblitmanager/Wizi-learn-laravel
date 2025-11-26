@extends('admin.layout')
@section('title', 'Détails du Média')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-show me-2"></i>Détails du média
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Tableau de bord
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('medias.index') }}" class="text-decoration-none">
                                        Médias
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Détails
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('medias.edit', $media->id) }}" class="btn btn-outline-warning">
                            <i class="bx bx-edit me-1"></i> Modifier
                        </a>
                        <a href="{{ route('medias.index') }}" class="btn btn-outline-primary ms-2">
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

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Section Aperçu du média -->
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-play-circle me-2"></i>Aperçu du média
                                        </h6>
                                    </div>
                                    <div class="card-body d-flex justify-content-center align-items-center p-4">
                                        @php
                                            // Détection des URLs YouTube
                                            $isYoutubeUrl = false;
                                            $youtubeId = null;
                                            if (filter_var($media->url, FILTER_VALIDATE_URL)) {
                                                $parsedUrl = parse_url($media->url);
                                                $host = strtolower($parsedUrl['host'] ?? '');

                                                if (
                                                    str_contains($host, 'youtube.com') ||
                                                    str_contains($host, 'youtu.be')
                                                ) {
                                                    $isYoutubeUrl = true;

                                                    // Extraction de l'ID vidéo
        if (str_contains($media->url, 'watch?v=')) {
            parse_str($parsedUrl['query'] ?? '', $query);
            $youtubeId = $query['v'] ?? null;
        } elseif (str_contains($media->url, 'youtu.be/')) {
            $youtubeId = substr($parsedUrl['path'] ?? '', 1);
        } elseif (str_contains($media->url, '/embed/')) {
            $parts = explode('/embed/', $media->url);
            $youtubeId = $parts[1] ?? null;
        } elseif (str_contains($media->url, '/shorts/')) {
            $parts = explode('/shorts/', $media->url);
            $youtubeId = $parts[1] ?? null;
        }

        // Nettoyage de l'ID
                                                    if ($youtubeId) {
                                                        $youtubeId = strtok($youtubeId, '?&');
                                                    }
                                                }
                                            }

                                            // Déterminer le type de média
                                            $isAudio =
                                                Str::startsWith($media->type, 'audio') || $media->type === 'audio';
                                            $isImage = Str::startsWith($media->type, 'image');
                                            $isVideo = Str::startsWith($media->type, 'video');
                                        @endphp

                                        @if ($isYoutubeUrl && $youtubeId)
                                            <div class="w-100">
                                                <div class="ratio ratio-16x9">
                                                    <iframe
                                                        src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=0&rel=0&modestbranding=1"
                                                        title="{{ $media->titre }}"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        allowfullscreen style="border-radius: 8px; border: none;"
                                                        loading="lazy">
                                                    </iframe>
                                                </div>
                                                <div class="text-center mt-3">
                                                    <a href="https://www.youtube.com/watch?v={{ $youtubeId }}"
                                                        target="_blank" class="btn btn-sm btn-danger">
                                                        <i class="bx bxl-youtube me-2"></i>Voir sur YouTube
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif ($isImage)
                                            <div class="text-center">
                                                <img src="{{ asset($media->url) }}" alt="{{ $media->titre }}"
                                                    class="img-fluid rounded shadow-sm media-preview"
                                                    style="max-height: 300px; object-fit: contain;">
                                                <div class="mt-3">
                                                    <a href="{{ asset($media->url) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-zoom-in me-2"></i>Agrandir l'image
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif ($isVideo)
                                            <div class="w-100">
                                                <video controls class="w-100 rounded shadow-sm"
                                                    style="max-height: 300px; background: #000;">
                                                    <source src="{{ asset($media->url) }}" type="video/mp4">
                                                    Votre navigateur ne supporte pas la lecture de vidéos.
                                                </video>
                                                <div class="text-center mt-3">
                                                    <a href="{{ asset($media->url) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-download me-2"></i>Télécharger la vidéo
                                                    </a>
                                                </div>
                                            </div>
                                        @elseif ($isAudio)
                                            <div class="w-100 text-center">
                                                <div class="mb-3">
                                                    <i class="bx bx-music bx-lg text-primary"></i>
                                                </div>
                                                <audio controls class="w-100">
                                                    <source src="{{ asset($media->url) }}" type="audio/mpeg">
                                                    Votre navigateur ne supporte pas la lecture audio.
                                                </audio>
                                                <div class="mt-3">
                                                    <a href="{{ asset($media->url) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bx bx-download me-2"></i>Télécharger l'audio
                                                    </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <i class="bx bx-file bx-lg text-muted mb-3"></i>
                                                <p class="text-muted mb-3">Document média</p>
                                                <a href="{{ asset($media->url) }}" target="_blank"
                                                    class="btn btn-outline-primary">
                                                    <i class="bx bx-download me-2"></i> Télécharger le document
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Section Informations du média -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-info-circle me-2"></i>Informations du média
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <h4 class="text-dark fw-bold mb-3">{{ $media->titre }}</h4>
                                            @if ($media->description)
                                                <div class="mb-4">
                                                    <h6 class="fw-semibold text-dark mb-2">Description :</h6>
                                                    <p class="text-muted mb-0" style="line-height: 1.6;">
                                                        {!! $media->description !!}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <span class="fw-semibold text-dark">Type :</span>
                                                    <br>
                                                    <span
                                                        class="badge 
                                                        @if ($media->type === 'image') bg-success
                                                        @elseif($media->type === 'audio') bg-warning text-dark
                                                        @elseif($media->type === 'document') bg-info
                                                        @elseif($media->type === 'video') bg-danger
                                                        @else bg-secondary @endif">
                                                        {{ ucfirst($media->type) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <span class="fw-semibold text-dark">Catégorie :</span>
                                                    <br>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $media->categorie ?? 'Non définie' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <span class="fw-semibold text-dark">Formation :</span>
                                                    <br>
                                                    <span class="text-muted">
                                                        {{ $media->formation->titre ?? 'Aucune formation' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <span class="fw-semibold text-dark">Durée :</span>
                                                    <br>
                                                    <span class="text-muted">
                                                        {{ $media->duree ? $media->duree . ' minutes' : 'Non définie' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <span class="fw-semibold text-dark">Ordre d'affichage :</span>
                                                    <br>
                                                    <span class="text-muted">
                                                        {{ $media->ordre ?? 'Non défini' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <span class="fw-semibold text-dark">Source :</span>
                                                    <br>
                                                    <span class="badge bg-primary">
                                                        {{ $media->is_url ? 'Lien externe' : 'Fichier uploadé' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($media->url && $media->is_url)
                                            <div class="mb-3">
                                                <span class="fw-semibold text-dark">URL :</span>
                                                <br>
                                                <a href="{{ $media->url }}" target="_blank"
                                                    class="text-decoration-none text-truncate d-inline-block"
                                                    style="max-width: 100%;">
                                                    {{ $media->url }}
                                                </a>
                                            </div>
                                        @endif

                                        <div class="mt-4 pt-3 border-top">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="bx bx-calendar me-1"></i>
                                                        Créé le : {{ $media->created_at->format('d/m/Y à H:i') }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="bx bx-calendar-edit me-1"></i>
                                                        Modifié le : {{ $media->updated_at->format('d/m/Y à H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center py-3">
                                        <div class="btn-group">
                                            <a href="{{ route('medias.edit', $media->id) }}"
                                                class="btn btn-warning px-4">
                                                <i class="bx bx-edit me-2"></i> Modifier le média
                                            </a>
                                            <a href="{{ route('medias.index') }}"
                                                class="btn btn-outline-secondary px-4 ms-2">
                                                <i class="bx bx-list-ul me-2"></i> Voir tous les médias
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
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

        .media-preview {
            transition: transform 0.3s ease;
        }

        .media-preview:hover {
            transform: scale(1.02);
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@endsection
