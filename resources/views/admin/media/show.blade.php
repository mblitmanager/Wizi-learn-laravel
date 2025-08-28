@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold"><a
                                    href="{{ route('medias.index') }}">Détails d'un média</a>
                            </li>

                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('medias.index') }}" class="btn btn-sm btn-primary"><i
                                class="bx bx-chevron-left-circle"></i> Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-body p-4">
                        <div class="row">

                            <!-- Colonne pour le média -->
                            <div class="col-md-6 d-flex justify-content-center align-items-center">
                                @php
                                    // Détection des URLs YouTube
                                    $isYoutubeUrl = false;
                                    $youtubeId = null;
                                    if (filter_var($media->url, FILTER_VALIDATE_URL)) {
                                        $parsedUrl = parse_url($media->url);
                                        $host = strtolower($parsedUrl['host'] ?? '');

                                        if (str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be')) {
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

        // Nettoyage de l'ID (suppression des paramètres supplémentaires)
                                            if ($youtubeId) {
                                                $youtubeId = strtok($youtubeId, '?&');
                                            }
                                        }
                                    }

                                    // Déterminer le type de média
                                    $isAudio = Str::startsWith($media->type, 'audio') || $media->type === 'audio';
                                    $isImage = Str::startsWith($media->type, 'image');
                                    $isVideo = Str::startsWith($media->type, 'video');
                                @endphp

                                @if ($isYoutubeUrl && $youtubeId)
                                    <div class="card p-3 shadow-lg border-0 youtube-card"
                                        style="
                                            background: linear-gradient(135deg, #ff0000, #cc0000);
                                            border-radius: 15px;
                                            width: 100%;
                                            max-width: 800px;
                                            transition: transform 0.3s ease;
                                        ">
                                        <div class="ratio ratio-16x9">
                                            <iframe
                                                src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=0&rel=0&modestbranding=1"
                                                title="{{ $media->titre }}"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen style="border-radius: 10px; border: none;"
                                                loading="lazy"></iframe>
                                        </div>
                                        <div class="card-footer bg-transparent border-0 pt-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="https://www.youtube.com/watch?v={{ $youtubeId }}"
                                                    target="_blank" class="btn btn-sm btn-danger">
                                                    <i class="fab fa-youtube me-2"></i>Voir sur YouTube
                                                </a>
                                                <small class="text-white">@lang('Vidéo YouTube')</small>
                                            </div>
                                        </div>
                                    </div>
                                @elseif ($isImage)
                                    <img src="{{ asset($media->url) }}" alt="{{ $media->titre }}"
                                        class="img-fluid rounded shadow-lg" style="max-height: 400px; object-fit: cover;">
                                @elseif ($isVideo)
                                    <div class="card p-3 shadow-lg border-0"
                                        style="background: linear-gradient(135deg, #ffb923, #fcde99);">
                                        <div class="position-relative" style="overflow: hidden;">
                                            <video controls autoplay muted playsinline
                                                style="width: 100%; height: auto; display: block; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
                                                <source src="{{ asset($media->url) }}" type="video/mp4">
                                                Votre navigateur ne supporte pas la lecture de vidéos.
                                            </video>
                                        </div>
                                    </div>
                                @elseif ($isAudio)
                                    <div class="card p-4 shadow-lg border-0 audio-player-container"
                                        style="background: linear-gradient(135deg, #6c63ff, #4a43c9); border-radius: 20px; width: 100%;">
                                        <div class="text-center mb-4">
                                            <i class="bi bi-music-note-beamed" style="font-size: 4rem; color: white;"></i>
                                            <h4 class="mt-3 text-white">{{ $media->titre }}</h4>
                                        </div>
                                        <audio controls
                                            style="width: 100%; border-radius: 10px; background: rgba(255, 255, 255, 0.2); padding: 15px;">
                                            <source src="{{ asset($media->url) }}" type="audio/mpeg">
                                            Votre navigateur ne supporte pas la lecture d'audio.
                                        </audio>
                                        <div class="mt-3 text-center">
                                            <small class="text-white">Type: {{ $media->type }}</small>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ asset($media->url) }}" target="_blank" class="btn btn-outline-primary">
                                        Télécharger le média
                                    </a>
                                @endif
                            </div>

                            <!-- Colonne pour les informations -->
                            <div class="col-md-6" style="">
                                <h3 class="mb-3" style="font-weight: bold; color: #333;">{{ $media->titre }}</h3>
                                <p class="text-muted mb-4">{!! $media->description !!}</p>
                                <ul class="list-group list-group-flush mb-4">
                                    <li class="list-group-item"><strong>Type :</strong> {{ $media->type }}</li>
                                    <li class="list-group-item"><strong>Formation liée :</strong>
                                        {{ $media->formation->titre ?? 'Aucune' }}
                                    </li>
                                    <li class="list-group-item"><strong>Catégorie :</strong>
                                        {{ $media->categorie ?? 'Aucune' }}
                                    </li>
                                </ul>
                                <div class="text-end">
                                    <a href="{{ route('medias.edit', $media->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bx bx-edit"></i> Modifier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .audio-player-container {
            transition: transform 0.3s ease;
        }

        .audio-player-container:hover {
            transform: translateY(-5px);
        }
    </style>
@endsection
