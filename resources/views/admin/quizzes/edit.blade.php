@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
<div class="container-fluid">
    <div class="shadow-lg border-0 px-2 py-2 mb-3">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('quiz.index') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modification d'un
                            Quiz</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <button class="btn btn-sm text-white btn-info mx-2" data-bs-toggle="modal"
                        data-bs-target="#importModal"><i class="lni lni-cloud-download"></i>importer quiz
                    </button>
                    <a href="{{ route('quiz.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                            class="fadeIn animated bx bx-chevron-left-circle"></i> Retour</a>
                    <form action="{{ route('quiz.duplicate', $quiz->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm mx-2" onclick="return confirm('Dupliquer ce quiz ?')">
                            <i class="lni lni-copy"></i> Dupliquer
                        </button>
                    </form>
                    @if($quiz->status === 'inactif')
                    <form action="{{ route('quiz.enable', $quiz->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm mx-2" onclick="return confirm('Réactiver ce quiz ?')">
                            <i class="lni lni-checkmark-circle"></i> Réactiver
                        </button>
                    </form>
                    @else
                    <form action="{{ route('quiz.disable', $quiz->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm mx-2" onclick="return confirm('Désactiver ce quiz ?')">
                            <i class="lni lni-ban"></i> Désactiver
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('quiz.destroy', $quiz->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm mx-2" onclick="return confirm('Supprimer ce quiz ?')">
                            <i class="lni lni-trash"></i> Supprimer
                        </button>
                    </form>
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
            <div class="card-body">
                <form action="{{ route('quiz.update', $quiz->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                    {{-- QUIZ --}}
                    <div class=" mb-4 px-2" id="quiz-form">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Titre</label>

                                    <input type="text" name="quiz[titre]" placeholder="Titre"
                                        class="form-control mb-2" value="{{ old('quiz.titre', $quiz->titre) }}"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Description</label>

                                    <textarea type="text" name="quiz[description]" placeholder="Description"
                                        id="description" class="form-control mb-2"
                                        value="{{ old('quiz.description', $quiz->description) }}">{{ old('quiz.description', $quiz->description) }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Niveau</label>

                                    <select name="quiz[niveau]" class="form-select mb-2">
                                        <option value="">Niveau</option>
                                        <option value="débutant"
                                            {{ old('quiz.niveau', $quiz->niveau) == 'débutant' ? 'selected' : '' }}>
                                            Débutant
                                        </option>
                                        <option value="intermédiaire"
                                            {{ old('quiz.niveau', $quiz->niveau) == 'intermédiaire' ? 'selected' : '' }}>
                                            Intermédiaire
                                        </option>
                                        <option value="avancé"
                                            {{ old('quiz.niveau', $quiz->niveau) == 'avancé' ? 'selected' : '' }}>
                                            Avancé
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Durée</label>

                                    <input type="number" name="quiz[duree]" placeholder="Durée"
                                        class="form-control mb-2" value="{{ old('quiz.duree', $quiz->duree) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Formation</label>

                                    <select name="quiz[formation_id]" class="form-select mb-2">
                                        @foreach ($formations as $formation)
                                        <option value="{{ $formation->id }}"
                                            {{ old('quiz.formation_id', $quiz->formation_id) == $formation->id ? 'selected' : '' }}>
                                            {{ $formation->titre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="input1" class="form-label">Points total</label>

                                    <input type="number" name="quiz[nb_points_total]" placeholder="Points total"
                                        class="form-control"
                                        value="{{ old('quiz.nb_points_total', $quiz->nb_points_total) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="">Rechercher une question</h5>
                                <hr>
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <!-- Input de recherche et boutons "Rechercher" et "Réinitialiser" -->
                                        <div class="d-flex align-items-center">
                                            <!-- Agrandir l'input de recherche -->
                                            <input type="text" id="searchInput" name="search"
                                                placeholder="Rechercher une question"
                                                class="form-control me-2 form-control-sm" style=" width: 400px;"
                                                value="">
                                            <button id="searchBtn"
                                                class="btn btn-sm btn-primary me-2 d-flex align-items-center">
                                                <i class="lni lni-search-alt me-1"></i> Rechercher
                                            </button>
                                            <button id="resetBtn"
                                                class="btn btn-sm btn-warning d-flex align-items-center">
                                                <i class="lni lni-spinner-arrow me-1"></i> Réinitialiser
                                            </button>
                                        </div>

                                        <div>
                                            <button type="button" class="btn btn-sm btn-default"
                                                data-bs-toggle="modal" data-bs-target="#NewQuestionModal"><i
                                                    class="lni lni-plus"></i>Nouveau
                                                questions
                                            </button>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                        {{-- QUESTIONS & REPONSES --}}
                        <div class="accordion mb-4 shadow-sm" id="accordionQuestions">
                            @foreach ($questions as $qIndex => $question)
                            <div class="accordion-item question-item">
                                <input type="hidden" name="questions[{{ $qIndex }}][_delete]"
                                    value="0" class="delete-flag">

                                <div class="accordion-header" id="heading{{ $qIndex }}">
                                    <h2 class="accordion-header d-flex align-items-center justify-content-between px-3 py-2 bg-light"
                                        id="heading{{ $qIndex }}" style="border-bottom: 1px solid #ddd;">
                                        <button
                                            class="accordion-button flex-grow-1 {{ $qIndex > 0 ? 'collapsed' : '' }}"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse{{ $qIndex }}"
                                            aria-expanded="{{ $qIndex == 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $qIndex }}"
                                            style="box-shadow: none; background: none;">
                                            <div
                                                class="d-flex flex-column flex-md-row justify-content-between w-100">
                                                <span><strong>Question #{{ $question->id }}</strong></span>
                                                <span class="text-muted mx-2">{{ $question->text }}</span>
                                            </div>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm remove-question-btn">
                                            <i class="lni lni-trash"></i>
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapse{{ $qIndex }}"
                                    class="accordion-collapse collapse {{ $qIndex == 0 ? 'show' : '' }}"
                                    aria-labelledby="heading{{ $qIndex }}"
                                    data-bs-parent="#accordionQuestions">
                                    <div class="accordion-body">
                                        <div class="px-3 py-3 mb-3"
                                            style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">
                                            <input type="hidden" name="questions[{{ $qIndex }}][id]"
                                                value="{{ $question->id }}">

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Texte de la question</label>
                                                    <input type="text"
                                                        name="questions[{{ $qIndex }}][text]"
                                                        class="form-control"
                                                        value="{{ old("questions.$qIndex.text", $question->text) }}"
                                                        required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Media URL</label>
                                                    <input type="file"
                                                        name="questions[{{ $qIndex }}][media_file]"
                                                        class="form-control"
                                                        accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.mp4,.mp3">
                                                    @if ($question->media_url)
                                                    <small>
                                                        <a href="{{ asset(  $question->media_url) }}"
                                                            target="_blank">Voir le fichier</a>
                                                    </small>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Type</label>
                                                    <select name="questions[{{ $qIndex }}][type]"
                                                        class="form-select" required>
                                                        <option value="">Type</option>
                                                        @foreach (['question audio', 'remplir le champ vide', 'carte flash', 'correspondance', 'choix multiples', 'rearrangement', 'vrai/faux', 'banque de mots'] as $type)
                                                        <option value="{{ $type }}"
                                                            {{ old("questions.$qIndex.type", $question->type) == $type ? 'selected' : '' }}>
                                                            {{ ucfirst($type) }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Points</label>
                                                    <input type="number"
                                                        name="questions[{{ $qIndex }}][points]"
                                                        class="form-control"
                                                        value="{{ old("questions.$qIndex.points", $question->points ?: 2) }}"
                                                        min="1">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Réponse correcte</label>
                                                    <input type="text"
                                                        name="questions[{{ $qIndex }}][reponse_correct]"
                                                        class="form-control"
                                                        value="{{ old("questions.$qIndex.reponse_correct", $question->reponse_correct) }}">
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Explication</label>
                                                    <textarea name="questions[{{ $qIndex }}][explication]" class="form-control" rows="3">{{ old("questions.$qIndex.explication", $question->explication) }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Réponses --}}

                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mt-4">Réponses</h6>
                                            <button type="button" class="btn btn-primary btn-sm add-reponse-btn"
                                                data-question-index="{{ $qIndex }}">
                                                Ajouter une réponse
                                            </button>
                                        </div>
                                        <div id="reponses-container-{{ $qIndex }}">
                                            @foreach ($question->reponses as $rIndex => $reponse)
                                            <div class="px-3 py-3 mb-3 reponse-form"
                                                style="box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;">
                                                <input type="hidden"
                                                    name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][id]"
                                                    value="{{ $reponse->id }}">
                                                <input type="hidden" class="delete-flag" name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][_delete]" value="0">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Texte de la
                                                            réponse</label>
                                                        <input type="text"
                                                            name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][text]"
                                                            class="form-control mb-2"
                                                            value="{{ old("questions.$qIndex.reponses.$rIndex.text", $reponse->text) }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Bonne réponse ?</label>
                                                        <select
                                                            name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][is_correct]"
                                                            class="form-select mb-2">
                                                            <option value="1"
                                                                {{ $reponse->is_correct ? 'selected' : '' }}>
                                                                Oui
                                                            </option>
                                                            <option value="0"
                                                                {{ !$reponse->is_correct ? 'selected' : '' }}>
                                                                Non
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Position</label>
                                                        <input type="number"
                                                            name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][position]"
                                                            class="form-control"
                                                            value="{{ old("questions.$qIndex.reponses.$rIndex.position", $reponse->position) }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Pair
                                                            correspondante</label>
                                                        <input type="text"
                                                            name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][match_pair]"
                                                            class="form-control"
                                                            value="{{ old("questions.$qIndex.reponses.$rIndex.match_pair", $reponse->match_pair) }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Groupe</label>
                                                        <input type="text"
                                                            name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][bank_group]"
                                                            class="form-control"
                                                            value="{{ old("questions.$qIndex.reponses.$rIndex.bank_group", $reponse->bank_group) }}">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Dos de carte</label>
                                                        <input type="text"
                                                            name="questions[{{ $qIndex }}][reponses][{{ $rIndex }}][flashcard_back]"
                                                            class="form-control"
                                                            value="{{ old("questions.$qIndex.reponses.$rIndex.flashcard_back", $reponse->flashcard_back) }}">
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm remove-reponse-btn">
                                                        Supprimer cette réponse
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        {{-- template caché pour réponse --}}
                                        <template id="reponse-template-{{ $qIndex }}">
                                            <div class="px-3 py-3 mb-3 reponse-form"
                                                style="box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Texte de la réponse</label>
                                                        <input type="text"
                                                            name="questions[{{ $qIndex }}][reponses][__RINDEX__][text]"
                                                            class="form-control mb-2">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Bonne réponse ?</label>
                                                        <select
                                                            name="questions[{{ $qIndex }}][reponses][__RINDEX__][is_correct]"
                                                            class="form-select mb-2">
                                                            <option value="1">Oui</option>
                                                            <option value="0">Non</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Position</label>
                                                        <input type="number"
                                                            name="questions[{{ $qIndex }}][reponses][__RINDEX__][position]"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Pair correspondante</label>
                                                        <input type="text"
                                                            name="questions[{{ $qIndex }}][reponses][__RINDEX__][match_pair]"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Groupe</label>
                                                        <input type="text"
                                                            name="questions[{{ $qIndex }}][reponses][__RINDEX__][bank_group]"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Dos de carte</label>
                                                        <input type="text"
                                                            name="questions[{{ $qIndex }}][reponses][__RINDEX__][flashcard_back]"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm remove-reponse-btn">
                                                        Supprimer cette réponse
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div id="noResults" class="alert alert-warning d-none">
                            Aucun résultat trouvé.
                        </div>

                        <div class="d-flex justify-content-center">
                            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                <ul class="pagination" id="paginationNumbersWrapper">
                                    <li class="paginate_button page-item previous disabled" id="prevPage">
                                        <a href="#" class="page-link">Précédent</a>
                                    </li>
                                    <!-- Les numéros de page seront insérés ici -->
                                    <li class="paginate_button page-item next" id="nextPage">
                                        <a href="#" class="page-link">Suivant</a>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                    {{-- SUBMIT --}}
                    <div class="text-center">
                        <button type="submit" class="btn btn-sm btn-success px-4"><i class="lni lni-save"></i>Mettre
                            à
                            jour
                        </button>
                    </div>
                </form>
                <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Importer stagiaires</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('quiz_question.import') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Fichier Excel (.xlsx)</label>
                                        <input type="file" name="file" id="file" class="form-control"
                                            required accept=".xlsx,.xls">
                                    </div>

                                    <div class="progress mb-3 d-none" id="progressBarWrapper">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" style="width: 100%;" id="progressBar">
                                            Importation en cours...
                                        </div>
                                    </div>


                                    <button type="submit" class="btn btn-sm btn-primary">Importer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
{{-- MODAL NEW QUESTION FOR ONE QUIZ --}}
<div class="modal fade" id="NewQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" action="{{ route('quiz_question.new') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une Nouvelle Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Texte de la question</label>
                            <input type="text" name="text" class="form-control"
                                placeholder="Entrez votre question..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Type de question</label>
                            <select name="question[type]" class="form-select" required>
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
                            <label class="form-label">Explication (optionnel)</label>
                            <textarea name="explication" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Astuce (optionnel)</label>
                            <textarea name="astuce" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Points</label>
                            <input type="number" name="points" class="form-control" value="1" min="1"
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Média (optionnel)</label>
                            <input type="file" name="media_file" class="form-control">
                        </div>
                    </div>

                    <hr>

                    <h5 class="text-primary mb-3 py-2">Ajouter des réponses</h5>

                    <div id="reponses-container">
                        <div class="reponse-item mb-4">
                            <div class="row g-3 reponse-item mb-3 py-3"
                                style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">

                                <div class="col-md-6">
                                    <label for="">Texte de la réponse</label>
                                    <input type="text" name="reponses[0][text]" class="form-control"
                                        placeholder="Texte de la réponse" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Bonne réponse ?</label>
                                    <select name="reponses[0][is_correct]" class="form-select" required>
                                        <option value="0">Non</option>
                                        <option value="1">Oui</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">
                                        Position
                                    </label>
                                    <input type="number" name="reponses[0][position]" class="form-control"
                                        placeholder="Position">
                                </div>
                                <div class="col-md-6">
                                    <label for="">Pair correspondante</label>
                                    <input type="text" name="reponses[0][match_pair]" class="form-control"
                                        placeholder="Match pair">
                                </div>
                                <div class="col-md-6">
                                    <label for="">Groupe</label>
                                    <input type="text" name="reponses[0][bank_group]" class="form-control"
                                        placeholder="Groupe banque">
                                </div>
                                <div class="col-md-6">
                                    <label for="">Dos de la flashcard</label>
                                    <input type="text" name="reponses[0][flashcard_back]" class="form-control"
                                        placeholder="Dos de la flashcard">
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-danger btn-sm w-100 mt-2"
                                        onclick="removeReponse(this)">Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="text-end">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addReponse()">Ajouter
                            une réponse
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                        Annuler
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer la Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    document.querySelectorAll('.add-reponse-btn').forEach(button => {
        button.addEventListener('click', function() {
            const qIndex = this.dataset.questionIndex;
            const container = document.getElementById(`reponses-container-${qIndex}`);
            const template = document.getElementById(`reponse-template-${qIndex}`).innerHTML;

            // Trouver les index déjà utilisés
            const indexes = Array.from(container.querySelectorAll(
                    '.reponse-form input[name^="questions["]'))
                .map(input => {
                    const match = input.name.match(/questions\[\d+]\[reponses]\[(\d+)]/);
                    return match ? parseInt(match[1]) : -1;
                });

            const maxIndex = indexes.length ? Math.max(...indexes) : -1;
            const newIndex = maxIndex + 1;

            // Remplacer __RINDEX__ par le nouvel index
            const newContent = template.replace(/__RINDEX__/g, newIndex);
            container.insertAdjacentHTML('beforeend', newContent);

            const newReponse = container.querySelector(`.reponse-form:last-child`);
            newReponse.classList.add(
                'new-reponse'); // Ajout d'une classe CSS pour les nouvelles réponses
            newReponse.style.border = '2px solid #4CAF50'; // Bordure verte pour les nouvelles réponses
            newReponse.style.backgroundColor = '#e8f5e9';
        });
    });

    // Gérer la suppression de réponse dynamique
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-reponse-btn')) {
            const reponseForm = e.target.closest('.reponse-form');
            console.log('Form reponse', reponseForm)
            const deleteInput = reponseForm.querySelector('.delete-flag');

            if (deleteInput) {
                // Réponse existante : marquer pour suppression
                deleteInput.value = '1';
                reponseForm.remove();
            } else {
                // Réponse ajoutée dynamiquement (pas encore enregistrée) : supprimer du DOM
                reponseForm.remove();
            }

        }
    });
</script>
{{-- Media appercu --}}
<script>
    document.getElementById('media_file').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('media-preview');

        if (file) {
            const fileType = file.type;

            // Afficher uniquement un aperçu si c'est une image
            if (fileType.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };

                reader.readAsDataURL(file);
            } else {
                // Cacher l'aperçu si ce n'est pas une image
                previewContainer.style.display = 'none';
                previewImage.src = '';
            }
        }
    });
</script>
{{-- Pagination Question --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const itemsPerPage = 10;
        const items = Array.from(document.querySelectorAll(".question-item"));
        const totalItems = items.length;
        let filteredItems = [...items];
        let currentPage = 1;
        const maxVisiblePages = 5;

        const searchInput = document.getElementById("searchInput");
        const searchBtn = document.getElementById("searchBtn");
        const resetBtn = document.getElementById("resetBtn");
        const noResults = document.getElementById("noResults");

        function showPage(page, itemsToShow = filteredItems) {
            const start = (page - 1) * itemsPerPage;
            const end = page * itemsPerPage;

            items.forEach(item => item.style.display = "none");
            itemsToShow.forEach((item, index) => {
                if (index >= start && index < end) {
                    item.style.display = "block";
                }
            });

            noResults.classList.toggle("d-none", itemsToShow.length > 0);

            updatePaginationButtons(itemsToShow.length);
        }

        function updatePaginationButtons(itemCount = filteredItems.length) {
            const container = document.getElementById("paginationNumbersWrapper");
            const totalPages = Math.ceil(itemCount / itemsPerPage);

            // Supprimer les numéros existants entre "prev" et "next"
            while (container.children.length > 2) {
                container.removeChild(container.children[1]);
            }

            let startPage = Math.floor((currentPage - 1) / maxVisiblePages) * maxVisiblePages + 1;
            let endPage = Math.min(startPage + maxVisiblePages - 1, totalPages);

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement("li");
                li.className = `paginate_button page-item ${i === currentPage ? 'active' : ''}`;

                const a = document.createElement("a");
                a.href = "#";
                a.className = "page-link";
                a.innerText = i;
                a.addEventListener("click", (e) => {
                    e.preventDefault();
                    currentPage = i;
                    showPage(currentPage);
                });

                li.appendChild(a);
                // insérer avant le bouton "Suivant"
                container.insertBefore(li, document.getElementById("nextPage"));
            }

            const prev = document.getElementById("prevPage");
            const next = document.getElementById("nextPage");

            prev.classList.toggle("disabled", currentPage === 1);
            next.classList.toggle("disabled", currentPage === totalPages);
        }


        document.getElementById("prevPage").addEventListener("click", (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        document.getElementById("nextPage").addEventListener("click", (e) => {
            e.preventDefault();
            const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });

        searchBtn.addEventListener("click", (e) => {
            e.preventDefault();
            const query = searchInput.value.trim().toLowerCase();

            filteredItems = items.filter(item => {
                const questionText = item.querySelector(".accordion-button").innerText
                    .toLowerCase();
                return questionText.includes(query);
            });

            currentPage = 1;
            showPage(currentPage);
        });

        resetBtn.addEventListener("click", (e) => {
            e.preventDefault();
            searchInput.value = "";
            filteredItems = [...items];
            currentPage = 1;
            showPage(currentPage);
        });

        // Initial display

        showPage(currentPage);
    });
</script>
<script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-question-btn')) {
            const questionItem = e.target.closest('.question-item');
            const deleteInput = questionItem.querySelector('input.delete-flag');

            if (deleteInput) {
                deleteInput.value = '1';
                questionItem.style.display = 'none';
            }
        }
    });
</script>
<script>
    let reponseIndex = 1;

    function addReponse() {
        const container = document.getElementById('reponses-container');
        const html = `
                   <div class="row g-3 reponse-item mb-3 py-3" style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">
                <div class="col-md-6">
<label for="">Texte de la réponse</label>
                    <input type="text" name="reponses[${reponseIndex}][text]" class="form-control" placeholder="Texte de la réponse" required>
                </div>

                <div class="col-md-6">
<label for="">Bonne réponse ?</label>
                    <select name="reponses[${reponseIndex}][is_correct]" class="form-select" required>
                        <option value="0">Non</option>
                        <option value="1">Oui</option>
                    </select>
                </div>

                <div class="col-md-6">
<label for="">Position</label>
                    <input type="number" name="reponses[${reponseIndex}][position]" class="form-control" placeholder="Position">
                </div>

                <div class="col-md-6">
<label for="">Match pair</label>
                    <input type="text" name="reponses[${reponseIndex}][match_pair]" class="form-control" placeholder="Match pair">
                </div>

                <div class="col-md-6">
<label for="">Groupe</label>
                    <input type="text" name="reponses[${reponseIndex}][bank_group]" class="form-control" placeholder="Groupe banque">
                </div>

                <div class="col-md-6">
<label for="">Dos de la flashcard</label>
                    <input type="text" name="reponses[${reponseIndex}][flashcard_back]" class="form-control" placeholder="Dos de la flashcard">
                </div>

                <div class="col-md-12">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeReponse(this)">Supprimer</button>
                </div>
            </div>

        `;
        container.insertAdjacentHTML('beforeend', html);
        reponseIndex++;
    }

    function removeReponse(button) {
        button.closest('.reponse-item').remove();
    }
</script>
@endsection
