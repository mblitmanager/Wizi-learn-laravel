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
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modification d'un
                            domain formation</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('formations.index') }}" type="button" class="btn btn-sm btn-primary"><i
                            class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                            <form action="{{ route('formations.duplicate', $formation->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm mx-2" onclick="return confirm('Dupliquer cette formation ?')">
                                    <i class="lni lni-copy"></i> Dupliquer
                                </button>
                            </form>
                            <form action="{{ route('formations.destroy', $formation->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mx-2" onclick="return confirm('Supprimer cette formation ?')">
                                    <i class="lni lni-trash"></i> Supprimer
                                </button>
                            </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">

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
                <form class="row g-3" action="{{ route('formations.update', $formation->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="col-md-6">
                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="titre">Titre</label>
                            <input type="text" name="titre" id="titre"
                                class="form-control @error('titre') is-invalid @enderror"
                                value="{{ old('titre', $formation->titre ?? '') }}">
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
                                class="form-control @error('description') is-invalid @enderror">{{ old('description', $formation->description ?? '') }}</textarea>
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
                                value="{{ old('categorie', $formation->categorie ?? '') }}">
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
                                value="{{ old('duree', $formation->duree ?? '') }}">
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
                                    {{ old('statut', $formation->statut ?? '') == 1 ? 'selected' : '' }}>Actif</option>
                                <option value="0"
                                    {{ old('statut', $formation->statut ?? '') == 0 ? 'selected' : '' }}>Inactif
                                </option>
                            </select>
                            @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-sm btn-primary px-4">
                            <i class="lni lni-save"></i> Mettre à jour
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