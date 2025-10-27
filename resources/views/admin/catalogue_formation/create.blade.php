@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
<div class="container-fluid">
    <div class="shadow-lg border-0 px-2 py-2 mb-3">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center">

            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('catalogue_formation.index') }}"><i
                                    class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Création catalogue
                            Formation</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('catalogue_formation.index') }}" type="button"
                        class="btn btn-sm btn-primary mx-4"> <i
                            class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">


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

        <div class="card">
            <form action="{{ route('catalogue_formation.store') }}" method="POST" class="px-4 py-4"
                enctype="multipart/form-data">
                @csrf
                <div class="card mb-4 px-4">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-6">
                                <label for="titre" class="form-label">Titre</label>
                                <input type="text" name="titre" id="titre" placeholder="Titre"
                                    class="form-control mb-2 @error('titre') is-invalid @enderror"
                                    value="{{ old('titre') }}">
                                @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control mb-2 @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="duree" class="form-label">Durée (en heures)</label>
                                <input type="number" name="duree" id="duree" placeholder="Durée"
                                    class="form-control mb-2 @error('duree') is-invalid @enderror"
                                    value="{{ old('duree') }}">
                                @error('duree')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="formation_id" class="form-label">Formation</label>
                                <select name="formation_id" id="formation_id"
                                    class="form-select mb-2 @error('formation_id') is-invalid @enderror">
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
                            <div class="col-md-6">
                                <label for="certification" class="form-label">Certification</label>
                                <input type="text" name="certification" id="certification"
                                    placeholder="Certification"
                                    class="form-control mb-2 @error('certification') is-invalid @enderror"
                                    value="{{ old('certification') }}">
                                @error('certification')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="prerequis" class="form-label">Prérequis</label>
                                <input type="text" name="prerequis" id="prerequis" placeholder="Prérequis"
                                    class="form-control mb-2 @error('prerequis') is-invalid @enderror"
                                    value="{{ old('prerequis') }}">
                                @error('prerequis')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="objectifs" class="form-label">Objectifs</label>
                                <textarea name="objectifs" id="objectifs" class="form-control mb-2">{{ old('objectifs') }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="programme" class="form-label">Programme</label>
                                <textarea name="programme" id="programme" class="form-control mb-2">{{ old('programme') }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="lieu" class="form-label">Lieu</label>
                                <input type="text" name="lieu" id="lieu" class="form-control mb-2" value="{{ old('lieu') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="niveau" class="form-label">Niveau</label>
                                <input type="text" name="niveau" id="niveau" class="form-control mb-2" value="{{ old('niveau') }}">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="public_cible" class="form-label">Public cible</label>
                                <input type="text" name="public_cible" id="public_cible" class="form-control mb-2" value="{{ old('public_cible') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="nombre_participants" class="form-label">Nombre de participants</label>
                                <input type="number" name="nombre_participants" id="nombre_participants" class="form-control mb-2" value="{{ old('nombre_participants') }}">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="modalites" class="form-label">Modalités</label>
                                <textarea name="modalites" id="modalites" class="form-control mb-2">{{ old('modalites') }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="modalites_accompagnement" class="form-label">Modalités d'accompagnement</label>
                                <textarea name="modalites_accompagnement" id="modalites_accompagnement" class="form-control mb-2">{{ old('modalites_accompagnement') }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="moyens_pedagogiques" class="form-label">Moyens pédagogiques</label>
                                <textarea name="moyens_pedagogiques" id="moyens_pedagogiques" class="form-control mb-2">{{ old('moyens_pedagogiques') }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="modalites_suivi" class="form-label">Modalités de suivi</label>
                                <textarea name="modalites_suivi" id="modalites_suivi" class="form-control mb-2">{{ old('modalites_suivi') }}</textarea>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="evaluation" class="form-label">Evaluation</label>
                                <textarea name="evaluation" id="evaluation" class="form-control mb-2">{{ old('evaluation') }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="image_url" class="form-label">Image</label>
                                <input type="file" name="image_url" id="image_url"
                                    class="form-control mb-2 @error('image_url') is-invalid @enderror">
                                @error('image_url')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="cursus_pdf" class="form-label">Cursus PDF</label>
                                <input type="file" name="cursus_pdf" id="cursus_pdf" accept=".pdf"
                                    class="form-control mb-2 @error('cursus_pdf') is-invalid @enderror">
                                <p class="mt-1 text-sm text-gray-500">Format accepté : PDF</p>
                                @error('cursus_pdf')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="tarif" class="form-label">Tarif</label>
                                <input type="number" name="tarif" id="tarif" placeholder="Tarif"
                                    class="form-control mb-2 @error('tarif') is-invalid @enderror"
                                    value="{{ old('tarif') }}" step="1">
                                @error('tarif')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="statut" class="form-label">Statut</label>
                                <select name="statut" id="statut"
                                    class="form-select mb-2 @error('statut') is-invalid @enderror">
                                    <option value="">Sélectionnez un statut</option>
                                    <option value="1" {{ old('statut') == '1' ? 'selected' : '' }}>Actif
                                    </option>
                                    <option value="0" {{ old('statut') == '0' ? 'selected' : '' }}>Inactif
                                    </option>
                                </select>
                                @error('statut')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="lni lni-save"></i> Enregistrer
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        function checkQuizFieldsFilled() {
            let isValid = true;

            $('#quiz-form input[], #quiz-form select[]').each(function() {
                const value = $(this).val();
                if (!value || value.trim() === '') {
                    isValid = false;
                    return false;
                }
            });

            if (isValid) {
                $('#question-card').removeClass('d-none').hide().slideDown();
                $('#reponse-card').removeClass('d-none').hide().slideDown();
            } else {
                $('#question-card').slideUp();
                $('#reponse-card').slideUp();
            }
        }


        $('#quiz-form input, #quiz-form select').on('input change', function() {
            checkQuizFieldsFilled();
        });


        checkQuizFieldsFilled();
    });
</script>
<script>
    document.getElementById('add-reponse-btn').addEventListener('click', function() {

        const container = document.getElementById('reponses-container');


        const firstForm = container.querySelector('.reponse-form');
        const newForm = firstForm.cloneNode(true);


        newForm.querySelectorAll('input, textarea, select').forEach(field => {
            field.value = '';
        });


        if (!newForm.querySelector('.remove-reponse-btn')) {
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn btn-danger btn-sm remove-reponse-btn mt-3';
            removeButton.textContent = 'Supprimer';


            const textEndDiv = newForm.querySelector('.text-end');
            if (!textEndDiv) {
                const newTextEndDiv = document.createElement('div');
                newTextEndDiv.className = 'text-end';
                newTextEndDiv.appendChild(removeButton);
                newForm.appendChild(newTextEndDiv);
            } else {
                textEndDiv.appendChild(removeButton);
            }
        }


        container.appendChild(newForm);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-reponse-btn')) {
            const formToRemove = e.target.closest('.reponse-form');
            formToRemove.remove();
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-reponse-btn')) {
            const container = document.getElementById('reponses-container');
            const forms = container.querySelectorAll('.reponse-form');
            if (forms.length > 1) {
                const formToRemove = e.target.closest('.reponse-form');
                formToRemove.remove();
            } else {
                alert('Vous devez avoir au moins une réponse.');
            }
        }
    });
</script>
@endsection
