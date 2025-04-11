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
                <form class="row g-3" action="{{ route('quiz.store') }}" method="POST">
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
                            <label for="description">Duree</label>
                            <input type="text" name="duree" id="v" class="form-control @error('duree') is-invalid @enderror"
                                value="{{ old('duree', $quiz->description ?? '') }}">
                            @error('duree')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="niveau">Niveau</label>
                            {{-- <input type="text" name="niveau" id="niveau"
                                class="form-control @error('niveau') is-invalid @enderror"
                                value="{{ old('duree', $quiz->niveau ?? '') }}"> --}}
                            <select name="niveau" id="niveau" class="form-select @error('niveau') is-invalid @enderror">
                                <option value="" {{ old('niveau') == '' ? 'selected' : '' }}> Choisir un
                                    niveau </option>
                                <option value="débutant" {{ old('niveau') == "débutant" ? 'selected' : '' }}>débutant</option>
                                <option value="intermédiaire" {{ old('niveau') == "intermédiaire" ? 'selected' : '' }}>
                                    intermédiaire
                                </option>
                                <option value="avancé" {{ old('niveau') == "avancé" ? 'selected' : '' }}>avancé</option>
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
                                @foreach($formations as $formation)
                                    <option value="{{ $formation->id }}" {{ old('formation_id', $formation->formation_id ?? '') == $formation->id ? 'selected' : '' }}>
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
    </div>
@endsection
@section('scripts')

@endsection