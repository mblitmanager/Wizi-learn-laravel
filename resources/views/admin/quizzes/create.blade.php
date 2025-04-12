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
        <h5 class="card-title">Ajouter Quiz</h5>
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
            <div class="card-body p-4 border rounded">
                <form id="quiz-form" class="row g-3" action="{{ route('quiz.store') }}" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="titre">Nom</label>
                            <input type="text" name="titre" id="titre"
                                class="form-control @error('titre') is-invalid @enderror"
                                value="{{ old('titre', $quiz->titre ?? '') }}">
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="description">Description</label>
                            <input type="text" name="description" id="description"
                                class="form-control @error('description') is-invalid @enderror"
                                value="{{ old('description', $quiz->description ?? '') }}">
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="duree">Duree</label>
                            <input type="number" name="duree" id="duree"
                                class="form-control @error('duree') is-invalid @enderror"
                                value="{{ old('duree', $quiz->duree ?? '') }}">
                            @error('duree')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="niveau">Niveau</label>
                            <select name="niveau" id="niveau" class="form-select @error('niveau') is-invalid @enderror">
                                <option value="" {{ old('niveau') == '' ? 'selected' : '' }}> Choisir un
                                    niveau </option>
                                <option value="débutant" {{ old('niveau') == 'débutant' ? 'selected' : '' }}>débutant
                                </option>
                                <option value="intermédiaire" {{ old('niveau') == 'intermédiaire' ? 'selected' : '' }}>
                                    intermédiaire
                                </option>
                                <option value="avancé" {{ old('niveau') == 'avancé' ? 'selected' : '' }}>avancé</option>
                            </select>
                            @error('niveau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="formation_id">Formation</label>
                            <select name="formation_id" id="formation_id"
                                class="form-select @error('formation_id') is-invalid @enderror">
                                <option value="" {{ old('formation_id') == '' ? 'selected' : '' }}> Choisir un
                                    formation </option>
                                @foreach ($formations as $formation)
                                    <option value="{{ $formation->id }}"
                                        {{ old('formation_id', $formation->formation_id ?? '') == $formation->id ? 'selected' : '' }}>
                                        {{ $formation->titre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('formation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="nb_points_total">Nombre total de points</label>
                            <input type="number" name="nb_points_total" id="nb_points_total"
                                class="form-control @error('nb_points_total') is-invalid @enderror"
                                value="{{ old('nb_points_total', $quiz->nb_points_total ?? '') }}">
                            @error('nb_points_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card hidden-until-valid" id="second-card">
            <h4 class="px-3 mb-3 mt-3">Ajoute question</h4>
            <div class="card-body p-4 border rounded">
                <form class="row g-3" action="" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="text">Text</label>
                            <input type="text" name="text" id="text"
                                class="form-control @error('text') is-invalid @enderror"
                                value="{{ old('text', $question->text ?? '') }}">
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="question">Question</label>
                            <input type="text" name="question" id="question"
                                class="form-control @error('question') is-invalid @enderror"
                                value="{{ old('question', $question->question ?? '') }}">
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="media_url">Media url</label>
                            <input type="text" name="media_url" id="media_url"
                                class="form-control @error('media_url') is-invalid @enderror"
                                value="{{ old('media_url', $quiz->media_url ?? '') }}">
                            @error('media_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="type">Type</label>
                            <select name="type" id="type"
                                class="form-select @error('type') is-invalid @enderror">
                                <option value="" {{ old('type') == '' ? 'selected' : '' }}> Choisir un
                                    type </option>
                                <option value="  question audio" {{ old('type') == '  question audio' ? 'selected' : '' }}>
                                    question audio
                                </option>
                                <option value="remplir le champ vide" {{ old('type') == 'remplir le champ vide' ? 'selected' : '' }}>
                                    remplir le champ vide
                                </option>
                                <option value="carte flash" {{ old('type') == 'carte flash' ? 'selected' : '' }}>carte flash</option>
                                <option value="correspondance" {{ old('type') == 'correspondance' ? 'selected' : '' }}>correspondance</option>
                                <option value="choix multiples" {{ old('type') == 'choix multiples' ? 'selected' : '' }}>choix multiples</option>
                                <option value="commander" {{ old('type') == 'commander' ? 'selected' : '' }}>commander</option>
                                <option value="vrai faux" {{ old('type') == 'vrai faux' ? 'selected' : '' }}>vrai faux</option>
                                <option value="banque de mots" {{ old('type') == 'banque de mots' ? 'selected' : '' }}>banque de mots</option>

                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="reponse_correct">Reponse correct</label>
                            <input type="text" class="form-control @error('reponse_correct') is-invalid @enderror"
                                name="reponse_correct" id="reponse_correct">
                            @error('reponse_correct')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="immage_illustration">Image illustration</label>
                            <input type="file" class="form-control @error('immage_illustration') is-invalid @enderror"
                                name="immage_illustration" id="reponse_correct">
                            @error('immage_illustration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="explication">Explication</label>
                            <input type="text" class="form-control @error('explication') is-invalid @enderror"
                                name="explication" id="explication">
                            @error('explication')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="points">Points</label>
                            <input type="number" class="form-control @error('points') is-invalid @enderror"
                                name="points" id="points">
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="astuce">Astuce</label>
                            <input type="text" class="form-control @error('astuce') is-invalid @enderror"
                                name="astuce" id="astuce">
                            @error('astuce')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            function checkFormFilled() {
                let isValid = true;

                $('#quiz-form input, #quiz-form select').each(function() {
                    const value = $(this).val();
                    if (!value || value.trim() === '') {
                        isValid = false;
                        return false;
                    }
                });

                if (isValid) {
                    $('#second-card').removeClass('hidden-until-valid').slideDown();
                } else {
                    $('#second-card').slideUp();
                }
            }

            $('#quiz-form input, #quiz-form select').on('input change', function() {
                checkFormFilled();
            });

            checkFormFilled();
        });
    </script>

@endsection
