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
                    <h5 class="card-title">Statistiques de quiz par jour</h5>

                    <div class="mb-3">
                        <label for="quizSelectorDaily" class="form-label">Choisir un quiz :</label>
                        <select id="quizSelectorDaily" class="form-select">
                            <option value="" selected>-- Tous les quiz --</option>
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
                    <h5 class="card-title">Statistiques de quiz par mois</h5>

                    <div class="mb-3">
                        <label for="quizSelectorMonthly" class="form-label">Choisir un quiz :</label>
                        <select id="quizSelectorMonthly" class="form-select shadow-sm border border-primary">
                            <option value="" selected>-- Tous les quiz --</option>
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

        const mergeDataByLabel = (groupedData) => {
            const merged = {};
            for (const [quiz, entries] of Object.entries(groupedData)) {
                entries.forEach(entry => {
                    if (!merged[entry.label]) merged[entry.label] = 0;
                    merged[entry.label] += entry.total;
                });
            }
            return Object.entries(merged).map(([label, total]) => ({ label, total }));
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


        const initCharts = () => {
            const dailyDataMerged = mergeDataByLabel(dailyGrouped);
            const monthlyDataMerged = mergeDataByLabel(monthlyGrouped);

            dailyChart = createChart(
                document.getElementById('filteredDailyChart'),
                dailyDataMerged,
                'Tous les quiz (par jour)'
            );

            monthlyChart = createChart(
                document.getElementById('filteredMonthlyChart'),
                monthlyDataMerged,
                'Tous les quiz (par mois)',
                '#FEB823'
            );
        };

        dailySelect.addEventListener('change', () => {
            if (dailyChart) dailyChart.destroy();
            const title = dailySelect.value;
            const data = title ? dailyGrouped[title] : mergeDataByLabel(dailyGrouped);
            console.log(data)
            dailyChart = createChart(
                document.getElementById('filteredDailyChart'),
                data,
                title || 'Tous les quiz (par jour)'
            );
        });

        monthlySelect.addEventListener('change', () => {
            if (monthlyChart) monthlyChart.destroy();
            const title = monthlySelect.value;
            const data = title ? monthlyGrouped[title] : mergeDataByLabel(monthlyGrouped);
            monthlyChart = createChart(
                document.getElementById('filteredMonthlyChart'),
                data,
                title || 'Tous les quiz (par mois)',
                '#FEB823'
            );
        });

        window.addEventListener('DOMContentLoaded', initCharts);
    </script>
@endsection
