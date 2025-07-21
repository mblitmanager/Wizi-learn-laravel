@extends('admin.layout')
@section('content')
<div class="container">
    <h1>Ajouter un partenaire</h1>
    <form action="{{ route('partenaires.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="identifiant" class="form-label">Identifiant</label>
            <input type="text" name="identifiant" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" name="adresse" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="ville" class="form-label">Ville</label>
            <input type="text" name="ville" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="departement" class="form-label">Département</label>
            <input type="text" name="departement" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="code_postal" class="form-label">Code postal</label>
            <input type="text" name="code_postal" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <input type="text" name="type" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="logo" class="form-label">Logo</label>
            <input type="file" name="logo" class="form-control" accept="image/*">
        </div>
        <div class="mb-3">
            <label for="stagiaires" class="form-label">Stagiaires associés</label>
            <select name="stagiaires[]" class="form-select" multiple>
                @foreach($stagiaires as $stagiaire)
                    <option value="{{ $stagiaire->id }}">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Créer</button>
        <a href="{{ route('partenaires.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
