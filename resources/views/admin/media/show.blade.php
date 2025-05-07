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
                            @if (Str::startsWith($media->type, 'image'))
                            <img src="{{ asset($media->url) }}" alt="{{ $media->titre }}"
                                class="img-fluid rounded shadow-lg" style="max-height: 400px; object-fit: cover;">
                            @elseif (Str::startsWith($media->type, 'video'))
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
                            @elseif (Str::startsWith($media->type, 'audio'))
                            <div class="card p-4 shadow-lg border-0"
                                style="background: linear-gradient(135deg, #fbdfa1, #ffb923); border-radius: 20px;">
                                <div class="text-center mb-4">
                                    <i class="bi bi-music-note-beamed" style="font-size: 4rem; color: #6c63ff;"></i>
                                </div>
                                <audio controls
                                    style="width: 100%; border-radius: 10px; background: #fff; padding: 10px;">
                                    <source src="{{ asset($media->url) }}" type="audio/mpeg">
                                    Votre navigateur ne supporte pas la lecture d'audio.
                                </audio>
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
@endsection