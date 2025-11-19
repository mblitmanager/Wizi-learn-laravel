@extends('admin.layout')
@section('title', 'Détails du stagiaire')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('stagiaires.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Stagiaires
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $stagiaire->user->name }} {{ $stagiaire->prenom }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('stagiaires.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                        <a href="{{ route('stagiaires.edit', $stagiaire->id) }}" class="btn btn-warning text-white ms-2">
                            <i class="bx bx-edit me-1"></i> Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-xl-3">
                <!-- Carte profil -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <form action="{{ route('parametre.updateImage', $stagiaire->user->id) }}" method="POST"
                            enctype="multipart/form-data" id="updateImageForm">
                            @csrf
                            @method('PUT')

                            <label for="imageInput" class="cursor-pointer position-relative d-inline-block">
                                <img src="{{ $stagiaire->user->image ? asset($stagiaire->user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($stagiaire->user->name) . '&background=0D8ABC&color=fff&size=200' }}"
                                    class="rounded-circle shadow" width="150" height="150" alt="Avatar"
                                    id="profileImage" style="object-fit: cover; border: 4px solid #fff;">

                                <span
                                    class="position-absolute bottom-0 end-0 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 40px; height: 40px;">
                                    <i class="bx bx-camera text-primary fs-5"></i>
                                </span>
                            </label>

                            <input type="file" name="image" id="imageInput" class="d-none" accept="image/*"
                                onchange="document.getElementById('updateImageForm').submit();">
                        </form>

                        <h4 class="mt-4 mb-1 fw-bold text-dark">{{ $stagiaire->user->name }} {{ $stagiaire->prenom }}</h4>
                        <span class="badge bg-primary px-3 py-2">{{ ucfirst($stagiaire->user->role) }}</span>

                        <div class="mt-3">
                            <span class="badge {{ $stagiaire->statut ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                                <i class="bx bx-{{ $stagiaire->statut ? 'check' : 'x' }}-circle me-1"></i>
                                {{ $stagiaire->statut ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-info-circle me-2"></i>Informations de contact
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bx bx-envelope text-primary me-3 fs-5"></i>
                            <div>
                                <small class="text-muted d-block">Email</small>
                                <a href="mailto:{{ $stagiaire->user->email }}" class="text-decoration-none">
                                    {{ $stagiaire->user->email }}
                                </a>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <i class="bx bx-phone text-primary me-3 fs-5"></i>
                            <div>
                                <small class="text-muted d-block">Téléphone</small>
                                <span class="text-dark">{{ $stagiaire->telephone }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="bx bx-map text-primary me-3 fs-5"></i>
                            <div>
                                <small class="text-muted d-block">Adresse</small>
                                <span class="text-dark">{{ $stagiaire->adresse }}, {{ $stagiaire->code_postal }}
                                    {{ $stagiaire->ville }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-xl-9">
                <!-- Informations personnelles -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="bx bx-user me-2"></i>Informations personnelles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Nom</label>
                                <p class="fw-semibold text-dark mb-0">{{ $stagiaire->user->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Prénom</label>
                                <p class="fw-semibold text-dark mb-0">{{ $stagiaire->prenom }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Date de naissance</label>
                                <p class="fw-semibold text-dark mb-0">
                                    {{ $stagiaire->date_naissance ? \Carbon\Carbon::parse($stagiaire->date_naissance)->format('d/m/Y') : 'Non renseignée' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Civilité</label>
                                <p class="fw-semibold text-dark mb-0">{{ $stagiaire->civilite }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Date de lancement</label>
                                <p class="fw-semibold text-dark mb-0">
                                    {{ $stagiaire->date_debut_formation ? \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y') : 'Non renseignée' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small mb-1">Date de vente</label>
                                <p class="fw-semibold text-dark mb-0">
                                    {{ $stagiaire->date_inscription ? \Carbon\Carbon::parse($stagiaire->date_inscription)->format('d/m/Y') : 'Non renseignée' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Partenaire associé -->
                @if ($stagiaire->partenaire)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-info text-white py-3">
                            <h5 class="mb-0">
                                <i class="bx bx-building me-2"></i>Partenaire associé
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Identifiant</label>
                                    <p class="fw-semibold text-dark mb-0">{{ $stagiaire->partenaire->identifiant }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Type</label>
                                    <p class="fw-semibold text-dark mb-0">{{ $stagiaire->partenaire->type }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Ville</label>
                                    <p class="fw-semibold text-dark mb-0">{{ $stagiaire->partenaire->ville }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Département</label>
                                    <p class="fw-semibold text-dark mb-0">{{ $stagiaire->partenaire->departement }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label text-muted small mb-1">Adresse</label>
                                    <p class="fw-semibold text-dark mb-0">{{ $stagiaire->partenaire->adresse }},
                                        {{ $stagiaire->partenaire->code_postal }}</p>
                                </div>
                                @if ($stagiaire->partenaire->logo)
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Logo</label>
                                        <div>
                                            <img src="{{ asset('storage/' . $stagiaire->partenaire->logo) }}"
                                                alt="Logo Partenaire" class="rounded shadow-sm"
                                                style="max-width: 120px; max-height: 120px;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('partenaires.show', $stagiaire->partenaire->id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="bx bx-group me-1"></i> Voir les contacts du partenaire
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-info-circle me-2 fs-5"></i>
                            <div>
                                <strong>Aucun partenaire associé à ce stagiaire.</strong>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Formations associées -->
                @unless ($stagiaire->catalogue_formations->isEmpty())
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="mb-0">
                                <i class="bx bx-book-reader me-2"></i>Formations associées
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="accordion" id="accordionFormation">
                                @foreach ($stagiaire->catalogue_formations as $index => $formation)
                                    @php
                                        $bgColor = '';
                                        switch ($formation->formation->categorie) {
                                            case 'Bureautique':
                                                $bgColor = '#3D9BE9';
                                                break;
                                            case 'Langues':
                                                $bgColor = '#A55E6E';
                                                break;
                                            case 'Internet':
                                                $bgColor = '#FFC533';
                                                break;
                                            case 'Création':
                                                $bgColor = '#9392BE';
                                                break;
                                            default:
                                                $bgColor = '#6c757d';
                                        }
                                        $formateur = $formation->pivot->formateur_id
                                            ? \App\Models\Formateur::find($formation->pivot->formateur_id)
                                            : null;
                                    @endphp
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="formation-heading-{{ $index }}">
                                            <button class="accordion-button collapsed text-white"
                                                style="background: {{ $bgColor }}" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#formation-collapse-{{ $index }}"
                                                aria-expanded="false" aria-controls="formation-collapse-{{ $index }}">
                                                <i class="bx bx-book me-2"></i>
                                                {{ $formation->titre }}
                                            </button>
                                        </h2>
                                        <div id="formation-collapse-{{ $index }}" class="accordion-collapse collapse"
                                            aria-labelledby="formation-heading-{{ $index }}"
                                            data-bs-parent="#accordionFormation">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <strong>Catégorie:</strong> {{ $formation->formation->categorie }}
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <strong>Durée:</strong> {{ $formation->duree }} h
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <strong>Description:</strong> {!! $formation->description !!}
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <strong>Date début:</strong><br>
                                                        {{ $formation->pivot->date_debut ? \Carbon\Carbon::parse($formation->pivot->date_debut)->format('d/m/Y') : 'Non renseignée' }}
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <strong>Date inscription:</strong><br>
                                                        {{ $formation->pivot->date_inscription ? \Carbon\Carbon::parse($formation->pivot->date_inscription)->format('d/m/Y') : 'Non renseignée' }}
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <strong>Date fin:</strong><br>
                                                        {{ $formation->pivot->date_fin ? \Carbon\Carbon::parse($formation->pivot->date_fin)->format('d/m/Y') : 'Non renseignée' }}
                                                    </div>
                                                    <div class="col-12">
                                                        <strong>Formateur:</strong>
                                                        {{ $formateur ? $formateur->user->name . ' ' . $formateur->prenom : 'Non assigné' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-book me-2 fs-5"></i>
                            <div>
                                <strong>Aucune formation associée à ce stagiaire.</strong>
                            </div>
                        </div>
                    </div>
                @endunless

                <!-- Formateurs associés -->
                @unless ($stagiaire->formateurs->isEmpty())
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-warning text-dark py-3">
                            <h5 class="mb-0">
                                <i class="bx bx-group me-2"></i>Formateurs associés
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="accordion" id="accordionFormateurs">
                                @foreach ($stagiaire->formateurs as $index => $formateur)
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="formateur-heading-{{ $index }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#formateur-collapse-{{ $index }}"
                                                aria-expanded="false" aria-controls="formateur-collapse-{{ $index }}">
                                                <div class="d-flex align-items-center">
                                                    <i class="bx bx-user-circle me-3 text-warning"></i>
                                                    <div>
                                                        <div class="fw-semibold">{{ $formateur->prenom }}
                                                            {{ $formateur->user->name }}</div>
                                                        <small class="text-muted">{{ $formateur->user->email }}</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="formateur-collapse-{{ $index }}" class="accordion-collapse collapse"
                                            aria-labelledby="formateur-heading-{{ $index }}"
                                            data-bs-parent="#accordionFormateurs">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <strong>Téléphone:</strong> {{ $formateur->telephone }}
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <strong>Email:</strong>
                                                        <a
                                                            href="mailto:{{ $formateur->user->email }}">{{ $formateur->user->email }}</a>
                                                    </div>
                                                    <div class="col-12">
                                                        <strong>Formations proposées:</strong>
                                                        <div class="mt-2">
                                                            @if ($formateur->catalogue_formations && $formateur->catalogue_formations->count())
                                                                @foreach ($formateur->catalogue_formations as $row)
                                                                    @php
                                                                        $bgColor = '';
                                                                        switch ($row->formation->categorie) {
                                                                            case 'Bureautique':
                                                                                $bgColor = '#3D9BE9';
                                                                                break;
                                                                            case 'Langues':
                                                                                $bgColor = '#A55E6E';
                                                                                break;
                                                                            case 'Internet':
                                                                                $bgColor = '#FFC533';
                                                                                break;
                                                                            case 'Création':
                                                                                $bgColor = '#9392BE';
                                                                                break;
                                                                            default:
                                                                                $bgColor = '#6c757d';
                                                                        }
                                                                    @endphp
                                                                    <span class="badge text-white me-1 mb-1"
                                                                        style="background: {{ $bgColor }}">
                                                                        {{ $row->titre }}
                                                                    </span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted">Aucune formation associée</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-user-voice me-2 fs-5"></i>
                            <div>
                                <strong>Aucun formateur associé à ce stagiaire.</strong>
                            </div>
                        </div>
                    </div>
                @endunless

                <!-- Contacts associés -->
                @unless ($stagiaire->poleRelationClient->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-secondary text-white py-3">
                            <h5 class="mb-0">
                                <i class="bx bx-user-voice me-2"></i>Contacts associés
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="accordion" id="accordionPoleRelationClient">
                                @foreach ($stagiaire->poleRelationClient as $index => $pole)
                                    <div class="accordion-item border-bottom">
                                        <h2 class="accordion-header" id="pole-heading-{{ $index }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#pole-collapse-{{ $index }}"
                                                aria-expanded="false" aria-controls="pole-collapse-{{ $index }}">
                                                <div class="d-flex align-items-center">
                                                    <i class="bx bx-user-voice me-3 text-secondary"></i>
                                                    <div>
                                                        <div class="fw-semibold">{{ $pole->prenom }} {{ $pole->user->name }}
                                                        </div>
                                                        <small class="text-muted">{{ $pole->role }}</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="pole-collapse-{{ $index }}" class="accordion-collapse collapse"
                                            aria-labelledby="pole-heading-{{ $index }}"
                                            data-bs-parent="#accordionPoleRelationClient">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-2">
                                                        <strong>Rôle:</strong> {{ $pole->role }}
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <strong>Téléphone:</strong> {{ $pole->telephone }}
                                                    </div>
                                                    <div class="col-12">
                                                        <strong>Email:</strong>
                                                        <a
                                                            href="mailto:{{ $pole->user->email }}">{{ $pole->user->email }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-user-check me-2 fs-5"></i>
                            <div>
                                <strong>Aucun contact associé à ce stagiaire.</strong>
                            </div>
                        </div>
                    </div>
                @endunless
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Réinitialiser Bootstrap JavaScript si nécessaire
        document.addEventListener('DOMContentLoaded', function() {
            // Réinitialiser tous les accordéons
            const accordionButtons = document.querySelectorAll('.accordion-button');

            accordionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Bootstrap gère automatiquement le comportement
                    // Cette fonction assure que le JavaScript est bien chargé
                });
            });

            // Vérifier que Bootstrap est correctement initialisé
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap JavaScript est chargé correctement');
            } else {
                console.error('Bootstrap JavaScript n\'est pas chargé');
            }
        });
    </script>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .card {
            border-radius: 12px;
        }

        .accordion-button {
            border-radius: 8px !important;
            margin: 4px;
            transition: all 0.3s ease;
        }

        .accordion-button:not(.collapsed) {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .accordion-item {
            border: none !important;
        }

        .accordion-body {
            border-top: 1px solid rgba(0, 0, 0, 0.125);
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
        }

        /* Assurer que les accordéons se ferment correctement */
        .accordion-collapse {
            transition: height 0.35s ease;
        }
    </style>
@endsection
