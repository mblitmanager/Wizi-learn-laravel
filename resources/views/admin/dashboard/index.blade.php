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
    <div class="row mt-5">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-primary">Statistiques de quiz par jour</h5>

                    <div class="mb-3">
                        <label for="quizSelectorDaily" class="form-label">Choisir un quiz :</label>
                        <select id="quizSelectorDaily" class="form-select">
                            <option disabled selected>-- Sélectionner un quiz --</option>
                        </select>
                    </div>

                    <div style="height: 450px;">
                        <canvas id="filteredDailyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-success">Statistiques de quiz par mois</h5>

                    <div class="mb-3">
                        <label for="quizSelectorMonthly" class="form-label">Choisir un quiz :</label>
                        <select id="quizSelectorMonthly" class="form-select shadow-sm border border-primary">
                            <option disabled selected>-- Sélectionner un quiz --</option>
                        </select>
                    </div>

                    <div style="height: 450px;">
                        <canvas id="filteredMonthlyChart"></canvas>
                    </div>
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

        const dailyGrouped = groupByQuiz(dailyStats, 'date');
        const monthlyGrouped = groupByQuiz(monthlyStats, 'month');

        const dailySelect = document.getElementById('quizSelectorDaily');
        const monthlySelect = document.getElementById('quizSelectorMonthly');

        const fillSelect = (selectElement, quizTitles) => {
            quizTitles.forEach((title, index) => {
                const opt = document.createElement('option');
                opt.value = title;
                opt.textContent = title;
                if (index === 0) opt.selected = true; // Par défaut : premier quiz sélectionné
                selectElement.appendChild(opt);
            });
        };

        fillSelect(dailySelect, Object.keys(dailyGrouped));
        fillSelect(monthlySelect, Object.keys(monthlyGrouped));

        let dailyChart = null;
        let monthlyChart = null;

        const createChart = (ctx, data, label, color = '#3b82f6') => {
            return new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.label),
                    datasets: [{
                        label: label,
                        data: data.map(item => item.total),
                        borderColor: color,
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        pointRadius: 5,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nombre de participations'
                            }
                        }
                    }
                }
            });
        };

        const initCharts = () => {
            const defaultDailyTitle = dailySelect.value;
            const defaultMonthlyTitle = monthlySelect.value;

            dailyChart = createChart(
                document.getElementById('filteredDailyChart'),
                dailyGrouped[defaultDailyTitle],
                defaultDailyTitle
            );

            monthlyChart = createChart(
                document.getElementById('filteredMonthlyChart'),
                monthlyGrouped[defaultMonthlyTitle],
                defaultMonthlyTitle,
                '#10b981'
            );
        };

        dailySelect.addEventListener('change', () => {
            if (dailyChart) dailyChart.destroy();
            const title = dailySelect.value;
            dailyChart = createChart(
                document.getElementById('filteredDailyChart'),
                dailyGrouped[title],
                title
            );
        });

        monthlySelect.addEventListener('change', () => {
            if (monthlyChart) monthlyChart.destroy();
            const title = monthlySelect.value;
            monthlyChart = createChart(
                document.getElementById('filteredMonthlyChart'),
                monthlyGrouped[title],
                title,
                '#10b981'
            );
        });

        // Affiche les charts dès le chargement
        window.addEventListener('DOMContentLoaded', initCharts);
    </script>
@endsection
