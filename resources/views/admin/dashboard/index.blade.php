@extends('admin.layout')

@section('content')
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
        <div class="col">
            <div class="dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-label mb-1">Total Stagiaire</p>
                            <h3 class="stat-number">{{ $totalStagiaires }}</h3>
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
                            <p class="stat-label mb-1">Total commerciaux</p>
                            <h3 class="stat-number">{{ $totalCommerciaux }}</h3>
                        </div>
                        <div class="card-icon">
                            <i class='bx bxs-user-voice'></i>
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
                            <p class="stat-label mb-1">Total formateurs</p>
                            <h3 class="stat-number">{{ $totalFormateurs }}</h3>
                        </div>
                        <div class="card-icon">
                            <i class='bx bxs-user-check'></i>
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
                            <p class="stat-label mb-1">Total pôle relation client</p>
                            <h3 class="stat-number">{{ $totalPoleRelationClient }}</h3>
                        </div>
                        <div class="card-icon">
                            <i class='bx bxs-user-detail'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
        <div class="col">
            <div class="dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-label mb-1">Android - Utilisateurs suivis</p>
                            <h3 class="stat-number">{{ $androidUsers ?? 0 }}</h3>
                            <div class="mt-2">
                                <p class="mb-0 small text-muted"><i class='bx bx-user-plus me-1'></i>Premières utilisations:
                                    {{ $androidFirstUses ?? 0 }}</p>
                                <p class="mb-0 small text-muted"><i class='bx bx-trending-up me-1'></i>Actifs 30j:
                                    {{ $androidActive30d ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="card-icon bg-success">
                            <i class='bx bxl-android'></i>
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
                            <p class="stat-label mb-1">iOS - Utilisateurs suivis</p>
                            <h3 class="stat-number">{{ $iosUsers ?? 0 }}</h3>
                            <div class="mt-2">
                                <p class="mb-0 small text-muted"><i class='bx bx-user-plus me-1'></i>Premières utilisations:
                                    {{ $iosFirstUses ?? 0 }}</p>
                                <p class="mb-0 small text-muted"><i class='bx bx-trending-up me-1'></i>Actifs 30j:
                                    {{ $iosActive30d ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="card-icon bg-dark">
                            <i class='bx bxl-apple'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        <div class="col">
            <div class="dashboard-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="stat-label mb-1">App téléchargée</p>
                            <h3 class="stat-number">{{ $totalAppDownloads ?? 0 }}</h3>
                        </div>
                        <div class="card-icon bg-info">
                            <i class='bx bx-download'></i>
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
                            <p class="stat-label mb-1">Première connexion</p>
                            <h3 class="stat-number">{{ $totalFirstLogins ?? 0 }}</h3>
                        </div>
                        <div class="card-icon bg-warning">
                            <i class='bx bx-log-in'></i>
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
                            <p class="stat-label mb-1">Quiz joués</p>
                            <h3 class="stat-number">{{ $totalQuizzesPlayed ?? 0 }}</h3>
                        </div>
                        <div class="card-icon bg-success">
                            <i class='bx bx-play-circle'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class='bx bx-bar-chart-alt-2 me-2'></i>Statistiques de quiz par jour
                    </h5>

                    <!-- Filtres supplémentaires -->
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label for="formateurFilterDaily" class="form-label small fw-bold">Formateur :</label>
                            <select id="formateurFilterDaily" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach ($formateurs ?? [] as $formateur)
                                    <option value="{{ $formateur->id }}">{{ $formateur->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="commercialFilterDaily" class="form-label small fw-bold">Commercial :</label>
                            <select id="commercialFilterDaily" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach ($commerciaux ?? [] as $commercial)
                                    <option value="{{ $commercial->id }}">{{ $commercial->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="poleFilterDaily" class="form-label small fw-bold">Pôle relation client :</label>
                            <select id="poleFilterDaily" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach ($poles ?? [] as $pole)
                                    <option value="{{ $pole->id }}">{{ $pole->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quizSelectorDaily" class="form-label small fw-bold">Choisir un quiz :</label>
                        <select id="quizSelectorDaily" class="form-select form-select-sm">
                            <option value="" selected>-- Tous les quiz --</option>
                        </select>
                    </div>

                    <div class="chart-container" style="height: 350px;">
                        <canvas id="filteredDailyChart"></canvas>
                    </div>
                    <div class="mt-3 text-end">
                        <button id="exportDailyCSV" class="btn btn-sm btn-outline-primary">
                            <i class='bx bx-export me-1'></i>Exporter CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class='bx bx-bar-chart-alt-2 me-2'></i>Statistiques de quiz par mois
                    </h5>

                    <!-- Filtres supplémentaires -->
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label for="formateurFilterMonthly" class="form-label small fw-bold">Formateur :</label>
                            <select id="formateurFilterMonthly" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach ($formateurs ?? [] as $formateur)
                                    <option value="{{ $formateur->id }}">{{ $formateur->user->name }}
                                        {{ $formateur->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="commercialFilterMonthly" class="form-label small fw-bold">Commercial :</label>
                            <select id="commercialFilterMonthly" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach ($commerciaux ?? [] as $commercial)
                                    <option value="{{ $commercial->id }}">{{ $commercial->user->name }}
                                        {{ $commercial->prenom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="poleFilterMonthly" class="form-label small fw-bold">Pôle relation client :</label>
                            <select id="poleFilterMonthly" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach ($poles ?? [] as $pole)
                                    <option value="{{ $pole->id }}">{{ $pole->user->name }} {{ $pole->prenom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quizSelectorMonthly" class="form-label small fw-bold">Choisir un quiz :</label>
                        <select id="quizSelectorMonthly" class="form-select form-select-sm">
                            <option value="" selected>-- Tous les quiz --</option>
                        </select>
                    </div>

                    <div class="chart-container" style="height: 350px;">
                        <canvas id="filteredMonthlyChart"></canvas>
                    </div>
                    <div class="mt-3 text-end">
                        <button id="exportMonthlyCSV" class="btn btn-sm btn-outline-primary">
                            <i class='bx bx-export me-1'></i>Exporter CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class='bx bx-pie-chart-alt-2 me-2'></i>Répartition des participations par quiz
                    </h5>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="pieQuizParticipation"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class='bx bx-user-check me-2'></i>Utilisateurs connectés
                    </h5>
                    <div class="connected-users-container" style="max-height: 300px; overflow-y: auto;">
                        <ul id="connectedUsersList" class="list-group list-group-flush">
                            @forelse ($connectedUsers ?? [] as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="status-indicator bg-success rounded-circle me-2"></span>
                                        <div>
                                            <span class="fw-medium">{{ $user->name }}</span>
                                            <small class="d-block text-muted">{{ $user->role }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $platform = $user->platform ?? 'web';
                                            $icon = 'bx bx-globe';
                                            $platformClass = 'text-primary';
                                            if ($platform === 'android') {
                                                $icon = 'bx bxl-android';
                                                $platformClass = 'text-success';
                                            } elseif ($platform === 'ios') {
                                                $icon = 'bx bxl-apple';
                                                $platformClass = 'text-dark';
                                            }
                                        @endphp
                                        <i class="{{ $icon }} {{ $platformClass }} me-1"></i>
                                        <small class="text-capitalize">{{ $platform }}</small>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-3 text-muted">
                                    <i class='bx bx-user-x fs-4 mb-2'></i>
                                    <div>Aucun utilisateur connecté</div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class='bx bx-history me-2'></i>Quiz récemment joués
                    </h5>
                    <div class="recent-quizzes-container" style="max-height: 300px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @forelse($recentQuizzes ?? [] as $quiz)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <span class="fw-medium">{{ $quiz->quiz_title }}</span>
                                        <small class="d-block text-muted">par {{ $quiz->user_name }}</small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $quiz->completed_at ? \Carbon\Carbon::parse($quiz->completed_at)->format('d/m/Y H:i') : '' }}
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-3 text-muted">
                                    <i class='bx bx-play-circle fs-4 mb-2'></i>
                                    <div>Aucun quiz récemment joué</div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="dashboard-card h-100">
                <div class="card-body">
                    <h5 class="card-title d-flex align-items-center">
                        <i class='bx bx-time-five me-2'></i>Quiz en cours
                    </h5>
                    <div class="active-quizzes-container" style="max-height: 300px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @forelse($activeQuizzes ?? [] as $quiz)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <span class="fw-medium">{{ $quiz->quiz_title }}</span>
                                        <small class="d-block text-muted">par {{ $quiz->user_name }}</small>
                                    </div>
                                    <span class="badge bg-warning rounded-pill">
                                        {{ $quiz->started_at ? \Carbon\Carbon::parse($quiz->started_at)->format('d/m/Y H:i') : '' }}
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-3 text-muted">
                                    <i class='bx bx-time fs-4 mb-2'></i>
                                    <div>Aucun quiz en cours</div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
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

        .card-icon.bg-dark {
            background: linear-gradient(135deg, #212529, #495057);
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

        .status-indicator {
            width: 10px;
            height: 10px;
        }

        .chart-container {
            position: relative;
        }

        .form-select-sm {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .btn-outline-primary {
            border-radius: 8px;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge {
            font-size: 0.7rem;
            padding: 0.35em 0.65em;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Le code JavaScript reste identique à votre version originale
        const dailyStats = @json($dailyStats);
        const monthlyStats = @json($monthlyStats);

        const groupByQuiz = (data, labelKey) => {
            const grouped = {};
            data.forEach(item => {
                if (!grouped[item.titre]) grouped[item.titre] = [];
                grouped[item.titre].push({
                    label: item[labelKey],
                    total: item.total
                });
            });
            return grouped;
        };

        const mergeDataByLabel = (groupedData) => {
            const merged = {};
            for (const [quiz, entries] of Object.entries(groupedData)) {
                entries.forEach(entry => {
                    if (!merged[entry.label]) merged[entry.label] = 0;
                    merged[entry.label] += entry.total;
                });
            }
            return Object.entries(merged).map(([label, total]) => ({
                label,
                total
            }));
        };

        const dailyGrouped = groupByQuiz(dailyStats, 'date');
        const monthlyGrouped = groupByQuiz(monthlyStats, 'month');

        const dailySelect = document.getElementById('quizSelectorDaily');
        const monthlySelect = document.getElementById('quizSelectorMonthly');

        const fillSelect = (selectElement, quizTitles) => {
            quizTitles.forEach(title => {
                const opt = document.createElement('option');
                opt.value = title;
                opt.textContent = title;
                selectElement.appendChild(opt);
            });
        };

        fillSelect(dailySelect, Object.keys(dailyGrouped));
        fillSelect(monthlySelect, Object.keys(monthlyGrouped));

        let dailyChart = null;
        let monthlyChart = null;

        const createChart = (ctx, data, label) => {
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.label),
                    datasets: [{
                        label: label,
                        data: data.map(item => item.total),
                        backgroundColor: '#FEB823',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#000000'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: (context) => label,
                                label: (context) => {
                                    const dateOrMonth = context.label;
                                    const value = context.formattedValue;
                                    return `Date: ${dateOrMonth} — ${value} participations`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#000000'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre de participations',
                                color: '#000000'
                            },
                            ticks: {
                                color: '#000000'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        }
                    }
                }
            });
        };

        // Ajout du filtrage et de l'export CSV
        function filterStats(stats, filters) {
            return stats.filter(item => {
                let ok = true;
                if (filters.formateur && item.formateur_id != filters.formateur) ok = false;
                if (filters.commercial && item.commercial_id != filters.commercial) ok = false;
                if (filters.pole && item.pole_id != filters.pole) ok = false;
                return ok;
            });
        }

        function exportToCSV(data, filename) {
            if (!data.length) return;
            const csvRows = [];
            const headers = Object.keys(data[0]);
            csvRows.push(headers.join(','));
            data.forEach(row => {
                csvRows.push(headers.map(field => '"' + (row[field] ?? '') + '"').join(','));
            });
            const csvString = csvRows.join('\n');
            const blob = new Blob([csvString], {
                type: 'text/csv'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Gestion des filtres et export pour le graphique journalier
        function updateDailyChart() {
            if (dailyChart) dailyChart.destroy();
            const filters = {
                formateur: document.getElementById('formateurFilterDaily').value,
                commercial: document.getElementById('commercialFilterDaily').value,
                pole: document.getElementById('poleFilterDaily').value
            };
            let filtered = filterStats(dailyStats, filters);
            const quiz = dailySelect.value;
            if (quiz) filtered = filtered.filter(item => item.titre === quiz);
            const grouped = groupByQuiz(filtered, 'date');
            const data = quiz ? grouped[quiz] || [] : mergeDataByLabel(grouped);
            dailyChart = createChart(
                document.getElementById('filteredDailyChart'),
                data,
                quiz || 'Tous les quiz (par jour)'
            );
            // Stocker les données filtrées pour l'export
            window._lastDailyExport = data;
        }

        document.getElementById('formateurFilterDaily').addEventListener('change', updateDailyChart);
        document.getElementById('commercialFilterDaily').addEventListener('change', updateDailyChart);
        document.getElementById('poleFilterDaily').addEventListener('change', updateDailyChart);
        dailySelect.addEventListener('change', updateDailyChart);
        document.getElementById('exportDailyCSV').addEventListener('click', function() {
            exportToCSV(window._lastDailyExport || [], 'statistiques-quiz-jour.csv');
        });

        // Gestion des filtres et export pour le graphique mensuel
        function updateMonthlyChart() {
            if (monthlyChart) monthlyChart.destroy();
            const filters = {
                formateur: document.getElementById('formateurFilterMonthly').value,
                commercial: document.getElementById('commercialFilterMonthly').value,
                pole: document.getElementById('poleFilterMonthly').value
            };
            let filtered = filterStats(monthlyStats, filters);
            const quiz = monthlySelect.value;
            if (quiz) filtered = filtered.filter(item => item.titre === quiz);
            const grouped = groupByQuiz(filtered, 'month');
            const data = quiz ? grouped[quiz] || [] : mergeDataByLabel(grouped);
            monthlyChart = createChart(
                document.getElementById('filteredMonthlyChart'),
                data,
                quiz || 'Tous les quiz (par mois)'
            );
            window._lastMonthlyExport = data;
        }

        document.getElementById('formateurFilterMonthly').addEventListener('change', updateMonthlyChart);
        document.getElementById('commercialFilterMonthly').addEventListener('change', updateMonthlyChart);
        document.getElementById('poleFilterMonthly').addEventListener('change', updateMonthlyChart);
        monthlySelect.addEventListener('change', updateMonthlyChart);
        document.getElementById('exportMonthlyCSV').addEventListener('click', function() {
            exportToCSV(window._lastMonthlyExport || [], 'statistiques-quiz-mois.csv');
        });

        // Initialisation
        window.addEventListener('DOMContentLoaded', function() {
            updateDailyChart();
            updateMonthlyChart();
            // Stockage initial pour l'export
            window._lastDailyExport = mergeDataByLabel(dailyGrouped);
            window._lastMonthlyExport = mergeDataByLabel(monthlyGrouped);
        });

        // Camembert de répartition des participations par quiz
        document.addEventListener('DOMContentLoaded', function() {
            const pieData = {};
            (window.dailyStats || []).forEach(item => {
                if (!pieData[item.titre]) pieData[item.titre] = 0;
                pieData[item.titre] += item.total;
            });
            const pieLabels = Object.keys(pieData);
            const pieValues = Object.values(pieData);
            if (document.getElementById('pieQuizParticipation')) {
                new Chart(document.getElementById('pieQuizParticipation'), {
                    type: 'pie',
                    data: {
                        labels: pieLabels,
                        datasets: [{
                            data: pieValues,
                            backgroundColor: [
                                '#0d6efd', '#17a00e', '#f41127', '#ffc107', '#212529',
                                '#FEB823', '#6c757d', '#20c997', '#6610f2', '#fd7e14'
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            title: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
