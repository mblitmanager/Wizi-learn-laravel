@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Modification d'un media</li>
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
            <div class="card">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body p-4 border rounded">
                    <div class="card-body p-4 border rounded">
                        <form class="row g-3" action="{{ route('medias.update', $media->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <!-- Nom -->
                                <div class="mb-3">
                                    <label for="titre">Nom</label>
                                    <input type="text" name="titre" id="titre"
                                        class="form-control @error('titre') is-invalid @enderror"
                                        value="{{ old('titre', $media->titre) }}">
                                    @error('titre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Fichier -->
                                <div class="mb-3">
                                    <label for="file">Fichier (image, vidéo ou PDF)</label>
                                    <input type="file" name="url" id="file"
                                        class="form-control @error('url') is-invalid @enderror" accept="image/*, video/*">
                                    @error('url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if (!empty($media->url))
                                        <small class="form-text text-muted">
                                            Fichier actuel :
                                            <a href="{{ asset($media->url) }}" target="_blank">Voir le fichier</a>
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Formation -->
                                <div class="mb-3">
                                    <label for="formation_id">Formation</label>
                                    <select name="formation_id" id="formation_id"
                                        class="form-select @error('formation_id') is-invalid @enderror">
                                        <option value=""
                                            {{ old('formation_id', $media->formation_id) == '' ? 'selected' : '' }}>Choisir
                                            une formation</option>
                                        @foreach ($formations as $formation)
                                            <option value="{{ $formation->id }}"
                                                {{ old('formation_id', $media->formation_id) == $formation->id ? 'selected' : '' }}>
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
                                    <label for="type">Type</label>
                                    <select name="type" id="type"
                                        class="form-select @error('type') is-invalid @enderror">
                                        <option value="" {{ old('type', $media->type) == '' ? 'selected' : '' }}>
                                            Choisir un type</option>
                                        <option value="video"
                                            {{ old('type', $media->type) == 'video' ? 'selected' : '' }}>Vidéo</option>
                                        <option value="document"
                                            {{ old('type', $media->type) == 'document' ? 'selected' : '' }}>Document
                                        </option>
                                        <option value="image"
                                            {{ old('type', $media->type) == 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="audio"
                                            {{ old('type', $media->type) == 'audio' ? 'selected' : '' }}>Audio</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Catégorie -->
                                <div class="mb-3">
                                    <label for="categorie">Catégorie</label>
                                    <select name="categorie" id="categorie"
                                        class="form-select @error('categorie') is-invalid @enderror">
                                        <option value=""
                                            {{ old('categorie', $media->categorie) == '' ? 'selected' : '' }}>Choisir une
                                            catégorie</option>
                                        <option value="tutoriel"
                                            {{ old('categorie', $media->categorie) == 'tutoriel' ? 'selected' : '' }}>
                                            Tutoriel</option>
                                        <option value="astuce"
                                            {{ old('categorie', $media->categorie) == 'astuce' ? 'selected' : '' }}>Astuce
                                        </option>
                                    </select>
                                    @error('categorie')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Durée -->
                                <div class="mb-3">
                                    <label for="duree">Durée (en minutes)</label>
                                    <input type="number" name="duree" id="duree"
                                        class="form-control @error('duree') is-invalid @enderror"
                                        value="{{ old('duree', $media->duree) }}">
                                    @error('duree')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Ordre -->
                                <div class="mb-3">
                                    <label for="ordre">Ordre</label>
                                    <input type="number" name="ordre" id="ordre"
                                        class="form-control @error('ordre') is-invalid @enderror"
                                        value="{{ old('ordre', $media->ordre) }}">
                                    @error('ordre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Description -->
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="5">{{ old('description', $media->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-sm btn-primary px-4">
                                    <i class="lni lni-save"></i> Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')

@endsection
