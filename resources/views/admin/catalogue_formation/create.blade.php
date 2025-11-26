@extends('admin.layout')
@section('title', 'Créer un catalogue de formation')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-book-open me-2"></i>Création catalogue de formation
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('catalogue_formation.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Catalogue de formations
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Création
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
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
                            <i class="bx bx-plus-circle me-2"></i>Nouveau catalogue de formation
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('catalogue_formation.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="accordion" id="catalogueAccordion">
                                <!-- Section Générale -->
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="headingGeneral">
                                        <button class="accordion-button bg-light text-dark fw-semibold border-0 shadow-sm"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseGeneral"
                                            aria-expanded="true" aria-controls="collapseGeneral">
                                            <i class="bx bx-info-circle me-2"></i>Informations générales
                                        </button>
                                    </h2>
                                    <div id="collapseGeneral" class="accordion-collapse collapse show"
                                        aria-labelledby="headingGeneral" data-bs-parent="#catalogueAccordion">
                                        <div class="accordion-body bg-light rounded-bottom">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="titre"
                                                        class="form-label fw-semibold text-dark">Titre</label>
                                                    <input type="text" name="titre" id="titre"
                                                        placeholder="Titre de la formation"
                                                        class="form-control @error('titre') is-invalid @enderror"
                                                        value="{{ old('titre') }}">
                                                    @error('titre')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="formation_id"
                                                        class="form-label fw-semibold text-dark">Formation associée</label>
                                                    <select name="formation_id" id="formation_id"
                                                        class="form-select @error('formation_id') is-invalid @enderror">
                                                        <option value="">Sélectionnez une formation</option>
                                                        @foreach ($formations as $formation)
                                                            <option value="{{ $formation->id }}"
                                                                {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                                                {{ $formation->titre }}
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
                                                    <textarea name="description" id="description" rows="4" placeholder="Description de la formation"
                                                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="duree" class="form-label fw-semibold text-dark">Durée
                                                        (heures)</label>
                                                    <input type="number" name="duree" id="duree" placeholder="Durée"
                                                        class="form-control @error('duree') is-invalid @enderror"
                                                        value="{{ old('duree') }}" min="1">
                                                    @error('duree')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="tarif" class="form-label fw-semibold text-dark">Tarif
                                                        (€)</label>
                                                    <input type="number" name="tarif" id="tarif"
                                                        placeholder="Tarif"
                                                        class="form-control @error('tarif') is-invalid @enderror"
                                                        value="{{ old('tarif') }}" min="0" step="0.01">
                                                    @error('tarif')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="statut"
                                                        class="form-label fw-semibold text-dark">Statut</label>
                                                    <select name="statut" id="statut"
                                                        class="form-select @error('statut') is-invalid @enderror">
                                                        <option value="">Sélectionnez un statut</option>
                                                        <option value="1"
                                                            {{ old('statut') == '1' ? 'selected' : '' }}>Actif</option>
                                                        <option value="0"
                                                            {{ old('statut') == '0' ? 'selected' : '' }}>Inactif</option>
                                                    </select>
                                                    @error('statut')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="certification"
                                                        class="form-label fw-semibold text-dark">Certification</label>
                                                    <input type="text" name="certification" id="certification"
                                                        placeholder="Certification"
                                                        class="form-control @error('certification') is-invalid @enderror"
                                                        value="{{ old('certification') }}">
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
                                                        placeholder="Prérequis"
                                                        class="form-control @error('prerequis') is-invalid @enderror"
                                                        value="{{ old('prerequis') }}">
                                                    @error('prerequis')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="niveau"
                                                        class="form-label fw-semibold text-dark">Niveau</label>
                                                    <input type="text" name="niveau" id="niveau"
                                                        placeholder="Niveau"
                                                        class="form-control @error('niveau') is-invalid @enderror"
                                                        value="{{ old('niveau') }}">
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
                                    <h2 class="accordion-header" id="headingMedias">
                                        <button
                                            class="accordion-button bg-light text-dark fw-semibold border-0 shadow-sm collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedias"
                                            aria-expanded="false" aria-controls="collapseMedias">
                                            <i class="bx bx-image me-2"></i>Médias et documents
                                        </button>
                                    </h2>
                                    <div id="collapseMedias" class="accordion-collapse collapse"
                                        aria-labelledby="headingMedias" data-bs-parent="#catalogueAccordion">
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Programme -->
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="headingProgramme">
                                        <button
                                            class="accordion-button bg-light text-dark fw-semibold border-0 shadow-sm collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseProgramme"
                                            aria-expanded="false" aria-controls="collapseProgramme">
                                            <i class="bx bx-book-content me-2"></i>Objectifs & Programme
                                        </button>
                                    </h2>
                                    <div id="collapseProgramme" class="accordion-collapse collapse"
                                        aria-labelledby="headingProgramme" data-bs-parent="#catalogueAccordion">
                                        <div class="accordion-body bg-light rounded-bottom">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="objectifs"
                                                        class="form-label fw-semibold text-dark">Objectifs</label>
                                                    <textarea name="objectifs" id="objectifs" rows="4" placeholder="Objectifs de la formation"
                                                        class="form-control">{{ old('objectifs') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="programme"
                                                        class="form-label fw-semibold text-dark">Programme</label>
                                                    <textarea name="programme" id="programme" rows="6" placeholder="Programme détaillé de la formation"
                                                        class="form-control">{{ old('programme') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section Modalités -->
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="headingModalites">
                                        <button
                                            class="accordion-button bg-light text-dark fw-semibold border-0 shadow-sm collapsed"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseModalites"
                                            aria-expanded="false" aria-controls="collapseModalites">
                                            <i class="bx bx-map me-2"></i>Modalités & Logistique
                                        </button>
                                    </h2>
                                    <div id="collapseModalites" class="accordion-collapse collapse"
                                        aria-labelledby="headingModalites" data-bs-parent="#catalogueAccordion">
                                        <div class="accordion-body bg-light rounded-bottom">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="lieu"
                                                        class="form-label fw-semibold text-dark">Lieu</label>
                                                    <input type="text" name="lieu" id="lieu"
                                                        placeholder="Lieu de formation" class="form-control"
                                                        value="{{ old('lieu') }}">
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="public_cible"
                                                        class="form-label fw-semibold text-dark">Public cible</label>
                                                    <input type="text" name="public_cible" id="public_cible"
                                                        placeholder="Public cible" class="form-control"
                                                        value="{{ old('public_cible') }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="nombre_participants"
                                                        class="form-label fw-semibold text-dark">Nombre de
                                                        participants</label>
                                                    <input type="number" name="nombre_participants"
                                                        id="nombre_participants"
                                                        placeholder="Nombre maximum de participants" class="form-control"
                                                        value="{{ old('nombre_participants') }}" min="1">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="modalites"
                                                        class="form-label fw-semibold text-dark">Modalités</label>
                                                    <textarea name="modalites" id="modalites" rows="3" placeholder="Modalités de la formation"
                                                        class="form-control">{{ old('modalites') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="modalites_accompagnement"
                                                        class="form-label fw-semibold text-dark">Modalités
                                                        d'accompagnement</label>
                                                    <textarea name="modalites_accompagnement" id="modalites_accompagnement" rows="3"
                                                        placeholder="Modalités d'accompagnement" class="form-control">{{ old('modalites_accompagnement') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="moyens_pedagogiques"
                                                        class="form-label fw-semibold text-dark">Moyens
                                                        pédagogiques</label>
                                                    <textarea name="moyens_pedagogiques" id="moyens_pedagogiques" rows="3"
                                                        placeholder="Moyens pédagogiques utilisés" class="form-control">{{ old('moyens_pedagogiques') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="modalites_suivi"
                                                        class="form-label fw-semibold text-dark">Modalités de suivi</label>
                                                    <textarea name="modalites_suivi" id="modalites_suivi" rows="3" placeholder="Modalités de suivi"
                                                        class="form-control">{{ old('modalites_suivi') }}</textarea>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="evaluation"
                                                        class="form-label fw-semibold text-dark">Évaluation</label>
                                                    <textarea name="evaluation" id="evaluation" rows="3" placeholder="Modalités d'évaluation"
                                                        class="form-control">{{ old('evaluation') }}</textarea>
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
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bx bx-save me-1"></i> Créer le catalogue
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
