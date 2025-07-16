@extends('layouts.admin')
@section('content')
<div class="container">
    <h1 class="mb-4">Gestion des Succès</h1>
    <a href="{{ route('admin.achievements.create') }}" class="btn btn-primary mb-3">Ajouter un succès</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Palier</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($achievements as $achievement)
            <tr>
                <td>{{ $achievement->id }}</td>
                <td>{{ $achievement->name }}</td>
                <td>{{ $achievement->description }}</td>
                <td>{{ $achievement->tier }}</td>
                <td>
                    <a href="{{ route('admin.achievements.edit', $achievement->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                    <form action="{{ route('admin.achievements.destroy', $achievement->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce succès ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
