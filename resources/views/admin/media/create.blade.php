@extends('admin.layout')
@section('title', 'Ajouter un medias')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">medias</li>
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
    <div class="card-body">
        <h5 class="card-title">Ajouter medias</h5>
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
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card-body p-4 border rounded">
                <form class="row g-3" action="{{ route('medias.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="titre">Nom</label>
                            <input type="text" name="titre" id="titre"
                                class="form-control @error('titre') is-invalid @enderror"
                                value="{{ old('titre', $medias->titre ?? '') }}">
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                rows="5">{{ old('description', $media->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="file">Fichier (image, vidéo ou PDF)</label>
                            <input type="file" name="url" id="file"
                                class="form-control @error('url') is-invalid @enderror">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="type">Type</label>

                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                <option value="" {{ old('type') == '' ? 'selected' : '' }}> Choisir un
                                    type </option>
                                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>video</option>
                                <option value="document" {{ old('type') == 'document' ? 'selected' : '' }}>
                                    document
                                </option>
                                <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>image</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="categorie">Catégorie</label>
                            <select name="categorie" id="categorie" class="form-select @error('categorie') is-invalid @enderror">
                                <option value="" {{ old('categorie') == '' ? 'selected' : '' }}>Choisir une catégorie</option>
                                <option value="tutoriel" {{ old('categorie') == 'tutoriel' ? 'selected' : '' }}>Tutoriel</option>
                                <option value="astuce" {{ old('categorie') == 'astuce' ? 'selected' : '' }}>Astuce</option>
                            </select>
                            @error('categorie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="duree">Durée (en secondes)</label>
                            <input type="number" name="duree" id="duree" min="1"
                                class="form-control @error('duree') is-invalid @enderror"
                                value="{{ old('duree', $media->duree ?? '') }}">
                            @error('duree')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ordre">Ordre d'affichage</label>
                            <input type="number" name="ordre" id="ordre" min="0"
                                class="form-control @error('ordre') is-invalid @enderror"
                                value="{{ old('ordre', $media->ordre ?? '') }}">
                            @error('ordre')
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

@endsection
