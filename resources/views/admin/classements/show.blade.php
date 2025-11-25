@extends('admin.layout')
@section('title', 'Classement des stagiaires')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="fas fa-trophy me-2"></i>Classement des stagiaires
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                        <i class="fas fa-home-alt"></i> Tableau de bord
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $partenaire->identifiant }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('classement.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Retour aux partenaires
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations du partenaire -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            @if ($partenaire->logo)
                                <div class="me-3">
                                    <img src="{{ asset('storage/' . $partenaire->logo) }}"
                                        alt="Logo {{ $partenaire->identifiant }}" class="rounded-circle" width="60"
                                        height="60">
                                </div>
                            @endif
                            <div>
                                <h4 class="mb-1 text-dark fw-semibold">{{ $partenaire->identifiant }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $partenaire->ville }}
                                    ({{ $partenaire->departement }})
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            <i class="fas fa-users me-1"></i>{{ $partenaire->stagiaires->count() }} stagiaires
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="fas fa-filter me-2"></i>Filtres
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="periode" class="form-label fw-semibold text-dark">Période</label>
                        <select name="periode" id="periode" class="form-select">
                            <option value="global" {{ $periode === 'global' ? 'selected' : '' }}>Global</option>
                            <option value="jour" {{ $periode === 'jour' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="semaine" {{ $periode === 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="mois" {{ $periode === 'mois' ? 'selected' : '' }}>Ce mois</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="formation_id" class="form-label fw-semibold text-dark">Domaine</label>
                        <select name="formation_id" id="formation_id" class="form-select">
                            <option value="">Tous les domaines</option>
                            @foreach ($formations as $formation)
                                <option value="{{ $formation->id }}"
                                    {{ $formationId == $formation->id ? 'selected' : '' }}>
                                    {{ $formation->titre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sync-alt me-2"></i> Actualiser
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Stagiaires</p>
                                <h4 class="mb-0 text-dark fw-semibold">{{ $partenaire->stagiaires->count() }}</h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Points totaux</p>
                                <h4 class="mb-0 text-dark fw-semibold">
                                    {{ number_format($classements->sum('total_points')) }}</h4>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fa fa-star fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Moyenne/stagiaire</p>
                                <h4 class="mb-0 text-dark fw-semibold">
                                    @if ($partenaire->stagiaires->count() > 0)
                                        {{ number_format($classements->sum('total_points') / $partenaire->stagiaires->count(), 1) }}
                                    @else
                                        0
                                    @endif
                                </h4>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-chart-line  fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Meilleur score</p>
                                <h4 class="mb-0 text-dark fw-semibold">
                                    @if (count($classements) > 0)
                                        {{ number_format($classements->first()['total_points']) }}
                                    @else
                                        0
                                    @endif
                                </h4>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-trophy fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Tableau et Graphique -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-dark fw-semibold">
                                <i class="fas fa-list-alt me-2"></i>Classement
                                @if ($periode !== 'global')
                                    <small class="text-muted">({{ ucfirst($periode) }})</small>
                                @endif
                            </h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ count($classements) }} stagiaires
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80" class="ps-4">Rang</th>
                                        <th>Stagiaire</th>
                                        <th width="120" class="text-center">Points</th>
                                        <th width="120" class="text-center">Formations</th>
                                        <th width="100" class="text-center pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($classements as $classement)
                                        <tr>
                                            <td class="ps-4">
                                                @if ($classement['rang'] == 1)
                                                    <span class="badge bg-warning bg-opacity-15 text-warning p-2 w-100">
                                                        <i class="fas fa-medal me-1"></i> 1er
                                                    </span>
                                                @elseif($classement['rang'] == 2)
                                                    <span
                                                        class="badge bg-secondary bg-opacity-15 text-secondary p-2 w-100">
                                                        <i class="fas fa-medal me-1"></i> 2ème
                                                    </span>
                                                @elseif($classement['rang'] == 3)
                                                    <span class="badge bg-danger bg-opacity-15 text-danger p-2 w-100">
                                                        <i class="fas fa-medal me-1"></i> 3ème
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-dark p-2 w-100">
                                                        {{ $classement['rang'] }}ème
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-3">
                                                        @if ($classement['stagiaire']->user->profile_photo_path)
                                                            <img src="{{ asset('storage/' . $classement['stagiaire']->user->profile_photo_path) }}"
                                                                class="rounded-circle" width="40"
                                                                alt="{{ $classement['stagiaire']->prenom }}">
                                                        @else
                                                            <div class="avatar-initials bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                                                style="width: 40px; height: 40px;">
                                                                {{ substr($classement['stagiaire']->prenom, 0, 1) }}{{ substr($classement['stagiaire']->nom, 0, 1) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-dark fw-semibold">
                                                            {{ $classement['stagiaire']->prenom }}
                                                            {{ $classement['stagiaire']->nom }}</h6>
                                                        <small
                                                            class="text-muted">{{ $classement['stagiaire']->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                                    <i
                                                        class="fas fa-star me-1"></i>{{ number_format($classement['total_points']) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">
                                                    <i
                                                        class="fas fa-book me-1"></i>{{ $classement['stagiaire']->catalogue_formations_count ?? $classement['stagiaire']->catalogue_formations->count() }}
                                                </span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <button class="btn btn-sm btn-outline-primary px-3" data-bs-toggle="modal"
                                                    data-bs-target="#detailsModal{{ $classement['stagiaire']->id }}">
                                                    <i class="fas fa-eye me-1"></i> Détails
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="alert alert-warning border-0 mb-0 py-2">
                                                    <i class="fas fa-exclamation-circle me-2"></i>
                                                    Aucun classement disponible
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphique -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="fas fa-chart-bar me-2"></i>
                            Top 10 des stagiaires
                        </h6>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px;">
                            <canvas id="classementChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals pour les détails des stagiaires -->
    @foreach ($classements as $classement)
        <div class="modal fade" id="detailsModal{{ $classement['stagiaire']->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-chart-pie me-2"></i>
                            Détails pour {{ $classement['stagiaire']->prenom }} {{ $classement['stagiaire']->nom }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Cartes de résumé -->
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary border-2 h-100">
                                    <div class="card-body text-center py-3">
                                        <p class="text-muted mb-1 small">Rang</p>
                                        <h3 class="text-primary mb-0">{{ $classement['rang'] }}ème</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-success border-2 h-100">
                                    <div class="card-body text-center py-3">
                                        <p class="text-muted mb-1 small">Points totaux</p>
                                        <h3 class="text-success mb-0">{{ number_format($classement['total_points']) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-info border-2 h-100">
                                    <div class="card-body text-center py-3">
                                        <p class="text-muted mb-1 small">Formations</p>
                                        <h3 class="text-info mb-0">
                                            {{ $classement['stagiaire']->catalogue_formations_count ?? $classement['stagiaire']->catalogue_formations->count() }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Détails des performances -->
                        <h6 class="mb-3 text-dark fw-semibold"><i class="fas fa-list-alt me-2"></i>Détail des performances
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Quiz</th>
                                        <th>Formation</th>
                                        <th class="text-end">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($classement['classements']) > 0)
                                        @foreach ($classement['classements'] as $detail)
                                            <tr>
                                                <td>{{ $detail->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $detail->quiz->titre }}</td>
                                                <td>{{ $detail->quiz->formation->titre }}</td>
                                                <td class="text-end">{{ number_format($detail->points) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted">
                                                <i class="fas fa-info-circle me-2"></i>Aucun détail de performance
                                                disponible
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                @if (count($classement['classements']) > 0)
                                    <tfoot class="table-light fw-semibold">
                                        <tr>
                                            <td colspan="3" class="text-end">Total:</td>
                                            <td class="text-end">{{ number_format($classement['total_points']) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    @if (count($classements) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('classementChart').getContext('2d');
                const labels = @json($classements->take(10)->map(fn($item) => $item['stagiaire']->prenom . ' ' . substr($item['stagiaire']->nom, 0, 1) + '.'));
                const data = @json($classements->take(10)->pluck('total_points'));

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Points',
                            data: data,
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            borderColor: 'rgba(13, 110, 253, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.parsed.y.toLocaleString() + ' points';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            });
        </script>
    @endif
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .avatar-initials {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endpush
