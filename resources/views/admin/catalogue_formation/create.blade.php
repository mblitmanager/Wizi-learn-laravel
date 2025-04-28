@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
  <div class="container">
      <div class="shadow-lg border-0 px-2 py-2 mb-3">

      <div class="page-breadcrumb d-none d-sm-flex align-items-center">

          <div class="ps-3">
              <nav aria-label="breadcrumb">
                  <ol class="breadcrumb mb-0 p-0">
                      <li class="breadcrumb-item"><a href="{{ route('catalogue_formation.index') }}"><i
                                  class="bx bx-home-alt"></i></a>
                      </li>
                      <li class="breadcrumb-item active" aria-current="page">Création catalogue Formation</li>
                  </ol>
              </nav>
          </div>
          <div class="ms-auto">
              <div class="btn-group">
                  <a href="{{ route('catalogue_formation.index') }}" type="button" class="btn btn-sm btn-primary mx-4"> <i
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
                                  <input type="text" name="certification" id="certification" placeholder="Certification"
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
                                  <label for="tarif" class="form-label">Tarif</label>
                                  <input type="number" name="tarif" id="tarif" placeholder="Tarif"
                                         class="form-control mb-2 @error('tarif') is-invalid @enderror"
                                         value="{{ old('tarif') }}">
                                  @error('tarif')
                                  <div class="invalid-feedback">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>

                          <div class="row">
                              <div class="col-md-6">
                                  <label for="statut" class="form-label">Statut</label>
                                  <select name="statut" id="statut"
                                          class="form-select mb-2 @error('statut') is-invalid @enderror">
                                      <option value="">Sélectionnez un statut</option>
                                      <option value="1" {{ old('statut') == '1' ? 'selected' : '' }}>Publié</option>
                                      <option value="0" {{ old('statut') == '0' ? 'selected' : '' }}>Non publié
                                      </option>
                                  </select>
                                  @error('statut')
                                  <div class="invalid-feedback d-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>

                      </div>
                  </div>

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
