    @extends('admin.layout')
    @section('content')
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
        <div>
            <!-- Carte du monde des connexions -->
            <div class="col-12">
                <div class="card shadow-sm border-light">
                    <div class="card-body">
                        <h6 class="mb-3 small fw-bold text-muted">Carte des connexions par pays</h6>
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        {{-- @vite(['resources/js/loginMap.js']) --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-geo@3.5.2/build/index.umd.min.js"></script>

        <script>
            const url = 'https://unpkg.com/world-atlas@2.0.2/countries-50m.json';
            fetch(url)
                .then(response => response.json())
                .then((datapoint) => {
                    const countries = ChartGeo.topojson.feature(datapoint, datapoint.objects.countries).features;
                    // setup 
                    const data = {
                        labels: countries.map((d) => d.properties.name),
                        datasets: [{
                            label: 'Weekly Sales',
                            data: countries.map((d) => Math.round(Math.random() * 100)),

                        }]
                    };

                    // config 
                    const config = {
                        type: 'choropleth',
                        data: data,
                        options: {

                        }
                    };

                    // render init block
                    const myChart = new Chart(
                        document.getElementById('myChart'),
                        config
                    );
                })


            // Instantly assign Chart.js version
            const chartVersion = document.getElementById('chartVersion');
            chartVersion.innerText = Chart.version;
        </script> --}}
    @endsection
