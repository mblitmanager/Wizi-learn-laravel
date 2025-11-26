@extends('admin.layout')
@section('title', 'Création d\'un partenaire')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-building me-2"></i>Création d'un partenaire
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('partenaires.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Partenaires
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Nouveau partenaire
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('partenaires.index') }}" class="btn btn-outline-primary">
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
                        <form class="row g-3" action="{{ route('partenaires.store') }}" method="POST"
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
                                                <label for="identifiant"
                                                    class="form-label fw-semibold text-dark">Identifiant</label>
                                                <input type="text" name="identifiant" id="identifiant"
                                                    class="form-control @error('identifiant') is-invalid @enderror"
                                                    value="{{ old('identifiant') }}"
                                                    placeholder="Entrez l'identifiant du partenaire" required>
                                                @error('identifiant')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label fw-semibold text-dark">Type</label>
                                                <input type="text" name="type" id="type"
                                                    class="form-control @error('type') is-invalid @enderror"
                                                    value="{{ old('type') }}" placeholder="Type de partenaire" required>
                                                @error('type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="logo" class="form-label fw-semibold text-dark">Logo</label>
                                                <input type="file" name="logo" id="logo"
                                                    class="form-control @error('logo') is-invalid @enderror"
                                                    accept="image/*">
                                                <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB
                                                </div>
                                                @error('logo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Adresse -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-map me-2"></i>Adresse
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="adresse"
                                                    class="form-label fw-semibold text-dark">Adresse</label>
                                                <input type="text" name="adresse" id="adresse"
                                                    class="form-control @error('adresse') is-invalid @enderror"
                                                    value="{{ old('adresse') }}" placeholder="Numéro et rue" required>
                                                @error('adresse')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="ville"
                                                    class="form-label fw-semibold text-dark">Ville</label>
                                                <input type="text" name="ville" id="ville"
                                                    class="form-control @error('ville') is-invalid @enderror"
                                                    value="{{ old('ville') }}" placeholder="Ville" required>
                                                @error('ville')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="code_postal" class="form-label fw-semibold text-dark">Code
                                                    postal</label>
                                                <input type="text" name="code_postal" id="code_postal"
                                                    class="form-control @error('code_postal') is-invalid @enderror"
                                                    value="{{ old('code_postal') }}" placeholder="Code postal" required>
                                                @error('code_postal')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="departement"
                                                    class="form-label fw-semibold text-dark">Département</label>
                                                <input type="text" name="departement" id="departement"
                                                    class="form-control @error('departement') is-invalid @enderror"
                                                    value="{{ old('departement') }}" placeholder="Département" required>
                                                @error('departement')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Contacts -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-group me-2"></i>Contacts (jusqu'à 3)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @for ($i = 0; $i < 3; $i++)
                                        <div class="card border shadow-sm mb-3">
                                            <div class="card-header bg-white py-2">
                                                <h6 class="mb-0 fw-semibold text-dark">
                                                    Contact {{ $i + 1 }}
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-3">
                                                        <label class="form-label small text-muted">Nom</label>
                                                        <input type="text" name="contacts[{{ $i }}][nom]"
                                                            class="form-control @error("contacts.{$i}.nom") is-invalid @enderror"
                                                            value="{{ old("contacts.{$i}.nom") }}" placeholder="Nom">
                                                        @error("contacts.{$i}.nom")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small text-muted">Prénom</label>
                                                        <input type="text"
                                                            name="contacts[{{ $i }}][prenom]"
                                                            class="form-control @error("contacts.{$i}.prenom") is-invalid @enderror"
                                                            value="{{ old("contacts.{$i}.prenom") }}"
                                                            placeholder="Prénom">
                                                        @error("contacts.{$i}.prenom")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small text-muted">Fonction</label>
                                                        <input type="text"
                                                            name="contacts[{{ $i }}][fonction]"
                                                            class="form-control @error("contacts.{$i}.fonction") is-invalid @enderror"
                                                            value="{{ old("contacts.{$i}.fonction") }}"
                                                            placeholder="Fonction">
                                                        @error("contacts.{$i}.fonction")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small text-muted">Email</label>
                                                        <input type="email" name="contacts[{{ $i }}][email]"
                                                            class="form-control @error("contacts.{$i}.email") is-invalid @enderror"
                                                            value="{{ old("contacts.{$i}.email") }}"
                                                            placeholder="email@exemple.com">
                                                        @error("contacts.{$i}.email")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small text-muted">Téléphone</label>
                                                        <input type="text" name="contacts[{{ $i }}][tel]"
                                                            class="form-control @error("contacts.{$i}.tel") is-invalid @enderror"
                                                            value="{{ old("contacts.{$i}.tel") }}" placeholder="+33 ...">
                                                        @error("contacts.{$i}.tel")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <!-- Section Stagiaires -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-user me-2"></i>Stagiaires associés
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="stagiaires" class="form-label fw-semibold text-dark">Sélection
                                                    des stagiaires</label>
                                                <select name="stagiaires[]" id="stagiaires" multiple
                                                    class="form-control select2 @error('stagiaires') is-invalid @enderror">
                                                    @foreach ($stagiaires as $stagiaire)
                                                        <option value="{{ $stagiaire->id }}"
                                                            {{ in_array($stagiaire->id, old('stagiaires', [])) ? 'selected' : '' }}>
                                                            {{ strtoupper($stagiaire->user->name ?? $stagiaire->nom) }}
                                                            {{ $stagiaire->prenom }}
                                                            @php($email = $stagiaire->user->email ?? null)
                                                            @php($phone = $stagiaire->telephone ?? null)
                                                            @if ($email || $phone)
                                                                - {{ $email ?? '-' }} @if ($phone)
                                                                    • {{ $phone }}
                                                                @endif
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('stagiaires')
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
                                    <i class="bx bx-save me-2"></i> Créer le partenaire
                                </button>
                                <a href="{{ route('partenaires.index') }}" class="btn btn-outline-secondary px-5 py-2">
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

        /* Style pour les cartes de contact */
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
