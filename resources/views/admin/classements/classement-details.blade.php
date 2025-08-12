@extends('admin.layout')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    @if ($partenaire->logo)
                        <div class="me-3">
                            <img src="{{ asset('storage/' . $partenaire->logo) }}" alt="Logo {{ $partenaire->identifiant }}"
                                class="rounded-circle" width="60">
                        </div>
                    @endif
                    <div>
                        <h2 class="mb-1">Classement des stagiaires</h2>
                        <h4 class="text-primary mb-0">{{ $partenaire->identifiant }}</h4>
                        <small class="text-muted">{{ $partenaire->ville }} ({{ $partenaire->departement }})</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('classement.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-2"></i> Retour aux partenaires
                </a>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card card-body border-0 shadow-sm mb-4">
            <h5 class="mb-3"><i class="fas fa-filter text-muted me-2"></i>Filtres</h5>
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="periode" class="form-label small text-uppercase fw-bold text-muted">Période</label>
                    <select name="periode" id="periode" class="form-select">
                        <option value="global" {{ $periode === 'global' ? 'selected' : '' }}>Global</option>
                        <option value="jour" {{ $periode === 'jour' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="semaine" {{ $periode === 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="mois" {{ $periode === 'mois' ? 'selected' : '' }}>Ce mois</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="formation_id" class="form-label small text-uppercase fw-bold text-muted">Domaine</label>
                    <select name="formation_id" id="formation_id" class="form-select">
                        <option value="">Tous les domaines</option>
                        @foreach ($formations as $formation)
                            <option value="{{ $formation->id }}" {{ $formationId == $formation->id ? 'selected' : '' }}>
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

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Stagiaires</p>
                                <h4 class="mb-0">{{ $partenaire->stagiaires->count() }}</h4>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Points totaux</p>
                                <h4 class="mb-0">{{ number_format($classements->sum('total_points')) }}</h4>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-star text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-info border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Moyenne/stagiaire</p>
                                <h4 class="mb-0">
                                    @if ($partenaire->stagiaires->count() > 0)
                                        {{ number_format($classements->sum('total_points') / $partenaire->stagiaires->count(), 1) }}
                                    @else
                                        0
                                    @endif
                                </h4>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-chart-line text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Meilleur score</p>
                                <h4 class="mb-0">
                                    @if (count($classements) > 0)
                                        {{ number_format($classements->first()['total_points']) }}
                                    @else
                                        0
                                    @endif
                                </h4>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-trophy text-warning"></i>
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
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy text-warning me-2"></i>
                            Classement
                            @if ($periode !== 'global')
                                <small class="text-muted">({{ ucfirst($periode) }})</small>
                            @endif
                        </h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ count($classements) }} stagiaires
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
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
                                                    <span class="badge bg-warning bg-opacity-15 text-warning p-2 w-100">🥇
                                                        1er</span>
                                                @elseif($classement['rang'] == 2)
                                                    <span
                                                        class="badge bg-secondary bg-opacity-15 text-secondary p-2 w-100">🥈
                                                        2ème</span>
                                                @elseif($classement['rang'] == 3)
                                                    <span class="badge bg-danger bg-opacity-15 text-danger p-2 w-100">🥉
                                                        3ème</span>
                                                @else
                                                    <span
                                                        class="badge bg-light text-dark p-2 w-100">{{ $classement['rang'] }}ème</span>
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
                                                        <h6 class="mb-0">{{ $classement['stagiaire']->prenom }}
                                                            {{ $classement['stagiaire']->nom }}</h6>
                                                        <small
                                                            class="text-muted">{{ $classement['stagiaire']->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                                    {{ number_format($classement['total_points']) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1">
                                                    {{ $classement['stagiaire']->catalogue_formations_count ?? $classement['stagiaire']->catalogue_formations->count() }}
                                                </span>
                                            </td>
                                            <td class="text-center pe-4">
                                                <button class="btn btn-sm btn-outline-primary px-3" data-bs-toggle="modal"
                                                    data-bs-target="#detailsModal{{ $classement['stagiaire']->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="alert alert-warning mb-0 py-2">
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
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar text-info me-2"></i>
                            Top 10 des stagiaires
                        </h5>
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

    <!-- Modals -->
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

                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Quiz</th>
                                        <th>Formation</th>
                                        <th class="text-end">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classement['classements'] as $detail)
                                        <tr>
                                            <td>{{ $detail->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $detail->quiz->titre }}</td>
                                            <td>{{ $detail->quiz->formation->titre }}</td>
                                            <td class="text-end">{{ number_format($detail->points) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
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
                const labels = @json($classements->take(10)->map(fn($item) => $item['stagiaire']->prenom . ' ' . substr($item['stagiaire']->nom, 0, 1) . '.'));
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
        .avatar-initials {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.125rem rgba(0, 0, 0, 0.05);
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
