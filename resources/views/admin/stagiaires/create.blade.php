@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-user-plus me-2"></i>Création d'un stagiaire
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('stagiaires.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Stagiaires
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Nouveau stagiaire
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('stagiaires.index') }}" class="btn btn-outline-primary">
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
                        <form class="row g-3" action="{{ route('stagiaires.store') }}" method="POST">
                            @csrf

                            <!-- Section Civilité -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="civilite" class="form-label fw-semibold text-dark">Civilité</label>
                                        <select name="civilite" id="civilite"
                                            class="form-select @error('civilite') is-invalid @enderror">
                                            <option value="">Sélectionner</option>
                                            <option value="M." {{ old('civilite') == 'M.' ? 'selected' : '' }}>M.
                                            </option>
                                            <option value="Mme" {{ old('civilite') == 'Mme' ? 'selected' : '' }}>Mme
                                            </option>
                                            <option value="Mlle" {{ old('civilite') == 'Mlle' ? 'selected' : '' }}>Mlle
                                            </option>
                                            <option value="Autre" {{ old('civilite') == 'Autre' ? 'selected' : '' }}>Autre
                                            </option>
                                        </select>
                                        @error('civilite')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Section Identité -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-id-card me-2"></i>Identité du stagiaire
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="name" class="form-label fw-semibold text-dark">Nom</label>
                                                <input type="text" name="name" id="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name') }}" placeholder="Entrez le nom">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="prenom"
                                                    class="form-label fw-semibold text-dark">Prénom</label>
                                                <input type="text" name="prenom" id="prenom"
                                                    class="form-control @error('prenom') is-invalid @enderror"
                                                    value="{{ old('prenom') }}" placeholder="Entrez le prénom">
                                                @error('prenom')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="date_naissance" class="form-label fw-semibold text-dark">Date de
                                                    naissance</label>
                                                <input type="date" name="date_naissance" id="date_naissance"
                                                    class="form-control @error('date_naissance') is-invalid @enderror"
                                                    value="{{ old('date_naissance') }}"
                                                    onfocus="this.max=new Date(new Date().getFullYear()-16, new Date().getMonth(), new Date().getDate()).toISOString().split('T')[0]">
                                                @error('date_naissance')
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
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="email" class="form-label fw-semibold text-dark">Adresse
                                                    mail</label>
                                                <input type="email" name="email" id="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ old('email') }}" placeholder="email@exemple.com">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="password" class="form-label fw-semibold text-dark">Mot de
                                                    passe</label>
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="●●●●●●●●">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
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
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="adresse"
                                                    class="form-label fw-semibold text-dark">Adresse</label>
                                                <input type="text" name="adresse" id="adresse"
                                                    class="form-control @error('adresse') is-invalid @enderror"
                                                    value="{{ old('adresse') }}" placeholder="Numéro et rue">
                                                @error('adresse')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label for="ville"
                                                    class="form-label fw-semibold text-dark">Ville</label>
                                                <input type="text" name="ville" id="ville"
                                                    class="form-control @error('ville') is-invalid @enderror"
                                                    value="{{ old('ville') }}" placeholder="Ville">
                                                @error('ville')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label for="code_postal" class="form-label fw-semibold text-dark">Code
                                                    postal</label>
                                                <input type="text" name="code_postal" id="code_postal"
                                                    class="form-control @error('code_postal') is-invalid @enderror"
                                                    value="{{ old('code_postal') }}" placeholder="Code postal">
                                                @error('code_postal')
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
                                        <i class="bx bx-book-reader me-2"></i>Formations du stagiaire
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="date_debut_formation"
                                                    class="form-label fw-semibold text-dark">Date de lancement</label>
                                                <input type="date" name="date_debut_formation"
                                                    id="date_debut_formation"
                                                    class="form-control @error('date_debut_formation') is-invalid @enderror"
                                                    value="{{ old('date_debut_formation') }}">
                                                @error('date_debut_formation')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="date_inscription"
                                                    class="form-label fw-semibold text-dark">Date de vente</label>
                                                <input type="date" name="date_inscription" id="date_inscription"
                                                    class="form-control @error('date_inscription') is-invalid @enderror"
                                                    value="{{ old('date_inscription') }}">
                                                @error('date_inscription')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Accordéon des formations -->
                                    <div class="accordion mt-3" id="accordionFormations">
                                        <div class="accordion-item border-0">
                                            <h2 class="accordion-header" id="headingFormations">
                                                <button class="accordion-button collapsed bg-primary text-white"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapseFormations" aria-expanded="false"
                                                    aria-controls="collapseFormations">
                                                    <i class="bx bx-list-check me-2"></i>
                                                    Sélectionnez les formations
                                                    <span
                                                        class="badge bg-light text-dark ms-2">{{ count($formations) }}</span>
                                                </button>
                                            </h2>
                                            <div id="collapseFormations" class="accordion-collapse collapse"
                                                aria-labelledby="headingFormations" data-bs-parent="#accordionFormations">
                                                <div class="accordion-body bg-light">
                                                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                                        @foreach ($formations as $formation)
                                                            <div class="col">
                                                                <div class="card border shadow-sm h-100">
                                                                    <div class="card-header bg-white py-2">
                                                                        <h6 class="mb-0 fw-semibold text-dark">
                                                                            {{ $formation->titre }}</h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <div class="form-check mb-3">
                                                                            <input class="form-check-input"
                                                                                type="checkbox"
                                                                                name="formations[{{ $formation->id }}][selected]"
                                                                                id="formation_{{ $formation->id }}"
                                                                                value="1"
                                                                                {{ old("formations.{$formation->id}.selected") ? 'checked' : '' }}>
                                                                            <label class="form-check-label fw-medium"
                                                                                for="formation_{{ $formation->id }}">
                                                                                Sélectionner cette formation
                                                                            </label>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label small text-muted">Date
                                                                                de début</label>
                                                                            <input type="date"
                                                                                name="formations[{{ $formation->id }}][date_debut]"
                                                                                class="form-control form-control-sm"
                                                                                value="{{ old("formations.{$formation->id}.date_debut") }}">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label
                                                                                class="form-label small text-muted">Formateur</label>
                                                                            <select
                                                                                name="formations[{{ $formation->id }}][formateur_id]"
                                                                                class="form-select form-select-sm">
                                                                                <option value="">-- Choisir --
                                                                                </option>
                                                                                @foreach ($formateurs as $formateur)
                                                                                    <option value="{{ $formateur->id }}"
                                                                                        {{ old("formations.{$formation->id}.formateur_id") == $formateur->id ? 'selected' : '' }}>
                                                                                        {{ $formateur->user->name }}
                                                                                        {{ $formateur->prenom }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label small text-muted">Date
                                                                                d'inscription</label>
                                                                            <input type="date"
                                                                                name="formations[{{ $formation->id }}][date_inscription]"
                                                                                class="form-control form-control-sm"
                                                                                value="{{ old("formations.{$formation->id}.date_inscription") }}">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label small text-muted">Date
                                                                                de fin</label>
                                                                            <input type="date"
                                                                                name="formations[{{ $formation->id }}][date_fin]"
                                                                                class="form-control form-control-sm"
                                                                                value="{{ old("formations.{$formation->id}.date_fin") }}">
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

                            <!-- Section Référents -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-group me-2"></i>Référents
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="commercial_id"
                                                    class="form-label fw-semibold text-dark">Commercial (optionnel)</label>
                                                <select name="commercial_id[]" id="commercial_id" multiple
                                                    class="form-control select2 @error('commercial_id') is-invalid @enderror">
                                                    @foreach ($commercials as $commercial)
                                                        <option value="{{ $commercial->id }}"
                                                            {{ in_array($commercial->id, old('commercial_id', [])) ? 'selected' : '' }}>
                                                            {{ strtoupper($commercial->user->name) }}
                                                            {{ $commercial->prenom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('commercial_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="poleRelation_id" class="form-label fw-semibold text-dark">Pôle
                                                    Relation Client (optionnel)</label>
                                                <select name="pole_relation_client_id[]" id="poleRelation_id" multiple
                                                    class="form-control select2 @error('pole_relation_client_id') is-invalid @enderror">
                                                    @foreach ($poleRelations as $poleRelation)
                                                        <option value="{{ $poleRelation->id }}"
                                                            {{ in_array($poleRelation->id, old('pole_relation_client_id', [])) ? 'selected' : '' }}>
                                                            {{ strtoupper($poleRelation->user->name) }}
                                                            {{ $poleRelation->prenom }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('pole_relation_client_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Partenaire -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-building me-2"></i>Partenaire
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="partenaire_id"
                                                    class="form-label fw-semibold text-dark">Partenaire associé</label>
                                                <select name="partenaire_id"
                                                    class="form-select @error('partenaire_id') is-invalid @enderror">
                                                    <option value="">-- Aucun partenaire --</option>
                                                    @foreach ($partenaires as $partenaire)
                                                        <option value="{{ $partenaire->id }}"
                                                            {{ old('partenaire_id') == $partenaire->id ? 'selected' : '' }}>
                                                            {{ $partenaire->identifiant }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('partenaire_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bouton de soumission -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2">
                                    <i class="bx bx-save me-2"></i> Enregistrer le stagiaire
                                </button>
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
                placeholder: "Sélectionner...",
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
    </style>
@endsection
