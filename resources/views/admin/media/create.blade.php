@extends('admin.layout')
@section('title', 'Ajouter un medias')
@section('content')
    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">

                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Création d'un
                                medias</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('medias.index') }}" type="button" class="btn btn-sm btn-primary"><i
                                class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
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
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="container py-4">
                    <div class="card">
                        <div class="card-body p-4">
                            <form class="row g-4" action="{{ route('medias.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="col-md-6">
                                    <!-- Nom -->
                                    <div class="mb-3">
                                        <label for="titre" class="form-label">Nom</label>
                                        <input type="text" name="titre" id="titre"
                                            class="form-control @error('titre') is-invalid @enderror"
                                            value="{{ old('titre', $medias->titre ?? '') }}">
                                        @error('titre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Fichier -->
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Fichier (image, vidéo ou PDF)</label>
                                        <input type="file" name="url" id="file"
                                            class="form-control @error('url') is-invalid @enderror">
                                        @error('url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Formation -->
                                    <div class="mb-3">
                                        <label for="formation_id" class="form-label">Formation</label>
                                        <select name="formation_id" id="formation_id"
                                            class="form-select @error('formation_id') is-invalid @enderror">
                                            <option value="">Choisir une formation</option>
                                            @foreach ($formations as $formation)
                                                <option value="{{ $formation->id }}"
                                                    {{ old('formation_id', $medias->formation_id ?? '') == $formation->id ? 'selected' : '' }}>
                                                    {{ $formation->titre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('formation_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Type -->
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Type</label>
                                        <select name="type" id="type"
                                            class="form-select @error('type') is-invalid @enderror">
                                            <option value="">Choisir un type</option>
                                            <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Vidéo
                                            </option>
                                            <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>
                                                Document</option>
                                            <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Image
                                            </option>
                                            <option value="audio" {{ old('type') == 'audio' ? 'selected' : '' }}>Audio
                                            </option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Catégorie -->
                                    <div class="mb-3">
                                        <label for="categorie" class="form-label">Catégorie</label>
                                        <select name="categorie" id="categorie"
                                            class="form-select @error('categorie') is-invalid @enderror">
                                            <option value="">Choisir une catégorie</option>
                                            <option value="tutoriel"
                                                {{ old('categorie') == 'tutoriel' ? 'selected' : '' }}>Tutoriel</option>
                                            <option value="astuce" {{ old('categorie') == 'astuce' ? 'selected' : '' }}>
                                                Astuce</option>
                                        </select>
                                        @error('categorie')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Durée -->
                                    <div class="mb-3">
                                        <label for="duree" class="form-label">Durée (en minutes)</label>
                                        <input type="number" name="duree" id="duree"
                                            class="form-control @error('duree') is-invalid @enderror"
                                            value="{{ old('duree', $medias->duree ?? '') }}">
                                        @error('duree')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Ordre -->
                                    <div class="mb-3">
                                        <label for="ordre" class="form-label">Ordre</label>
                                        <input type="number" name="ordre" id="ordre"
                                            class="form-control @error('ordre') is-invalid @enderror"
                                            value="{{ old('ordre', $medias->ordre ?? '') }}">
                                        @error('ordre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                            rows="8">{{ old('description', $medias->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <hr>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="lni lni-save"></i> Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
