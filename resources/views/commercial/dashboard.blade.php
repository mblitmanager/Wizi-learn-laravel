@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class='bx bx-trending-up me-2'></i>Tableau de Bord Commercial
                </h1>
                <small class="text-muted">{{ $commercial->user->name }}</small>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
            <div class="col">
                <div class="dashboard-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="stat-label mb-1">Total Stagiaires</p>
                                <h3 class="stat-number">{{ $stats['total_stagiaires'] }}</h3>
                            </div>
                            <div class="card-icon">
                                <i class='bx bxs-graduation'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="dashboard-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="stat-label mb-1">Total Participations</p>
                                <h3 class="stat-number">{{ $stats['total_participations'] }}</h3>
                            </div>
                            <div class="card-icon bg-success">
                                <i class='bx bx-play-circle'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="dashboard-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="stat-label mb-1">Score Moyen</p>
                                <h3 class="stat-number">{{ round($stats['avg_score'], 2) }}</h3>
                            </div>
                            <div class="card-icon bg-info">
                                <i class='bx bx-bar-chart-alt-2'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="dashboard-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <p class="stat-label mb-1">Formations</p>
                                <h3 class="stat-number">{{ count($statsByFormation) }}</h3>
                            </div>
                            <div class="card-icon bg-warning">
                                <i class='bx bx-book'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Sections -->
        <div class="row g-4 mb-4">
            <!-- Stats par Formation -->
            <div class="col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class='bx bx-book me-2'></i>Statistiques par Formation
                        </h5>
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Formation</th>
                                        <th class="text-center">Stagiaires</th>
                                        <th class="text-center">Part.</th>
                                        <th class="text-center">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($statsByFormation as $formation)
                                        <tr>
                                            <td>
                                                <small class="fw-medium">{{ $formation['name'] }}</small>
                                                <br>
                                                <small class="text-muted">{{ $formation['catalogue'] }}</small>
                                            </td>
                                            <td class="text-center"><badge class="badge bg-light text-dark">{{ $formation['stagiaires_count'] }}</badge></td>
                                            <td class="text-center">{{ $formation['total_participations'] }}</td>
                                            <td class="text-center">
                                                <small class="fw-medium">{{ $formation['avg_score'] }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                Aucune formation
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('commercial.stats.formation') }}" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-detail me-1'></i>Voir Détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Stagiaires -->
            <div class="col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class='bx bx-medal me-2'></i>Top 10 Stagiaires
                        </h5>
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Rang</th>
                                        <th>Stagiaire</th>
                                        <th class="text-center">Score</th>
                                        <th class="text-center">Moyenne</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($classement as $item)
                                        <tr>
                                            <td>
                                                @if($item['rank'] == 1)
                                                    <i class='bx bxs-medal fs-5' style="color: #FFD700;"></i>
                                                @elseif($item['rank'] == 2)
                                                    <i class='bx bxs-medal fs-5' style="color: #C0C0C0;"></i>
                                                @elseif($item['rank'] == 3)
                                                    <i class='bx bxs-medal fs-5' style="color: #CD7F32;"></i>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $item['rank'] }}</span>
                                                @endif
                                            </td>
                                            <td><small class="fw-medium">{{ $item['name'] }}</small></td>
                                            <td class="text-center">{{ $item['total_score'] }}</td>
                                            <td class="text-center">{{ $item['avg_score'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                Aucun stagiaire
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('commercial.stats.classement') }}" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-detail me-1'></i>Voir Classement Complet
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Affluence et Quizzes Récents -->
        <div class="row g-4">
            <div class="col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class='bx bx-bar-chart-alt-2 me-2'></i>Affluence (Derniers 30 jours)
                        </h5>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Période</th>
                                        <th class="text-center">Partici.</th>
                                        <th class="text-center">Utilisateurs</th>
                                        <th class="text-center">Score Moy.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($affluenceStats as $stat)
                                        <tr>
                                            <td><small>{{ $stat->period }}</small></td>
                                            <td class="text-center">{{ $stat->total_participations }}</td>
                                            <td class="text-center">{{ $stat->unique_users }}</td>
                                            <td class="text-center">{{ round($stat->avg_score, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                Aucune donnée
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('commercial.stats.affluence') }}" class="btn btn-sm btn-outline-primary">
                                <i class='bx bx-detail me-1'></i>Voir Affluence Détaillée
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class='bx bx-time me-2'></i>Quiz Récents et Actifs
                        </h5>
                        <ul class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                            @forelse($recentQuizzes as $quiz)
                                <li class="list-group-item py-2 d-flex justify-content-between">
                                    <div>
                                        <small class="fw-medium">{{ $quiz->titre }}</small>
                                        <br>
                                        <small class="text-muted">{{ $quiz->user_name }}</small>
                                    </div>
                                    <span class="badge bg-success">{{ $quiz->score }}</span>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-3">
                                    Aucun quiz
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --success: #4cc9f0;
            --info: #4895ef;
            --warning: #f72585;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gradient-start: #4361ee;
            --gradient-end: #3a0ca3;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --card-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .dashboard-card {
            border-radius: 16px;
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
            background: white;
            position: relative;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            color: white;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }

        .card-icon.bg-success {
            background: linear-gradient(135deg, #4cc9f0, #4895ef);
        }

        .card-icon.bg-info {
            background: linear-gradient(135deg, #4895ef, #4361ee);
        }

        .card-icon.bg-warning {
            background: linear-gradient(135deg, #f72585, #b5179e);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0;
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #718096;
            font-weight: 500;
        }

        .card-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .card-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
            border-radius: 3px;
        }
    </style>
@endsection
