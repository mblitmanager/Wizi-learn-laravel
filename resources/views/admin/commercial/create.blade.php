@extends('admin.layout')
@section('title', 'Créer un commercial')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-user-plus me-2"></i>Création d'un commercial
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('commercials.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Commerciaux
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Nouveau commercial
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('commercials.index') }}" class="btn btn-outline-primary">
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
                        <form class="row g-3" action="{{ route('commercials.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

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
                                                    <option value="M." {{ old('civilite') == 'M.' ? 'selected' : '' }}>
                                                        M.</option>
                                                    <option value="Mme."
                                                        {{ old('civilite') == 'Mme.' ? 'selected' : '' }}>Mme.</option>
                                                    <option value="Mlle."
                                                        {{ old('civilite') == 'Mlle.' ? 'selected' : '' }}>Mlle.</option>
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
                                                    value="{{ old('name') }}" placeholder="Entrez le nom" required>
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
                                                    value="{{ old('prenom') }}" placeholder="Entrez le prénom" required>
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
                                                    value="{{ old('email') }}" placeholder="email@exemple.com" required>
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
                                                    value="{{ old('telephone') }}" placeholder="+33 ...">
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
                                                    placeholder="●●●●●●●●" required>
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
                                    </div>
                                </div>
                            </div>

                            <!-- Section Stagiaires associés -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-group me-2"></i>Stagiaires associés
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
                                                            {{ in_array($stagiaire->id, old('stagiaire_id', [])) ? 'selected' : '' }}>
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

                            <!-- Boutons de soumission -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 me-3">
                                    <i class="bx bx-save me-2"></i> Créer le commercial
                                </button>
                                <a href="{{ route('commercials.index') }}" class="btn btn-outline-secondary px-5 py-2">
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
    </style>
@endsection
