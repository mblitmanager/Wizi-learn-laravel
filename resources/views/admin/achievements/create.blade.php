@extends('layouts.admin')
@section('content')
<div class="container">
    <h1>Ajouter un succès</h1>
    <form action="{{ route('admin.achievements.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="tier" class="form-label">Palier</label>
            <input type="text" name="tier" id="tier" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Créer</button>
        <a href="{{ route('admin.achievements.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
