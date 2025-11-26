@extends('admin.layout')
@section('title', 'Modifier un catalogue de formation')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-book-open me-2"></i>Modification catalogue de formation
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('catalogue_formation.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Catalogue de formations
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $catalogueFormation->titre }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="">


                        <form action="{{ route('catalogue_formation.destroy', $catalogueFormation->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger ms-2"
                                onclick="return confirm('Supprimer ce catalogue ?')">
                                <i class="bx bx-trash me-1"></i> Supprimer
                            </button>
                        </form>
                        <form action="{{ route('catalogue_formation.duplicate', $catalogueFormation->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning ms-2"
                                onclick="return confirm('Dupliquer ce catalogue ?')">
                                <i class="bx bx-copy me-1"></i> Dupliquer
                            </button>
                        </form>
                        <a href="{{ route('catalogue_formation.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 bg-success alert-dismissible fade show">
                <div class="text-white"> {{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                // Rediriger automatiquement vers l'index après enregistrement réussi
                setTimeout(function() {
                    window.location.href = "{{ route('catalogue_formation.index') }}";
                }, 800);
            </script>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <div class="text-white"> {{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <!-- Carte principale du formulaire -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-edit me-2"></i>Modifier : {{ $catalogueFormation->titre }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('catalogue_formation.update', $catalogueFormation->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="accordion" id="catalogueAccordionEdit">
                                <!-- Section Générale -->
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="headingGeneralEdit">
                                        <button class="accordion-button bg-light text-dark fw-semibold border-0 shadow-sm"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeneralEdit"
                                            aria-expanded="true" aria-controls="collapseGeneralEdit">
                                            <i class="bx bx-info-circle me-2"></i>Informations générales
                                        </button>
                                    </h2>
                                    <div id="collapseGeneralEdit" class="accordion-collapse collapse show"
                                        aria-labelledby="headingGeneralEdit" data-bs-parent="#catalogueAccordionEdit">
                                        <div class="accordion-body bg-light rounded-bottom">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="titre"
                                                        class="form-label fw-semibold text-dark">Titre</label>
                                                    <input type="text" name="titre" id="titre"
                                                        class="form-control @error('titre') is-invalid @enderror"
                                                        value="{{ old('titre', $catalogueFormation->titre) }}" required>
                                                    @error('titre')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="formation_id"
                                                        class="form-label fw-semibold text-dark">Formation associée</label>
                                                    <select name="formation_id" id="formation_id"
                                                        class="form-select @error('formation_id') is-invalid @enderror"
                                                        required>
                                                        <option value="">Sélectionnez une formation</option>
                                                        @foreach ($formations as $f)
                                                            <option value="{{ $f->id }}"
                                                                {{ old('formation_id', $catalogueFormation->formation_id) == $f->id ? 'selected' : '' }}>
                                                                {{ $f->titre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('formation_id')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="description"
                                                        class="form-label fw-semibold text-dark">Description</label>
                                                    <textarea name="description" id="description" rows="4"
                                                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $catalogueFormation->description) }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="duree" class="form-label fw-semibold text-dark">Durée
                                                        (heures)</label>
                                                    <input type="number" name="duree" id="duree"
                                                        class="form-control @error('duree') is-invalid @enderror"
                                                        value="{{ old('duree', $catalogueFormation->duree) }}" required
                                                        min="1">
                                                    @error('duree')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="tarif" class="form-label fw-semibold text-dark">Tarif
                                                        (€)</label>
                                                    <input type="number" name="tarif" id="tarif"
                                                        class="form-control @error('tarif') is-invalid @enderror"
                                                        value="{{ old('tarif', $catalogueFormation->tarif) }}" required
                                                        min="0" step="0.01">
                                                    @error('tarif')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="statut"
                                                        class="form-label fw-semibold text-dark">Statut</label>
                                                    <select name="statut" id="statut"
                                                        class="form-select @error('statut') is-invalid @enderror" required>
                                                        <option value="">Sélectionnez un statut</option>
                                                        <option value="1"
                                                            {{ old('statut', $catalogueFormation->statut) == '1' ? 'selected' : '' }}>
                                                            Actif
                                                        </option>
                                                        <option value="0"
                                                            {{ old('statut', $catalogueFormation->statut) == '0' ? 'selected' : '' }}>
                                                            Inactif
                                                        </option>
                                                    </select>
                                                    @error('statut')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="certification"
                                                        class="form-label fw-semibold text-dark">Certification</label>
                                                    <input type="text" name="certification" id="certification"
                                                        class="form-control @error('certification') is-invalid @enderror"
                                                        value="{{ old('certification', $catalogueFormation->certification) }}">
                                                    @error('certification')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="prerequis"
                                                        class="form-label fw-semibold text-dark">Prérequis</label>
                                                    <input type="text" name="prerequis" id="prerequis"
                                                        class="form-control @error('prerequis') is-invalid @enderror"
                                                        value="{{ old('prerequis', $catalogueFormation->prerequis) }}">
                                                    @error('prerequis')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="niveau"
                                                        class="form-label fw-semibold text-dark">Niveau</label>
                                                    <input type="text" name="niveau" id="niveau"
                                                        class="form-control @error('niveau') is-invalid @enderror"
                                                        value="{{ old('niveau', $catalogueFormation->niveau) }}">
                                                    @error('niveau')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Médias -->
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="headingMediasEdit">
                                        <button
                                            class="accordion-button bg-light text-dark fw-semibold border-0 shadow-sm collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseMediasEdit"
                                            aria-expanded="false" aria-controls="collapseMediasEdit">
                                            <i class="bx bx-image me-2"></i>Médias et documents
                                        </button>
                                    </h2>
                                    <div id="collapseMediasEdit" class="accordion-collapse collapse"
                                        aria-labelledby="headingMediasEdit" data-bs-parent="#catalogueAccordionEdit">
                                        <div class="accordion-body bg-light rounded-bottom">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="image_url"
                                                        class="form-label fw-semibold text-dark">Image</label>
                                                    <input type="file" name="image_url" id="image_url"
                                                        class="form-control @error('image_url') is-invalid @enderror"
                                                        accept="image/*">
                                                    <div class="form-text">Formats acceptés : JPG, PNG, GIF, WEBP</div>
                                                    @error('image_url')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror

                                                    @if ($catalogueFormation->image_url)
                                                        @php
                                                            $extension = strtolower(
                                                                pathinfo(
                                                                    $catalogueFormation->image_url,
                                                                    PATHINFO_EXTENSION,
                                                                ),
                                                            );
                                                        @endphp

                                                        <div class="mt-3 text-center">
                                                            <p class="fw-semibold text-dark mb-2">Image actuelle :</p>
                                                            @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                                <img src="{{ asset($catalogueFormation->image_url) }}"
                                                                    alt="Image actuelle" class="img-fluid rounded shadow"
                                                                    style="max-width: 250px;">
                                                            @elseif (in_array($extension, ['mp4', 'webm', 'ogg']))
                                                                <video controls class="rounded shadow"
                                                                    style="max-width: 100%; height: auto;">
                                                                    <source
                                                                        src="{{ asset('storage/' . $catalogueFormation->image_url) }}"
                                                                        type="video/{{ $extension }}">
                                                                    Votre navigateur ne supporte pas la lecture de vidéos.
                                                                </video>
                                                            @elseif (in_array($extension, ['mp3', 'wav', 'ogg']))
                                                                <audio controls class="rounded shadow mt-2"
                                                                    style="width: 100%;">
                                                                    <source
                                                                        src="{{ asset($catalogueFormation->image_url) }}"
                                                                        type="audio/{{ $extension }}">
                                                                    Votre navigateur ne supporte pas la lecture d'audio.
                                                                </audio>
                                                            @else
                                                                <a href="{{ asset($catalogueFormation->image_url) }}"
                                                                    target="_blank" class="btn btn-outline-primary">
                                                                    <i class="bx bx-download me-1"></i> Télécharger le
                                                                    fichier
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="cursus_pdf"
                                                        class="form-label fw-semibold text-dark">Cursus PDF</label>
                                                    <input type="file" name="cursus_pdf" id="cursus_pdf"
                                                        accept=".pdf"
                                                        class="form-control @error('cursus_pdf') is-invalid @enderror">
                                                    <div class="form-text">Format accepté : PDF uniquement</div>
                                                    @error('cursus_pdf')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror

                                                    @if ($catalogueFormation->cursus_pdf)
                                                        <div class="mt-3">
                                                            <p class="fw-semibold text-dark mb-2">PDF actuel :</p>
                                                            <div class="d-flex gap-2 flex-wrap">
                                                                <a href="{{ route('catalogue_formation.download-pdf', $catalogueFormation->id) }}"
                                                                    class="btn btn-outline-primary btn-sm">
                                                                    <i class="bx bx-download me-1"></i> Télécharger
                                                                </a>
                                                                <a href="{{ asset($catalogueFormation->cursus_pdf) }}"
                                                                    target="_blank"
                                                                    class="btn btn-outline-secondary btn-sm">
                                                                    <i class="bx bx-link-external me-1"></i> Ouvrir
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Programme -->
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="headingProgrammeEdit">
                                        <button
                                            class="accordion-button bg-light text-dark fw-semibold border-0 shadow-sm collapsed"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseProgrammeEdit" aria-expanded="false"
                                            aria-controls="collapseProgrammeEdit">
                                            <i class="bx bx-book-content me-2"></i>Objectifs & Programme
                                        </button>
                                    </h2>
                                    <div id="collapseProgrammeEdit" class="accordion-collapse collapse"
                                        aria-labelledby="headingProgrammeEdit" data-bs-parent="#catalogueAccordionEdit">
                                        <div class="accordion-body bg-light rounded-bottom">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="objectifs"
                                                        class="form-label fw-semibold text-dark">Objectifs</label>
                                                    <textarea name="objectifs" id="objectifs" rows="4" class="form-control">{{ old('objectifs', $catalogueFormation->objectifs) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="programme"
                                                        class="form-label fw-semibold text-dark">Programme</label>
                                                    <textarea name="programme" id="programme" rows="6" class="form-control">{{ old('programme', $catalogueFormation->programme) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Modalités -->
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="headingModalitesEdit">
                                        <button
                                            class="accordion-button bg-light text-dark fw-semibold border-0 shadow-sm collapsed"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseModalitesEdit" aria-expanded="false"
                                            aria-controls="collapseModalitesEdit">
                                            <i class="bx bx-map me-2"></i>Modalités & Logistique
                                        </button>
                                    </h2>
                                    <div id="collapseModalitesEdit" class="accordion-collapse collapse"
                                        aria-labelledby="headingModalitesEdit" data-bs-parent="#catalogueAccordionEdit">
                                        <div class="accordion-body bg-light rounded-bottom">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="lieu"
                                                        class="form-label fw-semibold text-dark">Lieu</label>
                                                    <input type="text" name="lieu" id="lieu"
                                                        class="form-control"
                                                        value="{{ old('lieu', $catalogueFormation->lieu) }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="public_cible"
                                                        class="form-label fw-semibold text-dark">Public cible</label>
                                                    <input type="text" name="public_cible" id="public_cible"
                                                        class="form-control"
                                                        value="{{ old('public_cible', $catalogueFormation->public_cible) }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nombre_participants"
                                                        class="form-label fw-semibold text-dark">Nombre de
                                                        participants</label>
                                                    <input type="number" name="nombre_participants"
                                                        id="nombre_participants" class="form-control"
                                                        value="{{ old('nombre_participants', $catalogueFormation->nombre_participants) }}"
                                                        min="1">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="modalites"
                                                        class="form-label fw-semibold text-dark">Modalités</label>
                                                    <textarea name="modalites" id="modalites" rows="3" class="form-control">{{ old('modalites', $catalogueFormation->modalites) }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="modalites_accompagnement"
                                                        class="form-label fw-semibold text-dark">Modalités
                                                        d'accompagnement</label>
                                                    <textarea name="modalites_accompagnement" id="modalites_accompagnement" rows="3" class="form-control">{{ old('modalites_accompagnement', $catalogueFormation->modalites_accompagnement) }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="moyens_pedagogiques"
                                                        class="form-label fw-semibold text-dark">Moyens
                                                        pédagogiques</label>
                                                    <textarea name="moyens_pedagogiques" id="moyens_pedagogiques" rows="3" class="form-control">{{ old('moyens_pedagogiques', $catalogueFormation->moyens_pedagogiques) }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="modalites_suivi"
                                                        class="form-label fw-semibold text-dark">Modalités de suivi</label>
                                                    <textarea name="modalites_suivi" id="modalites_suivi" rows="3" class="form-control">{{ old('modalites_suivi', $catalogueFormation->modalites_suivi) }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="evaluation"
                                                        class="form-label fw-semibold text-dark">Évaluation</label>
                                                    <textarea name="evaluation" id="evaluation" rows="3" class="form-control">{{ old('evaluation', $catalogueFormation->evaluation) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions du formulaire -->
                            <div class="card border-0 shadow-sm mt-4">
                                <div class="card-body text-center">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bx bx-check me-1"></i> Mettre à jour
                                        </button>
                                        <a href="{{ route('catalogue_formation.index') }}"
                                            class="btn btn-outline-primary ms-2">
                                            <i class="bx bx-arrow-back me-1"></i> Annuler
                                        </a>
                                    </div>
                                </div>
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
        $(document).ready(function() {
            // Aperçu de l'image sélectionnée
            $('#image_url').change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let preview = $('#image-preview');
                        if (preview.length === 0) {
                            $('#image_url').after(
                                '<div id="image-preview" class="mt-2 text-center"><img src="' + e
                                .target.result +
                                '" style="max-height: 150px;" class="img-thumbnail rounded"></div>');
                        } else {
                            preview.html('<img src="' + e.target.result +
                                '" style="max-height: 150px;" class="img-thumbnail rounded">');
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Validation des champs numériques
            $('form').submit(function(e) {
                const duree = $('#duree').val();
                const tarif = $('#tarif').val();

                if (duree && duree <= 0) {
                    alert('La durée doit être supérieure à 0');
                    e.preventDefault();
                    return false;
                }

                if (tarif && tarif < 0) {
                    alert('Le tarif ne peut pas être négatif');
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
@endsection

<style>
    .card {
        border-radius: 12px;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
    }

    .badge {
        border-radius: 6px;
        font-weight: 500;
    }

    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #1e40af;
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0, 0, 0, .125);
    }
</style>
