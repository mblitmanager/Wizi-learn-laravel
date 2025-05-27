@extends('admin.layout')

@section('content')
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total Stagiaire</p>
                            <h4 class="my-1 text-info">{{ $totalStagiaires }}</h4>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class="bx bxs-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total commerciaux</p>
                            <h4 class="my-1 text-danger">{{ $totalCommerciaux }}</h4>

                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class="bx bxs-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total formateurs</p>
                            <h4 class="my-1 text-success">{{ $totalFormateurs }}</h4>

                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class="bx bxs-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Total pôle relation client</p>
                            <h4 class="my-1 text-warning">{{ $totalPoleRelationClient }}</h4>

                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class="bx bxs-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .dot-sm {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .pulse {
            animation: pulse-animation 1.5s infinite;
        }

        @keyframes pulse-animation {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }

            100% {
                opacity: 1;
            }
        }

        .extra-small {
            font-size: 0.65rem;
        }
    </style>

    <style>
        .thin-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .thin-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .thin-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
    </style>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">App téléchargée</p>
                            <h4 class="my-1 text-danger">{{ $totalAppDownloads ?? 0 }}</h4>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class="bx bx-download"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Première connexion</p>
                            <h4 class="my-1 text-warning">{{ $totalFirstLogins ?? 0 }}</h4>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class="bx bx-log-in"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">Quiz joués</p>
                            <h4 class="my-1 text-success">{{ $totalQuizzesPlayed ?? 0 }}</h4>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i
                                class="bx bx-play-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Utilisateurs en ligne -->
        <div class="col-12 col-md-6 col-xl-3 mb-3">
            <div class="card shadow-sm border-light">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center mb-2">
                        <span class="dot bg-success me-2 pulse"></span>
                        <h6 class="mb-0 small fw-bold text-muted">En ligne ({{ count($onlineUsers) }})</h6>
                    </div>
                    <ul class="list-unstyled mb-0">
                        @forelse ($onlineUsers as $user)
                            <li class="d-flex align-items-center py-1">
                                <span class="dot-sm bg-success me-2"></span>
                                <span class="small text-truncate flex-grow-1">{{ $user->name }}</span>
                                <span class="text-muted extra-small ms-2">{{ $user->role }}</span>
                            </li>
                        @empty
                            <li class="small text-muted fst-italic py-1">Aucun utilisateur</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Récemment connectés -->
        <div class="col-12 col-md-6 col-xl-3 mb-3">
            <div class="card shadow-sm border-light">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center mb-2">
                        <span class="dot bg-secondary me-2"></span>
                        <h6 class="mb-0 small fw-bold text-muted">Récemment ({{ count($recentlyOnlineUsers) }})</h6>
                    </div>
                    <ul class="list-unstyled mb-0">
                        @forelse ($recentlyOnlineUsers as $user)
                            <li class="d-flex align-items-center py-1">
                                <span class="dot-sm bg-secondary me-2"></span>
                                <span class="small text-truncate flex-grow-1">{{ $user->name }}</span>
                                <span class="text-muted extra-small ms-2">{{ $user->role }}</span>
                            </li>
                        @empty
                            <li class="small text-muted fst-italic py-1">Aucune activité</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-12 col-md-6 col-xl-3 mb-3">
            <div class="card shadow-sm border-light">
                <div class="card-body p-2">
                    <div class="d-flex align-items-center mb-2">
                        <span class="dot bg-primary me-2"></span>
                        <h6 class="mb-0 small fw-bold text-muted">Statistiques</h6>
                    </div>
                    <div class="row g-1 text-center">
                        <div class="col-4">
                            <div class="small fw-bold text-primary">{{ $loginStats['today'] ?? 0 }}</div>
                            <div class="extra-small text-muted">Aujourd'hui</div>
                        </div>
                        <div class="col-4">
                            <div class="small fw-bold text-primary">{{ $loginStats['week'] ?? 0 }}</div>
                            <div class="extra-small text-muted">Semaine</div>
                        </div>
                        <div class="col-4">
                            <div class="small fw-bold text-primary">{{ $loginStats['month'] ?? 0 }}</div>
                            <div class="extra-small text-muted">Mois</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Statistiques de quiz par jour</h5>

                    <!-- Filtres supplémentaires -->
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label for="formateurFilterDaily" class="form-label">Formateur :</label>
                            <select id="formateurFilterDaily" class="form-select">
                                <option value="">Tous</option>
                                @foreach ($formateurs ?? [] as $formateur)
                                    <option value="{{ $formateur->id }}">{{ $formateur->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="commercialFilterDaily" class="form-label">Commercial :</label>
                            <select id="commercialFilterDaily" class="form-select">
                                <option value="">Tous</option>
                                @foreach ($commerciaux ?? [] as $commercial)
                                    <option value="{{ $commercial->id }}">{{ $commercial->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="poleFilterDaily" class="form-label">Pôle relation client :</label>
                            <select id="poleFilterDaily" class="form-select">
                                <option value="">Tous</option>
                                @foreach ($poles ?? [] as $pole)
                                    <option value="{{ $pole->id }}">{{ $pole->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quizSelectorDaily" class="form-label">Choisir un quiz :</label>
                        <select id="quizSelectorDaily" class="form-select">
                            <option value="" selected>-- Tous les quiz --</option>
                        </select>
                    </div>

                    <div style="height: 450px;">
                        <canvas id="filteredDailyChart"></canvas>
                    </div>
                    <div class="mt-3 text-end">
                        <button id="exportDailyCSV" class="btn btn-outline-primary btn-sm">Exporter CSV</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Statistiques de quiz par mois</h5>

                    <!-- Filtres supplémentaires -->
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2">
                            <label for="formateurFilterMonthly" class="form-label">Formateur :</label>
                            <select id="formateurFilterMonthly" class="form-select">
                                <option value="">Tous</option>
                                @foreach ($formateurs ?? [] as $formateur)
                                    <option value="{{ $formateur->id }}">{{ $formateur->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="commercialFilterMonthly" class="form-label">Commercial :</label>
                            <select id="commercialFilterMonthly" class="form-select">
                                <option value="">Tous</option>
                                @foreach ($commerciaux ?? [] as $commercial)
                                    <option value="{{ $commercial->id }}">{{ $commercial->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="poleFilterMonthly" class="form-label">Pôle relation client :</label>
                            <select id="poleFilterMonthly" class="form-select">
                                <option value="">Tous</option>
                                @foreach ($poles ?? [] as $pole)
                                    <option value="{{ $pole->id }}">{{ $pole->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quizSelectorMonthly" class="form-label">Choisir un quiz :</label>
                        <select id="quizSelectorMonthly" class="form-select shadow-sm border border-primary">
                            <option value="" selected>-- Tous les quiz --</option>
                        </select>
                    </div>

                    <div style="height: 450px;">
                        <canvas id="filteredMonthlyChart"></canvas>
                    </div>
                    <div class="mt-3 text-end">
                        <button id="exportMonthlyCSV" class="btn btn-outline-primary btn-sm">Exporter CSV</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Répartition des participations par quiz</h5>
                    <div style="height: 350px;">
                        <canvas id="pieQuizParticipation"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs connectés</h5>
                    <ul id="connectedUsersList" class="list-group">
                        @foreach ($connectedUsers ?? [] as $user)
                            <li class="list-group-item d-flex align-items-center">
                                <span class="badge bg-success rounded-circle me-2" style="width:10px;height:10px;"></span>
                                {{ $user->name }} <span class="text-muted ms-2">({{ $user->role }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Quiz récemment joués</h5>
                    <ul class="list-group">
                        @forelse($recentQuizzes ?? [] as $quiz)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>{{ $quiz->quiz_title }}</strong> par {{ $quiz->user_name }}
                                </span>
                                <span
                                    class="badge bg-primary">{{ $quiz->completed_at ? \Carbon\Carbon::parse($quiz->completed_at)->format('d/m/Y H:i') : '' }}</span>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun quiz récemment joué.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Quiz en cours</h5>
                    <ul class="list-group">
                        @forelse($activeQuizzes ?? [] as $quiz)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong>{{ $quiz->quiz_title }}</strong> par {{ $quiz->user_name }}
                                </span>
                                <span
                                    class="badge bg-warning">{{ $quiz->started_at ? \Carbon\Carbon::parse($quiz->started_at)->format('d/m/Y H:i') : '' }}</span>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun quiz en cours.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
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
                                color: '#000000' // légende en noir
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
            initCharts();
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
