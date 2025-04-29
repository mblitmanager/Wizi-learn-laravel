@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')


    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">

                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('quiz.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Création d' un quiz</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('quiz.index') }}" type="button" class="btn btn-sm btn-primary mx-4"> <i
                                class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                    </div>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <div class="text-white"> {{ $errors->first() }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
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
        <div class="col-md-12">
            <div class="card">
                <form action="{{ route('quiz.storeAll') }}" method="POST" class="px-4 py-4" enctype="multipart/form-data">
                    @csrf

                    {{-- QUIZ --}}
                    <div class=" mb-4 px-4" id="quiz-form">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Titre</label>
                                    <input type="text" name="quiz[titre]" placeholder="Titre" class="form-control mb-2"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Description</label>
                                    <input type="text" name="quiz[description]" placeholder="Description"
                                        id="description" class="form-control mb-2">
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Niveau</label>

                                    <select name="quiz[niveau]" class="form-select mb-2">
                                        <option value="">Niveau</option>
                                        <option value="débutant">Débutant</option>
                                        <option value="intermédiaire">Intermédiaire</option>
                                        <option value="avancé">Avancé</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Durée</label>

                                    <input type="number" name="quiz[duree]" placeholder="Durée" class="form-control mb-2">
                                </div>


                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Formation</label>

                                    <select name="quiz[formation_id]" class="form-select mb-2">
                                        @foreach ($formations as $formation)
                                            <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Points total</label>
                                    <input type="number" name="quiz[nb_points_total]" placeholder="Points total"
                                        class="form-control">

                                </div>
                            </div>

                        </div>
                    </div>
                    <hr>

                    {{-- QUESTION --}}
                    <div class="card mb-4 px-4" id="question-card">
                        <div class="card-body">
                            <h5 class="">Ajouter une question</h5>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Texte de la question</label>

                                    <input type="text" name="question[text]" placeholder="Texte de la question"
                                        class="form-control mb-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Media URL</label>

                                    <input type="file" name="question[media_url]" placeholder="Media URL"
                                        class="form-control mb-2">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Type</label>
                                    <select name="question[type]" class="form-select mb-2" required>
                                        <option value="">Type</option>
                                        <option value="question audio">Question audio</option>
                                        <option value="remplir le champ vide">Remplir le champ vide</option>
                                        <option value="carte flash">Carte flash</option>
                                        <option value="correspondance">Correspondance</option>
                                        <option value="choix multiples">Choix multiples</option>
                                        <option value="rearrangement">Rearrangement</option>
                                        <option value="vrai/faux">Vrai / Faux</option>
                                        <option value="banque de mots">Banque de mots</option>
                                    </select>

                                </div>
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Réponse correcte</label>

                                    <input type="text" name="question[reponse_correct]" placeholder="Réponse correcte"
                                        class="form-control mb-2">
                                </div>
                                <div class="col-md-12">
                                    <label for="input1" class="form-label">Explication</label>

                                    <textarea type="text" name="question[explication]" placeholder="Explication" class="form-control mb-2"></textarea>

                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Points</label>

                                    <input type="number" name="question[points]" placeholder="Points"
                                        class="form-control mb-2">
                                </div>
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Astuce</label>

                                    <input type="text" name="question[astuce]" placeholder="Astuce"
                                        class="form-control mb-2">

                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- RÉPONSE --}}
                    <div class="card mb-4 px-4" id="reponse-card">
                        <div class="card-body">
                            <div class="row d-flex align-items-center justify-content-between mb-3">
                                <div class="col-md-6">
                                    <h5 class="">Ajouter une réponse</h5>
                                    <hr>
                                </div>
                                <div class="col-md-6 d-flex align-items-end justify-content-end">
                                    <div id="add-reponse-btn"
                                        class="d-flex align-items-center theme-icons p-2 cursor-pointer rounded">
                                        <button type="button" class="btn btn-sm btn-primary px-5"><i
                                                class="lni lni-plus"></i></i>Ajouter une réponse</button>
                                    </div>
                                </div>
                            </div>
                            <div id="reponses-container">
                                <div class="reponse-form mb-4 mt-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="input1" class="form-label">Texte de la réponse</label>

                                            <input type="text" name="reponse[text][]"
                                                placeholder="Texte de la réponse" class="form-control mb-2">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="input1" class="form-label">Bonne réponse ?</label>

                                            <select name="reponse[is_correct][]" class="form-select mb-2">
                                                <option value="">Bonne réponse ?</option>
                                                <option value="1">Oui</option>
                                                <option value="0">Non</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="input1" class="form-label">Type de réponse</label>
                                            <input type="number" name="reponse[position][]" placeholder="Position"
                                                class="form-control mb-2">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="input1" class="form-label">Pair correspondant</label>
                                            <input type="text" name="reponse[match_pair][]"
                                                placeholder="Pair correspondante" class="form-control mb-2">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="input1" class="form-label">Groupe de banque de mots</label>
                                            <input type="text" name="reponse[bank_group][]" placeholder="Groupe"
                                                class="form-control mb-2">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="input1" class="form-label">Texte de la carte flash</label>
                                            <textarea name="reponse[flashcard_back][]" placeholder="Verso de la flashcard" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- SUBMIT --}}
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-sm px-4"> <i class="lni lni-save"></i>Tout
                            enregistrer</button>
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

                $('#quiz-form input[required], #quiz-form select[required]').each(function() {
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
