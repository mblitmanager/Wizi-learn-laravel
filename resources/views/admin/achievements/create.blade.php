@extends('admin.layout')
@section('title', 'Ajouter un succès')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.achievements.index') }}"><i
                                        class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Création d'un
                                succès</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.achievements.index') }}" type="button"
                            class="btn btn-sm btn-primary mx-4"><i
                                class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body p-4 border rounded">
                    <form class="row g-3" action="{{ route('admin.achievements.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom</label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tier" class="form-label">Palier</label>
                                    <input type="text" name="tier" id="tier"
                                        class="form-control @error('tier') is-invalid @enderror"
                                        value="{{ old('tier') }}">
                                    @error('tier')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quiz_id" class="form-label">Quiz associé (optionnel)</label>
                                    <select name="quiz_id" id="quiz_id"
                                        class="form-control @error('quiz_id') is-invalid @enderror">
                                        <option value="">Aucun</option>
                                        @foreach ($quizzes as $quiz)
                                            <option value="{{ $quiz->id }}"
                                                {{ old('quiz_id') == $quiz->id ? 'selected' : '' }}>{{ $quiz->titre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('quiz_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-success px-4">Créer</button>
                            <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary px-4">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
