{{-- resources/views/formateur/dashboard.blade.php --}}
@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">Tableau de Bord Formateur</h4>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="mb-0">{{ $stats['total_stagiaires'] }}</h4>
                                <p class="mb-0">Total Stagiaires</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="lni lni-users fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="mb-0">{{ $stats['stagiaires_en_cours'] }}</h4>
                                <p class="mb-0">En Formation</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="lni lni-graduation fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="mb-0">{{ $stats['stagiaires_termines'] }}</h4>
                                <p class="mb-0">Formation Terminée</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="lni lni-certificate fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="mb-0">{{ $stats['formations_encadrees'] }}</h4>
                                <p class="mb-0">Formations</p>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="lni lni-library fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derniers stagiaires -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Derniers Stagiaires</h5>
                        <a href="{{ route('formateur.stagiaires.index') }}" class="btn btn-primary btn-sm">Voir tous</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Formation</th>
                                        <th>Date début</th>
                                        <th>Statut</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentStagiaires as $stagiaire)
                                        <tr>
                                            <td>{{ $stagiaire->prenom }}</td>
                                            <td>
                                                @foreach($stagiaire->catalogue_formations as $formation)
                                                    <span class="badge bg-primary">{{ $formation->titre }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ $stagiaire->date_debut_formation ? \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y H:i') : '' }}
                                            </td>

                                            <td>
                                                @if($stagiaire->statut === 'actif')
                                                    <span class="badge bg-success">En cours</span>
                                                @else
                                                    <span class="badge bg-info">Terminé</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('formateur.stagiaires.show', $stagiaire->id) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection