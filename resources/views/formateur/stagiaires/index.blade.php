@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Mes Stagiaires</h3>
                </div>
                <div class="card-body">
                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box px-2 py-2"  style="background: #75c988; color: white;">
                                <span class="info-box-icon"><i class="fas fa-user-graduate"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">En cours de formation</span>
                                    <span class="info-box-number">{{ $stats['stagiairesEnCours'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box px-2 py-2" style="background: #65baee; color: white;">
                                <span class="info-box-icon"><i class="fas fa-graduation-cap"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Formation terminée (1 an)</span>
                                    <span class="info-box-number">{{ $stats['stagiairesTermines'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box px-2 py-2" style="background: #f39c12; color: white;">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total stagiaires</span>
                                    <span class="info-box-number">{{ $tousStagiaires->total() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('formateur.stagiaires.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-list"></i> Tous les stagiaires
                                </a>
                                <a href="{{ route('formateur.stagiaires.en-cours') }}" class="btn btn-outline-success">
                                    <i class="fas fa-user-graduate"></i> En cours
                                </a>
                                <a href="{{ route('formateur.stagiaires.termines') }}" class="btn btn-outline-info">
                                    <i class="fas fa-graduation-cap"></i> Terminés récents
                                </a>
                                 <a href="{{ route('formateur.classement') }}" class="btn btn-outline-warning">
                    <i class="fas fa-trophy"></i> Classement
                    </a>
                        <a href="{{ route('formateur.stagiaires.application') }}" class="btn btn-outline-success">
    <i class="fas fa-mobile-alt"></i> Utilisateurs App
</a>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des stagiaires -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Date début</th>
                                    <th>Date fin</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tousStagiaires as $stagiaire)
                                <tr>
                                    <td>{{ $stagiaire->prenom }} {{ $stagiaire->nom ?? '' }}</td>
                                    <td>{{ $stagiaire->user->email ?? 'N/A' }}</td>
                                    
                                    <td>{{ $stagiaire->date_debut_formation ? \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y') : 'N/A' }}</td>
                                    <td>{{ $stagiaire->date_fin_formation ? \Carbon\Carbon::parse($stagiaire->date_fin_formation)->format('d/m/Y H:i') : 'En cours' }}</td>
                                    <td>
                                        @if($stagiaire->statut === 1 && (!$stagiaire->date_fin_formation || $stagiaire->date_fin_formation > now()))
                                            <span class="badge bg-success">En cours</span>
                                        @else
                                            <span class="badge bg-info">Terminé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('formateur.stagiaires.show', $stagiaire->id) }}" class="btn btn-sm btn-primary" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Aucun stagiaire trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $tousStagiaires->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection