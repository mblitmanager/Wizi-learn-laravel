@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modification d'un
                                media</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('medias.index') }}" type="button" class="btn btn-sm btn-primary"><i
                                class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                        <form action="{{ route('medias.destroy', $media->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce média ?');"
                            style="display:inline-block; margin-left: 8px;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="lni lni-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body p-4 border rounded">
                    <div class="card-body p-4 border rounded">
                        <form class="row g-3" action="{{ route('medias.update', $media->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <!-- Nom -->
                                <div class="mb-3">
                                    <label for="titre">Nom</label>
                                    <input type="text" name="titre" id="titre"
                                        class="form-control @error('titre') is-invalid @enderror"
                                        value="{{ old('titre', $media->titre) }}">
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Choix Fichier ou URL -->
                                <div class="mb-3">
                                    <label class="form-label">Source du média</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="source_type"
                                                id="edit_source_file" value="file"
                                                {{ old('source_type', $media->is_url ? 'url' : 'file') == 'file' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_source_file">Téléverser un
                                                fichier</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="source_type"
                                                id="edit_source_url" value="url"
                                                {{ old('source_type', $media->is_url ? 'url' : 'file') == 'url' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="edit_source_url">Utiliser un lien</label>
                                        </div>
                                    </div>
                                    @error('source_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Fichier -->
                                <div class="mb-3" id="edit-file-upload-field">
                                    <label for="file">Fichier (image, vidéo ou PDF)</label>
                                    <input type="file" name="file" id="file"
                                        class="form-control @error('file') is-invalid @enderror"
                                        accept="image/*, video/*, .pdf, audio/*">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if ($media->url && !$media->is_url)
                                        <small class="form-text text-muted mt-2">
                                            Fichier actuel :
                                            <a href="{{ asset($media->url) }}" target="_blank"
                                                class="text-decoration-none">Voir le fichier</a>
                                        </small>
                                    @endif
                                </div>

                                <!-- URL -->
                                <div class="mb-3" id="edit-url-field">
                                    <label for="url_text">URL du média</label>
                                    <input type="text" name="url_text" id="url_text"
                                        class="form-control @error('url_text') is-invalid @enderror"
                                        value="{{ old('url_text', $media->is_url ? $media->url : '') }}"
                                        placeholder="https://example.com/media/video.mp4">
                                    @error('url_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Formation -->
                                <div class="mb-3">
                                    <label for="formation_id">Formation</label>
                                    <select name="formation_id" id="formation_id"
                                        class="form-select @error('formation_id') is-invalid @enderror">
                                        <option value="">Choisir une formation</option>
                                        @foreach ($formations as $formation)
                                            <option value="{{ $formation->id }}"
                                                {{ old('formation_id', $media->formation_id) == $formation->id ? 'selected' : '' }}>
                                                {{ $formation->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('formation_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Type -->
                                <div class="mb-3">
                                    <label for="type">Type</label>
                                    <select name="type" id="type"
                                        class="form-select @error('type') is-invalid @enderror">
                                        <option value="">Choisir un type</option>
                                        <option value="video"
                                            {{ old('type', $media->type) == 'video' ? 'selected' : '' }}>Vidéo</option>
                                        <option value="document"
                                            {{ old('type', $media->type) == 'document' ? 'selected' : '' }}>Document
                                        </option>
                                        <option value="image"
                                            {{ old('type', $media->type) == 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="audio"
                                            {{ old('type', $media->type) == 'audio' ? 'selected' : '' }}>Audio</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Catégorie -->
                                <div class="mb-3">
                                    <label for="categorie">Catégorie</label>
                                    <select name="categorie" id="categorie"
                                        class="form-select @error('categorie') is-invalid @enderror">
                                        <option value="">Choisir une catégorie</option>
                                        <option value="tutoriel"
                                            {{ old('categorie', $media->categorie) == 'tutoriel' ? 'selected' : '' }}>
                                            Tutoriel</option>
                                        <option value="astuce"
                                            {{ old('categorie', $media->categorie) == 'astuce' ? 'selected' : '' }}>Astuce
                                        </option>
                                    </select>
                                    @error('categorie')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Durée -->
                                <div class="mb-3">
                                    <label for="duree">Durée (en minutes)</label>
                                    <input type="number" name="duree" id="duree"
                                        class="form-control @error('duree') is-invalid @enderror"
                                        value="{{ old('duree', $media->duree) }}">
                                    @error('duree')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ordre -->
                                <div class="mb-3">
                                    <label for="ordre">Ordre</label>
                                    <input type="number" name="ordre" id="ordre"
                                        class="form-control @error('ordre') is-invalid @enderror"
                                        value="{{ old('ordre', $media->ordre) }}">
                                    @error('ordre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="5">{{ old('description', $media->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="lni lni-save me-2"></i> Mettre à jour
                                </button>
                                <a href="{{ route('medias.index') }}" class="btn btn-secondary px-4 ms-2">
                                    <i class="lni lni-arrow-left me-2"></i> Annuler
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
        < script >
            document.addEventListener('DOMContentLoaded', function() {
                const fileUploadField = document.getElementById('edit-file-upload-field');
                const urlField = document.getElementById('edit-url-field');
                const sourceFileRadio = document.getElementById('edit_source_file');
                const sourceUrlRadio = document.getElementById('edit_source_url');

                // Déterminer l'état initial basé sur le type de source
                const isUrlSource = "{{ $media->is_url ? 'true' : 'false' }}" === 'true';

                function toggleFields() {
                    if (sourceUrlRadio.checked) {
                        fileUploadField.style.display = 'none';
                        urlField.style.display = 'block';
                    } else {
                        fileUploadField.style.display = 'block';
                        urlField.style.display = 'none';
                    }
                }

                // Initialiser l'état
                if (isUrlSource) {
                    sourceUrlRadio.checked = true;
                } else {
                    sourceFileRadio.checked = true;
                }
                toggleFields();

                // Écouter les changements
                sourceFileRadio.addEventListener('change', toggleFields);
                sourceUrlRadio.addEventListener('change', toggleFields);
            }); <
        />
    @endsection
