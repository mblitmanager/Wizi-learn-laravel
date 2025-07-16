@extends('admin.layout')
@section('title', 'Statistiques détaillées des Succès')
@section('content')
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header">
            <h4>Succès débloqués par stagiaire</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Stagiaire</th>
                        <th>Succès débloqués</th>
                        <th>Date de déblocage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stagiaires as $stagiaire)
                        @foreach($stagiaire->achievements as $achievement)
                        <tr>
                            <td>{{ $stagiaire->prenom }} {{ $stagiaire->user->nom }}</td>
                            <td>{{ $achievement->name }}</td>
                            <td>{{ $achievement->pivot->created_at }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-header">
            <h4>Succès par achievement</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Succès</th>
                        <th>Nombre de stagiaires</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($achievements as $achievement)
                        <tr>
                            <td>{{ $achievement->name }}</td>
                            <td>{{ $achievement->users_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
