@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Quiz</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('quiz.index') }}" type="button" class="btn btn-primary">Retour</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <h5 class="card-title text-wizi">Ajouter Quiz</h5>
        <hr>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            <form action="{{ route('quiz.storeAll') }}" method="POST" class="px-4 py-4">
                @csrf

                {{-- QUIZ --}}
                <div class="card mb-4 px-4" id="quiz-form">
                    <div class="card-body">
                        <h5 class="text-wizi">Créer un quiz</h5>
                        <div class="row">
                            <div class="col-md-6"> <input type="number" name="quiz[duree]" placeholder="Durée"
                                    class="form-control mb-2">
                            </div>
                            <div class="col-md-6"> <input type="text" name="quiz[description]" placeholder="Description"
                                    class="form-control mb-2">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6"> <input type="text" name="quiz[titre]" placeholder="Titre"
                                    class="form-control mb-2" required>
                            </div>
                            <div class="col-md-6"> <select name="quiz[niveau]" class="form-select mb-2">
                                    <option value="">Niveau</option>
                                    <option value="débutant">Débutant</option>
                                    <option value="intermédiaire">Intermédiaire</option>
                                    <option value="avancé">Avancé</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <select name="quiz[formation_id]" class="form-select mb-2">
                                    @foreach ($formations as $formation)
                                        <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="number" name="quiz[nb_points_total]" placeholder="Points total"
                                    class="form-control">

                            </div>
                        </div>

                    </div>
                </div>

                {{-- QUESTION --}}
                <div class="card mb-4 px-4" id="question-card">
                    <div class="card-body">
                        <h5 class="text-wizi">Ajouter une question</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="question[text]" placeholder="Texte de la question"
                                    class="form-control mb-2" required>
                            </div>
                            <div class="col-md-6">
                                {{-- URL média (audio, image, etc.) --}}
                                <input type="text" name="question[media_url]" placeholder="Media URL"
                                    class="form-control mb-2">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                {{-- Type de question --}}
                                <select name="question[type]" class="form-select mb-2" required>
                                    <option value="">Type</option>
                                    <option value="question audio">Question audio</option>
                                    <option value="remplir le champ vide">Remplir le champ vide</option>
                                    <option value="carte flash">Carte flash</option>
                                    <option value="correspondance">Correspondance</option>
                                    <option value="choix multiples">Choix multiples</option>
                                    <option value="commander">Commander</option>
                                    <option value="vrai faux">Vrai / Faux</option>
                                    <option value="banque de mots">Banque de mots</option>
                                </select>

                            </div>
                            <div class="col-md-6">
                                <input type="text" name="question[reponse_correct]" placeholder="Réponse correcte"
                                    class="form-control mb-2">
                            </div>
                            <div class="col-md-12">
                                <textarea type="text" name="question[explication]" placeholder="Explication"
                                    class="form-control mb-2"></textarea>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" name="question[points]" placeholder="Points" class="form-control mb-2">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="question[astuce]" placeholder="Astuce" class="form-control mb-2">

                            </div>
                        </div>
                    </div>
                </div>


                {{-- RÉPONSE --}}
                <div class="card mb-4 px-4" id="reponse-card">
                    <div class="card-body">
                        <h5 class="text-wizi">Ajouter une réponse</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="reponse[text]" placeholder="Texte de la réponse"
                                    class="form-control mb-2">

                            </div>
                            <div class="col-md-6">
                                <select name="reponse[is_correct]" class="form-select mb-2">
                                    <option value="">Bonne réponse ?</option>
                                    <option value="1">Oui</option>
                                    <option value="0">Non</option>
                                </select>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" name="reponse[position]" placeholder="Position"
                                    class="form-control mb-2">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="reponse[match_pair]" placeholder="Pair correspondante"
                                    class="form-control mb-2">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="reponse[bank_group]" placeholder="Groupe"
                                    class="form-control mb-2">

                            </div>
                            <div class="col-md-6">
                                <textarea name="reponse[flashcard_back]" placeholder="Verso de la flashcard"
                                    class="form-control"></textarea>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5">Tout enregistrer</button>
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
        $(document).ready(function () {
            function checkQuizFieldsFilled() {
                let isValid = true;

                $('#quiz-form input[required], #quiz-form select[required]').each(function () {
                    const value = $(this).val();
                    if (!value || value.trim() === '') {
                        isValid = false;
                        return false; // sort de la boucle dès qu'un champ est vide
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

            // Sur changement des champs du quiz
            $('#quiz-form input, #quiz-form select').on('input change', function () {
                checkQuizFieldsFilled();
            });

            // Vérifie au chargement de la page (utile si valeurs déjà présentes)
            checkQuizFieldsFilled();
        });
    </script>
@endsection