@extends('admin.layout')

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Détails du média</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('medias.index') }}">Gestion media</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $media->titre }}</li>
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

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow rounded-4">
                <div class="card-body p-4">
                    <h4 class="card-title mb-3 text-primary">{{ $media->titre }}</h4>
                    <p class="text-muted mb-4">{{ $media->description }}</p>

                    <div class="mb-4 text-center">
                        @if (Str::startsWith($media->type, 'image'))
                            <img src="{{ asset($media->url) }}" alt="{{ $media->titre }}" class="img-fluid rounded shadow">
                        @elseif(Str::startsWith($media->type, 'video'))
                            <video controls class="w-100 rounded shadow" autoplay>
                                <source src="{{ asset($media->url) }}" type="video/mp4">
                                Votre navigateur ne supporte pas la lecture de vidéos.
                            </video>
                        @else
                            <a href="{{ asset($media->url) }}" target="_blank" class="btn btn-outline-primary">
                                Télécharger le média
                            </a>
                        @endif
                    </div>

                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item"><strong>Type :</strong> {{ $media->type }}</li>
                        <li class="list-group-item"><strong>Formation liée :</strong>
                            {{ $media->formation->titre ?? 'Aucune' }}</li>
                    </ul>

                    <div class="text-end">
                        <a href="{{ route('medias.edit', $media->id) }}" class="btn btn-warning"><i class="bx bx-edit"></i>
                            Modifier</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
