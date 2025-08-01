@extends('admin.layout')
@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold text-primary">Historique des Demandes d'Inscription</h4>
            </div>
            <div class="card-body">
                <!-- Filtres -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('demande.historique.index') }}" method="GET" class="form-inline">
                            <div class="form-group mr-3 mb-2">
                                <label for="type" class="mr-2">Type de demande</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="">Tous les types</option>
                                    <option value="demande_inscription_parrainage"
                                        {{ request('type') === 'demande_inscription_parrainage' ? 'selected' : '' }}>
                                        Parrainage
                                    </option>
                                    <option value="demande_inscription_formation"
                                        {{ request('type') === 'demande_inscription_formation' ? 'selected' : '' }}>
                                        Formation
                                    </option>
                                </select>
                            </div>

                            <div class="form-group mr-3 mb-2">
                                <label for="status" class="mr-2">Statut</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="en_attente" {{ request('status') === 'en_attente' ? 'selected' : '' }}>
                                        En attente
                                    </option>
                                    <option value="complete" {{ request('status') === 'complete' ? 'selected' : '' }}>
                                        Complète
                                    </option>
                                    <option value="annulee" {{ request('status') === 'annulee' ? 'selected' : '' }}>
                                        Annulée
                                    </option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary mb-2">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>

                            <a href="{{ route('demande.historique.index') }}" class="btn btn-secondary mb-2 ml-2">
                                <i class="fas fa-sync-alt"></i> Réinitialiser
                            </a>
                        </form>
                    </div>
                </div>

                <!-- Tableau des demandes -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Type</th>
                                <th>Formation</th>
                                <th>Parrain</th>
                                <th>Filleul</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($demandes as $demande)
                                <tr>

                                    <td>
                                        <span
                                            class="badge {{ $demande->motif === 'Soumission d\'une demande d\'inscription par parrainage' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $demande->motif === 'Soumission d\'une demande d\'inscription par parrainage'
                                                ? 'Soumission d\'une demande d\'inscription par parrainage'
                                                : 'Demande d\'inscription à une formation' }}
                                        </span>
                                    </td>
                                    <td>{{ $demande->formation->titre ?? 'N/A' }}</td>
                                    <td>{{ $demande->parrain ? $demande->parrain->name : 'N/A' }}</td>
                                    <td>{{ $demande->filleul->name ?? 'N/A' }}</td>
                                    <td>{{ $demande->date_demande->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                    {{ $demande->statut === 'complete'
                                        ? 'badge-success'
                                        : ($demande->statut === 'en_attente'
                                            ? 'bg-danger'
                                            : 'bg-warning') }}">
                                            {{ ucfirst($demande->statut) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('demande.historique.show', $demande->id) }}"
                                            class="btn btn-sm btn-info" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">Aucune demande trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $demandes->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom badge color */
        .badge-purple {
            background-color: #6f42c1;
            color: white;
        }
    </style>
@endsection
