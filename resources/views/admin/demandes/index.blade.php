@extends('admin.layout')
@section('content')
    <div class="container-fluid py-4">

        <!-- Alertes -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h4 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-history me-2"></i>Historique des Demandes d'Inscription
                </h4>

                <!-- Bouton d'exportation CSV -->
                <a href="{{ route('demande.historique.export.csv', request()->query()) }}" class="btn btn-success"
                    title="Exporter en CSV">
                    <i class="fas fa-file-csv me-2"></i>Exporter CSV
                </a>
            </div>

            <div class="card-body">
                <!-- Filtres -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body bg-light">
                        <form action="{{ route('demande.historique.index') }}" method="GET"
                            class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="type" class="form-label fw-semibold">Type de demande</label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="">Tous les types</option>
                                    <option value="Soumission d'une demande d'inscription par parrainage" 
                                        {{ request('type') === "Soumission d'une demande d'inscription par parrainage" ? 'selected' : '' }}>
                                        Parrainage
                                    </option>
                                    <option required value="Demande d'inscription à une formation" 
                                        {{ request('type') === "Demande d'inscription à une formation" ? 'selected' : '' }}>
                                        Formation
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="statut" class="form-label fw-semibold">Statut</label>
                                <select name="statut" id="statut" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>
                                        En attente
                                    </option>
                                    <option value="complete" {{ request('statut') === 'complete' ? 'selected' : '' }}>
                                        Complète
                                    </option>
                                    <option value="annulee" {{ request('statut') === 'annulee' ? 'selected' : '' }}>
                                        Annulée
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i> Appliquer les filtres
                                </button>
                                <a href="{{ route('demande.historique.index') }}"
                                    class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="fas fa-sync-alt me-2"></i> Réinitialiser
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistiques -->
                @if ($demandes->count() > 0)
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $demandes->total() }}</h5>
                                    <p class="card-text mb-0">Total des demandes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $demandes->where('statut', 'en_attente')->count() }}</h5>
                                    <p class="card-text mb-0">En attente</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $demandes->where('statut', 'complete')->count() }}</h5>
                                    <p class="card-text mb-0">Complètes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $demandes->where('statut', 'annulee')->count() }}</h5>
                                    <p class="card-text mb-0">Annulées</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tableau -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="50">ID</th>
                                        <th>Type</th>
                                        <th>Formation</th>
                                        <th>Parrain</th>
                                        <th>Filleul</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($demandes as $demande)
                                        <tr class="align-middle">
                                            <td class="fw-bold">#{{ $demande->id }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $demande->motif === 'Soumission d\'une demande d\'inscription par parrainage' ? 'bg-success' : 'bg-primary' }}">
                                                    {{ $demande->motif === 'Soumission d\'une demande d\'inscription par parrainage' ? 'Parrainage' : 'Formation' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="d-inline-block text-truncate" style="max-width: 200px;"
                                                    title="{{ $demande->formation->titre ?? 'N/A' }}">
                                                    {{ $demande->formation->titre ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $demande->parrain ? $demande->parrain->name : 'N/A' }}
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $demande->filleul->name ?? 'N/A' }}</strong>
                                                    @if ($demande->filleul && $demande->filleul->email)
                                                        <br>
                                                        <small class="text-muted">{{ $demande->filleul->email }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <small
                                                    class="text-muted d-block">{{ $demande->created_at->format('d/m/Y') }}</small>
                                                <small class="text-muted">{{ $demande->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                                    {{ $demande->statut === 'complete'
                                                        ? 'bg-success'
                                                        : ($demande->statut === 'en_attente'
                                                            ? 'bg-warning text-dark'
                                                            : ($demande->statut === 'annulee'
                                                                ? 'bg-danger'
                                                                : 'bg-secondary')) }}">
                                                    {{ $demande->statut === 'en_attente'
                                                        ? 'En attente'
                                                        : ($demande->statut === 'complete'
                                                            ? 'Complète'
                                                            : ($demande->statut === 'annulee'
                                                                ? 'Annulée'
                                                                : ucfirst($demande->statut))) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('demande.historique.show', $demande->id) }}"
                                                        class="btn btn-info" title="Voir détails" data-bs-toggle="tooltip">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <h5>Aucune demande trouvée</h5>
                                                    <p class="mb-0">Aucune demande ne correspond à vos critères de
                                                        recherche.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($demandes->hasPages())
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted">
                                        Affichage de {{ $demandes->firstItem() }} à {{ $demandes->lastItem() }}
                                        sur {{ $demandes->total() }} demandes
                                    </div>
                                    <nav>
                                        {{ $demandes->appends(request()->query())->links() }}
                                    </nav>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialisation des tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .table> :not(caption)>*>* {
            padding: 0.75rem 0.5rem;
        }

        .badge {
            font-size: 0.75em;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        .text-truncate {
            max-width: 200px;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .card-text {
            font-size: 0.875rem;
        }
    </style>
@endsection
