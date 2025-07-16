@extends('admin.layout')
@section('title', 'Statistiques des Succès')
@section('content')
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Statistiques des Succès</h4>
            <a href="{{ route('admin.achievements.detailed-stats') }}" class="btn btn-info btn-sm">
                Détails par stagiaire
            </a>
        </div>
        <div class="card-body">
            <ul class="list-group mb-4">
                <li class="list-group-item">Total Succès : <strong>{{ $totalAchievements }}</strong></li>
                <li class="list-group-item">Total Succès débloqués : <strong>{{ $totalUnlocked }}</strong></li>
            </ul>
            <h5>Succès par succès</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Débloqué par</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($byAchievement as $achievement)
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
