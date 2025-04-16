@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('quiz.index') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Quiz</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('quiz.index') }}" type="button" class="btn btn-primary px-4"> <i
                        class="fadeIn animated bx bx-chevron-left-circle"></i> Retour</a>
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
    <div class="card-body">
        <h5 class="card-title">Ajouter Quiz</h5>
        <hr>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card-body p-4 border rounded">
                <div class="card">
                    <form action="{{ route('quiz.update', $quiz->id) }}" method="POST" class="px-4 py-4"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- QUIZ --}}
                        <div class="card mb-4 px-4" id="quiz-form">
                            <div class="card-body">
                                <h5 class="text-wizi">Modifier un quiz</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Durée</label>

                                        <input type="number" name="quiz[duree]" placeholder="Durée"
                                            class="form-control mb-2" value="{{ old('quiz.duree', $quiz->duree) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Description</label>

                                        <input type="text" name="quiz[description]" placeholder="Description"
                                            class="form-control mb-2"
                                            value="{{ old('quiz.description', $quiz->description) }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Titre</label>

                                        <input type="text" name="quiz[titre]" placeholder="Titre"
                                            class="form-control mb-2" value="{{ old('quiz.titre', $quiz->titre) }}"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Niveau</label>

                                        <select name="quiz[niveau]" class="form-select mb-2">
                                            <option value="">Niveau</option>
                                            <option value="débutant"
                                                {{ old('quiz.niveau', $quiz->niveau) == 'débutant' ? 'selected' : '' }}>
                                                Débutant</option>
                                            <option value="intermédiaire"
                                                {{ old('quiz.niveau', $quiz->niveau) == 'intermédiaire' ? 'selected' : '' }}>
                                                Intermédiaire</option>
                                            <option value="avancé"
                                                {{ old('quiz.niveau', $quiz->niveau) == 'avancé' ? 'selected' : '' }}>
                                                Avancé
                                            </option>
                                        </select>
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

                        {{-- QUESTION --}}
                        <div class="card mb-4 px-4" id="question-card">
                            <div class="card-body">
                                <div class="row d-flex align-items-center justify-content-between mb-3">
                                    <div class="col-md-6">
                                        <h5 class="text-wizi">Modifier une question</h5>

                                    </div>
                                    <div class="col-md-6 d-flex align-items-end justify-content-end">
                                        <div id="add-reponse-btn"
                                            class="d-flex align-items-center theme-icons shadow-sm p-2 cursor-pointer rounded">
                                            <div class="font-22 text-primary">
                                                <i class="fadeIn animated bx bx-message-square-add"></i>
                                            </div>
                                            <div class="ms-2">plus de réponses</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Texte de la question</label>

                                        <input type="text" name="question[text]" placeholder="Texte de la question"
                                            class="form-control mb-2" value="{{ old('question.text', $question->text) }}"
                                            required>
                                    </div>
                                    <div class="col-md-6">

                                        <label for="media_file" class="form-label">Media URL</label>

                                        <input type="file" name="question_media_file" id="media_file"
                                            class="form-control mb-2"
                                            accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx">
                                        @if ($question->media_url)
                                            <small>Fichier actuel :
                                                <a href="{{ asset('storage/' . $question->media_url) }}" target="_blank">
                                                    Voir le fichier
                                                </a>
                                            </small>
                                        @endif
                                        {{-- Aperçu dynamique ici --}}
                                        <div id="preview-container" class="mt-2" style="display: none;">
                                            <strong>Aperçu :</strong>
                                            <img id="media-preview" src="#" alt="Aperçu de l'image"
                                                class="img-fluid mt-2" style="max-height: 200px;">
                                        </div>


                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Type</label>

                                        <select name="question[type]" class="form-select mb-2" required>
                                            <option value="">Type</option>
                                            <option value="question audio"
                                                {{ old('question.type', $question->type) == 'question audio' ? 'selected' : '' }}>
                                                Question audio</option>
                                            <option value="remplir le champ vide"
                                                {{ old('question.type', $question->type) == 'remplir le champ vide' ? 'selected' : '' }}>
                                                Remplir le champ vide</option>
                                            <option value="carte flash"
                                                {{ old('question.type', $question->type) == 'carte flash' ? 'selected' : '' }}>
                                                Carte flash</option>
                                            <option value="correspondance"
                                                {{ old('question.type', $question->type) == 'correspondance' ? 'selected' : '' }}>
                                                Correspondance</option>
                                            <option value="choix multiples"
                                                {{ old('question.type', $question->type) == 'choix multiples' ? 'selected' : '' }}>
                                                Choix multiples</option>
                                            <option value="commander"
                                                {{ old('question.type', $question->type) == 'commander' ? 'selected' : '' }}>
                                                Commander</option>
                                            <option value="vrai faux"
                                                {{ old('question.type', $question->type) == 'vrai faux' ? 'selected' : '' }}>
                                                Vrai / Faux</option>
                                            <option value="banque de mots"
                                                {{ old('question.type', $question->type) == 'banque de mots' ? 'selected' : '' }}>
                                                Banque de mots</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Réponse correcte</label>

                                        <input type="text" name="question[reponse_correct]"
                                            placeholder="Réponse correcte" class="form-control mb-2"
                                            value="{{ old('question.reponse_correct', $question->reponse_correct) }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label for="input1" class="form-label">Explication</label>

                                        <textarea type="text" name="question[explication]" placeholder="Explication" class="form-control mb-2">{{ old('question.explication', $question->explication) }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Points</label>

                                        <input type="number" name="question[points]" placeholder="Points"
                                            class="form-control mb-2"
                                            value="{{ old('question.points', $question->points) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="input1" class="form-label">Astuce</label>

                                        <input type="text" name="question[astuce]" placeholder="Astuce"
                                            class="form-control mb-2"
                                            value="{{ old('question.astuce', $question->astuce) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RÉPONSE --}}
                        <div class="card mb-4 px-4" id="reponse-card">
                            <div class="card-body">
                                <h5 class="text-wizi">Modifier les réponses</h5>
                                <div id="reponses-container">
                                    @foreach ($question->reponses as $index => $reponse)
                                        <div class="reponse-form mb-4 mt-4">
                                            <input type="hidden" name="reponse[id][{{ $index }}]"
                                                value="{{ $reponse->id }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="input1" class="form-label">Texte de la réponse</label>

                                                    <input type="text" name="reponse[text][{{ $index }}]"
                                                        placeholder="Texte de la réponse" class="form-control mb-2"
                                                        value="{{ old("reponse.text.$index", $reponse->text) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="input1" class="form-label">Bonne réponse ?</label>

                                                    <select name="reponse[is_correct][{{ $index }}]"
                                                        class="form-select mb-2">
                                                        <option value="">Bonne réponse ?</option>
                                                        <option value="1"
                                                            {{ old("reponse.is_correct.$index", $reponse->is_correct) == '1' ? 'selected' : '' }}>
                                                            Oui</option>
                                                        <option value="0"
                                                            {{ old("reponse.is_correct.$index", $reponse->is_correct) == '0' ? 'selected' : '' }}>
                                                            Non</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="input1" class="form-label">Position</label>

                                                    <input type="number" name="reponse[position][{{ $index }}]"
                                                        placeholder="Position" class="form-control mb-2"
                                                        value="{{ old("reponse.position.$index", $reponse->position) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="input1" class="form-label">Pair correspondante</label>

                                                    <input type="text" name="reponse[match_pair][{{ $index }}]"
                                                        placeholder="Pair correspondante" class="form-control mb-2"
                                                        value="{{ old("reponse.match_pair.$index", $reponse->match_pair) }}">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="input1" class="form-label">Groupe</label>

                                                    <input type="text" name="reponse[bank_group][{{ $index }}]"
                                                        placeholder="Groupe" class="form-control mb-2"
                                                        value="{{ old("reponse.bank_group.$index", $reponse->bank_group) }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="input1" class="form-label">Verso de la flashcard</label>

                                                    <textarea name="reponse[flashcard_back][{{ $index }}]" placeholder="Verso de la flashcard"
                                                        class="form-control mb-2">{{ old("reponse.flashcard_back.$index", $reponse->flashcard_back) }}</textarea>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-reponse-btn px-4"><i
                                                        class="fadeIn animated bx bx-trash-alt"></i>Supprimer</button>
                                            </div>
                                        </div>
                                        <hr>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        {{-- SUBMIT --}}
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-5"> <i class="lni lni-save"></i>Mettre à
                                jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.getElementById('add-reponse-btn').addEventListener('click', function() {
            const container = document.getElementById('reponses-container');
            const firstForm = container.querySelector('.reponse-form');

            const newForm = firstForm.cloneNode(true);

            // Réinitialise tous les champs et corrige les noms
            newForm.querySelectorAll('input, textarea, select').forEach(field => {
                field.value = '';
                field.name = field.name.replace(/\[\d+\]/g, '[]'); // Corrige les noms indexés
            });

            // Supprime le champ hidden de l’ID s’il existe (ne pas envoyer d’ID pour les nouveaux)
            const hiddenId = newForm.querySelector('input[type="hidden"][name^="reponse[id]"]');
            if (hiddenId) hiddenId.remove();

            container.appendChild(newForm);
        });

        // Suppression d'une réponse (mais en gardant au moins une)
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

    <script>
        document.getElementById('media_file').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('media-preview');

            if (file) {
                const fileType = file.type;

                // Afficher uniquement un aperçu si c’est une image
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

@endsection
