@extends('admin.layout')
@section('title', 'Modifier un Média')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-edit me-2"></i>Modification d'un média
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('medias.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Médias
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Modifier le média
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <form action="{{ route('medias.destroy', $media->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce média ?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bx bx-trash me-1"></i> Supprimer
                            </button>
                        </form>
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

        @if (session('error'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form class="row g-3" action="{{ route('medias.update', $media->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="alert alert-danger border-0 alert-dismissible fade show mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-error-circle me-2 fs-5"></i>
                                        <span class="fw-medium">Veuillez corriger les erreurs ci-dessous</span>
                                    </div>
                                    <ul class="mt-2 mb-0 ps-4">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

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
                                                    value="{{ old('titre', $media->titre) }}"
                                                    placeholder="Entrez le titre du média">
                                                @error('titre')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="formation_id" class="form-label fw-semibold text-dark">Formation
                                                    associée</label>
                                                <select name="formation_id" id="formation_id"
                                                    class="form-select @error('formation_id') is-invalid @enderror">
                                                    <option value="">Sélectionner une formation</option>
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
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="description"
                                                    class="form-label fw-semibold text-dark">Description</label>
                                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                                    rows="4" placeholder="Décrivez le contenu du média...">{{ old('description', $media->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Type et Catégorie -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-category me-2"></i>Type et Catégorie
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label fw-semibold text-dark">Type de
                                                    média</label>
                                                <select name="type" id="type"
                                                    class="form-select @error('type') is-invalid @enderror">
                                                    <option value="">Sélectionner un type</option>
                                                    <option value="video"
                                                        {{ old('type', $media->type) == 'video' ? 'selected' : '' }}>Vidéo
                                                    </option>
                                                    <option value="document"
                                                        {{ old('type', $media->type) == 'document' ? 'selected' : '' }}>
                                                        Document</option>
                                                    <option value="image"
                                                        {{ old('type', $media->type) == 'image' ? 'selected' : '' }}>Image
                                                    </option>
                                                    <option value="audio"
                                                        {{ old('type', $media->type) == 'audio' ? 'selected' : '' }}>Audio
                                                    </option>
                                                </select>
                                                @error('type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="categorie"
                                                    class="form-label fw-semibold text-dark">Catégorie</label>
                                                <select name="categorie" id="categorie"
                                                    class="form-select @error('categorie') is-invalid @enderror">
                                                    <option value="">Sélectionner une catégorie</option>
                                                    <option value="tutoriel"
                                                        {{ old('categorie', $media->categorie) == 'tutoriel' ? 'selected' : '' }}>
                                                        Tutoriel</option>
                                                    <option value="astuce"
                                                        {{ old('categorie', $media->categorie) == 'astuce' ? 'selected' : '' }}>
                                                        Astuce</option>
                                                    <option value="cours"
                                                        {{ old('categorie', $media->categorie) == 'cours' ? 'selected' : '' }}>
                                                        Cours</option>
                                                    <option value="ressource"
                                                        {{ old('categorie', $media->categorie) == 'ressource' ? 'selected' : '' }}>
                                                        Ressource</option>
                                                </select>
                                                @error('categorie')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Source du média -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-cloud-upload me-2"></i>Source du média
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold text-dark">Type de source</label>
                                                <div>
                                                    <div class="form-check form-check-inline" id="edit-source-file-radio">
                                                        <input class="form-check-input" type="radio" name="source_type"
                                                            id="edit_source_file" value="file"
                                                            {{ old('source_type', $media->is_url ? 'url' : 'file') == 'file' ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-medium" for="edit_source_file">
                                                            <i class="bx bx-upload me-1"></i>Téléverser un fichier
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline" id="edit-source-url-radio">
                                                        <input class="form-check-input" type="radio" name="source_type"
                                                            id="edit_source_url" value="url"
                                                            {{ old('source_type', $media->is_url ? 'url' : 'file') == 'url' ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-medium" for="edit_source_url">
                                                            <i class="bx bx-link me-1"></i>Utiliser un lien
                                                        </label>
                                                    </div>
                                                </div>
                                                @error('source_type')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Champ Fichier -->
                                    <div class="row" id="edit-file-upload-field">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="file" class="form-label fw-semibold text-dark">Fichier
                                                    média</label>
                                                <input type="file" name="file" id="file"
                                                    class="form-control @error('file') is-invalid @enderror"
                                                    accept="image/*, video/*, .pdf, audio/*">
                                                <div class="form-text">Formats acceptés selon le type sélectionné</div>
                                                @error('file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror

                                                @if ($media->url && !$media->is_url)
                                                    <div class="mt-2">
                                                        <span class="fw-medium text-dark">Fichier actuel :</span>
                                                        <a href="{{ asset($media->url) }}" target="_blank"
                                                            class="text-decoration-none ms-2">
                                                            <i class="bx bx-show me-1"></i>Voir le fichier
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Champ URL -->
                                    <div class="row" id="edit-url-field">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="url_text" class="form-label fw-semibold text-dark">URL du
                                                    média</label>
                                                <input type="text" name="url_text" id="url_text"
                                                    class="form-control @error('url_text') is-invalid @enderror"
                                                    value="{{ old('url_text', $media->is_url ? $media->url : '') }}"
                                                    placeholder="https://...">
                                                @error('url_text')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Paramètres avancés -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-cog me-2"></i>Paramètres avancés
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="duree" class="form-label fw-semibold text-dark">Durée
                                                    (minutes)</label>
                                                <input type="number" name="duree" id="duree"
                                                    class="form-control @error('duree') is-invalid @enderror"
                                                    value="{{ old('duree', $media->duree) }}" placeholder="0"
                                                    min="0">
                                                @error('duree')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="ordre" class="form-label fw-semibold text-dark">Ordre
                                                    d'affichage</label>
                                                <input type="number" name="ordre" id="ordre"
                                                    class="form-control @error('ordre') is-invalid @enderror"
                                                    value="{{ old('ordre', $media->ordre) }}" placeholder="0"
                                                    min="0">
                                                @error('ordre')
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
                                    <i class="bx bx-save me-2"></i> Mettre à jour
                                </button>
                                <a href="{{ route('medias.index') }}" class="btn btn-outline-secondary px-5 py-2">
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
        document.addEventListener('DOMContentLoaded', function() {
            const fileUploadField = document.getElementById('edit-file-upload-field');
            const urlField = document.getElementById('edit-url-field');
            const sourceFileRadio = document.getElementById('edit_source_file');
            const sourceUrlRadio = document.getElementById('edit_source_url');
            const typeSelect = document.getElementById('type');
            const sourceFileRadioDiv = document.getElementById('edit-source-file-radio');
            const sourceUrlRadioDiv = document.getElementById('edit-source-url-radio');

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

            // Gestion du changement de type de média
            function updateSourceOptions() {
                if (typeSelect.value === 'video') {
                    sourceFileRadioDiv.style.display = 'none';
                    sourceUrlRadioDiv.style.display = 'inline-block';
                    // Sélectionne automatiquement "Utiliser un lien"
                    document.getElementById('edit_source_url').checked = true;
                    fileUploadField.style.display = 'none';
                    urlField.style.display = 'block';
                } else {
                    sourceFileRadioDiv.style.display = 'inline-block';
                    sourceUrlRadioDiv.style.display = 'inline-block';
                    // Réinitialise à "Téléverser un fichier" si c'était sur URL et qu'on change de type
                    if (document.getElementById('edit_source_url').checked && typeSelect.value !== 'video') {
                        document.getElementById('edit_source_file').checked = true;
                        fileUploadField.style.display = 'block';
                        urlField.style.display = 'none';
                    }
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
            typeSelect.addEventListener('change', updateSourceOptions);

            // Initialiser les options de source
            updateSourceOptions();
        });
    </script>

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

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
@endsection
