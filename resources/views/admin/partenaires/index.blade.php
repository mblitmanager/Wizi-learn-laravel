@extends('admin.layout')
@section('content')
<div class="container">
    <h1>Liste des partenaires</h1>
    <a href="{{ route('partenaires.create') }}" class="btn btn-primary mb-3">Ajouter un partenaire</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Identifiant</th>
                <th>Adresse</th>
                <th>Ville</th>
                <th>Département</th>
                <th>Code postal</th>
                <th>Type</th>
                <th>Logo</th>
                <th>Stagiaires</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partenaires as $partenaire)
            <tr>
                <td>{{ $partenaire->id }}</td>
                <td>
                    <a href="{{ route('partenaires.show', $partenaire->id) }}" class="text-decoration-underline">
                        {{ $partenaire->identifiant }}
                    </a>
                </td>
                <td>{{ $partenaire->adresse }}</td>
                <td>{{ $partenaire->ville }}</td>
                <td>{{ $partenaire->departement }}</td>
                <td>{{ $partenaire->code_postal }}</td>
                <td>{{ $partenaire->type }}</td>
                <td>
                    @if($partenaire->logo)
                        <img src="{{ asset($partenaire->logo) }}" alt="Logo" style="max-width:60px;max-height:60px;">
                    @endif
                </td>
                <td>
                    @foreach($partenaire->stagiaires as $stagiaire)
                        <span class="badge bg-info">{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</span>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('partenaires.show', $partenaire->id) }}" class="btn btn-sm btn-info">Afficher</a>
                    <a href="{{ route('partenaires.edit', $partenaire->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                    <form action="{{ route('partenaires.destroy', $partenaire->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce partenaire ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
