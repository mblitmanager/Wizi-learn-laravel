@extends('admin.layout')
@section('title', 'Ajouter un formations')
@section('content')
<div class="container-fluid">
    <div class="shadow-lg border-0 px-2 py-2 mb-3">

        <div class="page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('formations.index') }}"><i
                                    class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Création d'un
                            domaine de formation</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('formations.index') }}" type="button" class="btn btn-sm btn-primary"><i
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
                <form class="row g-3" action="{{ route('formations.store') }}" method="POST">
                    @csrf
                    <div class="col-md-6">
                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="titre">Titre</label>
                            <input type="text" name="titre" id="titre"
                                class="form-control @error('titre') is-invalid @enderror"
                                value="{{ old('titre', $formations->titre ?? '') }}">
                            @error('titre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description">Description</label>
                            <textarea type="text" name="description" id="description"
                                class="form-control @error('description') is-invalid @enderror">{{ old('description', $formations->description ?? '') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Catégorie -->
                        <div class="mb-3">
                            <label for="categorie">Catégorie</label>
                            <input type="text" name="categorie" id="categorie"
                                class="form-control @error('categorie') is-invalid @enderror"
                                value="{{ old('categorie', $formations->categorie ?? '') }}">
                            @error('categorie')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Durée -->
                        <div class="mb-3">
                            <label for="duree">Durée</label>
                            <input type="number" name="duree" id="duree"
                                class="form-control @error('duree') is-invalid @enderror"
                                value="{{ old('duree', $formations->duree ?? '') }}">
                            @error('duree')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Statut -->
                        <div class="mb-3">
                            <label for="statut">Statut</label>
                            <select name="statut" id="statut" class="form-control">
                                <option value="1"
                                    {{ old('statut', $formations->statut ?? '') == 1 ? 'selected' : '' }}>Actif
                                </option>
                                <option value="0"
                                    {{ old('statut', $formations->statut ?? '') == 0 ? 'selected' : '' }}>Inactif
                                </option>
                            </select>
                            @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="lni lni-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@section('scripts')
<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2({
            placeholder: "Choisir une ou plusieurs formations", // Placeholder
            allowClear: true
        });
    });
</script>
@endsection
@endsection