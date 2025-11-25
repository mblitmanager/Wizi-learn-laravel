@extends('admin.layout')
@section('title', 'Modifier un Formateur')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-edit me-2"></i>Modification du formateur
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('formateur.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Formateurs
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('formateur.show', $formateur->id) }}" class="text-decoration-none">
                                        {{ $formateur->user->name }} {{ $formateur->prenom }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Modification
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <form action="{{ route('formateur.destroy', $formateur->id) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?')">
                                <i class="bx bx-trash me-1"></i> Supprimer
                            </button>
                        </form>
                        <a href="{{ route('formateur.index') }}" class="btn btn-outline-primary ms-2">
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

        @if ($errors->any())
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">Veuillez corriger les erreurs ci-dessous</span>
                </div>
                <ul class="mt-2 mb-0 ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form class="row g-3" action="{{ route('formateur.update', $formateur->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Section Informations personnelles -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-id-card me-2"></i>Informations personnelles
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="civilite"
                                                    class="form-label fw-semibold text-dark">Civilité</label>
                                                <select name="civilite" id="civilite"
                                                    class="form-select @error('civilite') is-invalid @enderror">
                                                    <option value="">Sélectionner</option>
                                                    <option value="M."
                                                        {{ old('civilite', $formateur->civilite ?? '') == 'M.' ? 'selected' : '' }}>
                                                        M.</option>
                                                    <option value="Mme."
                                                        {{ old('civilite', $formateur->civilite ?? '') == 'Mme.' ? 'selected' : '' }}>
                                                        Mme.</option>
                                                    <option value="Mlle."
                                                        {{ old('civilite', $formateur->civilite ?? '') == 'Mlle.' ? 'selected' : '' }}>
                                                        Mlle.</option>
                                                </select>
                                                @error('civilite')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="name" class="form-label fw-semibold text-dark">Nom</label>
                                                <input type="text" name="name" id="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name', $formateur->user->name ?? '') }}"
                                                    placeholder="Entrez le nom">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mb-3">
                                                <label for="prenom"
                                                    class="form-label fw-semibold text-dark">Prénom</label>
                                                <input type="text" name="prenom" id="prenom"
                                                    class="form-control @error('prenom') is-invalid @enderror"
                                                    value="{{ old('prenom', $formateur->prenom ?? '') }}"
                                                    placeholder="Entrez le prénom">
                                                @error('prenom')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Coordonnées -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-envelope me-2"></i>Coordonnées
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="email" class="form-label fw-semibold text-dark">Adresse
                                                    e-mail</label>
                                                <input type="email" name="email" id="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ old('email', $formateur->user->email ?? '') }}"
                                                    placeholder="email@exemple.com">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="telephone"
                                                    class="form-label fw-semibold text-dark">Téléphone</label>
                                                <input type="text" name="telephone" id="telephone"
                                                    class="form-control @error('telephone') is-invalid @enderror"
                                                    value="{{ old('telephone', $formateur->telephone ?? '') }}"
                                                    placeholder="+33 ...">
                                                @error('telephone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="password" class="form-label fw-semibold text-dark">Mot de
                                                    passe</label>
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Laissez vide pour ne pas modifier">
                                                <div class="form-text">Laissez vide pour conserver le mot de passe actuel
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Photo de profil -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-camera me-2"></i>Photo de profil
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="image"
                                                    class="form-label fw-semibold text-dark">Photo</label>
                                                <input type="file"
                                                    class="form-control @error('image') is-invalid @enderror"
                                                    id="image" name="image" accept="image/*">
                                                <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB
                                                </div>
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            @if ($formateur->user->image)
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold text-dark">Photo actuelle</label>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ asset($formateur->user->image) }}"
                                                            alt="Photo actuelle" class="rounded border"
                                                            style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                                        <div>
                                                            <span class="badge bg-info text-dark">Photo actuelle</span>
                                                            <div class="form-text">L'image sera remplacée si vous en
                                                                sélectionnez une nouvelle</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bx bx-user-circle fs-1 text-muted"></i>
                                                    <p class="text-muted mb-0 mt-2">Aucune photo</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Stagiaires associés -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-group me-2"></i>Stagiaires associés
                                        <span
                                            class="badge bg-info text-dark ms-2">{{ $formateur->stagiaires->count() }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="stagiaire_id"
                                                    class="form-label fw-semibold text-dark">Sélection des
                                                    stagiaires</label>
                                                <select name="stagiaire_id[]" id="stagiaire_id" multiple
                                                    class="form-control select2 @error('stagiaire_id') is-invalid @enderror">
                                                    @foreach ($stagiaires as $stagiaire)
                                                        <option value="{{ $stagiaire->id }}"
                                                            {{ in_array($stagiaire->id, old('stagiaire_id', $formateur->stagiaires->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                            {{ strtoupper($stagiaire->user->name) }}
                                                            {{ $stagiaire->prenom }}
                                                            @if ($stagiaire->user->email)
                                                                - {{ $stagiaire->user->email }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('stagiaire_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Formations -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-book-reader me-2"></i>Formations associées
                                        <span class="badge bg-primary ms-2">{{ count($catalogue_formations) }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Accordéon des formations -->
                                    <div class="accordion" id="accordionFormations">
                                        <div class="accordion-item border-0">
                                            <h2 class="accordion-header" id="headingFormations">
                                                <button class="accordion-button collapsed bg-primary text-white"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseFormations" aria-expanded="false"
                                                    aria-controls="collapseFormations">
                                                    <i class="bx bx-list-check me-2"></i>
                                                    Sélectionnez les formations
                                                    <span
                                                        class="badge bg-light text-dark ms-2">{{ count($catalogue_formations) }}</span>
                                                </button>
                                            </h2>
                                            <div id="collapseFormations" class="accordion-collapse collapse"
                                                aria-labelledby="headingFormations" data-bs-parent="#accordionFormations">
                                                <div class="accordion-body bg-light">
                                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                                        @foreach ($catalogue_formations as $formation)
                                                            <div class="col">
                                                                <div class="card border shadow-sm h-100">
                                                                    <div class="card-header bg-white py-2">
                                                                        <h6 class="mb-0 fw-semibold text-dark">
                                                                            {{ $formation->titre }}
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="mb-2">
                                                                            <span class="badge bg-info text-dark">
                                                                                {{ $formation->categorie }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                name="catalogue_formation_id[]"
                                                                                id="formation_{{ $formation->id }}"
                                                                                value="{{ $formation->id }}"
                                                                                {{ in_array($formation->id, old('catalogue_formation_id', $formateur->catalogue_formations->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                                            <label class="form-check-label fw-medium"
                                                                                for="formation_{{ $formation->id }}">
                                                                                Sélectionner cette formation
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons de soumission -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 me-3">
                                    <i class="bx bx-save me-2"></i> Enregistrer les modifications
                                </button>
                                <a href="{{ route('formateur.index') }}" class="btn btn-outline-secondary px-5 py-2">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialisation de Select2
            $('.select2').select2({
                placeholder: "Sélectionner les stagiaires...",
                allowClear: true,
                width: '100%'
            });

            // Style personnalisé pour Select2
            $('.select2-container--default .select2-selection--multiple').css({
                'border': '1px solid #dee2e6',
                'border-radius': '8px',
                'min-height': '38px'
            });
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

        .accordion-button {
            border-radius: 8px !important;
            font-weight: 500;
        }

        .accordion-button:not(.collapsed) {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
        }

        /* Style pour les cartes de formation */
        .card .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }

        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
@endsection
