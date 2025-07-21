@extends('admin.layout')
@section('content')
<div class="container">
    <h1>Modifier le partenaire</h1>
    <form action="{{ route('partenaires.update', $partenaire->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="identifiant" class="form-label">Identifiant</label>
            <input type="text" name="identifiant" class="form-control" value="{{ $partenaire->identifiant }}" required>
        </div>
        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" name="adresse" class="form-control" value="{{ $partenaire->adresse }}" required>
        </div>
        <div class="mb-3">
            <label for="ville" class="form-label">Ville</label>
            <input type="text" name="ville" class="form-control" value="{{ $partenaire->ville }}" required>
        </div>
        <div class="mb-3">
            <label for="departement" class="form-label">Département</label>
            <input type="text" name="departement" class="form-control" value="{{ $partenaire->departement }}" required>
        </div>
        <div class="mb-3">
            <label for="code_postal" class="form-label">Code postal</label>
            <input type="text" name="code_postal" class="form-control" value="{{ $partenaire->code_postal }}" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <input type="text" name="type" class="form-control" value="{{ $partenaire->type }}" required>
        </div>
        <div class="mb-3">
            <label for="logo" class="form-label">Logo</label>
            @if($partenaire->logo)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $partenaire->logo) }}" alt="Logo" style="max-width:120px;max-height:120px;">
                </div>
            @endif
            <input type="file" name="logo" class="form-control" accept="image/*">
        </div>
        <div class="mb-3">
            <label for="stagiaires" class="form-label">Stagiaires associés</label>
            <select name="stagiaires[]" class="form-select" multiple>
                @foreach($stagiaires as $stagiaire)
                    <option value="{{ $stagiaire->id }}" @if($partenaire->stagiaires->contains($stagiaire->id)) selected @endif>{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer</button>
        <a href="{{ route('partenaires.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
