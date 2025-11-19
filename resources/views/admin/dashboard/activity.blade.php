@extends('admin.layout')
@section('title', 'Tableau de bord des connexions')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Statistiques des connexions
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="bx bx-stats me-1"></i> Données en temps réel
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('success'))
            <div class="alert alert-success border-0 alert-dismissible fade show shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Cartes de statistiques -->
        <div class="row mb-4 g-4" id="kpiRow">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">En ligne maintenant</h6>
                                <p class="card-text display-6 fw-bold text-primary" id="kpiOnlineNow">
                                    {{ $stats['online_users'] }}</p>
                                <small class="text-success fw-medium">
                                    <i class="bx bx-up-arrow-alt"></i> Utilisateurs actifs
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">Connexions aujourd'hui</h6>
                                <p class="card-text display-6 fw-bold text-success" id="kpiToday">
                                    {{ $stats['today_logins'] }}</p>
                                <small class="text-info fw-medium">
                                    <i class="bx bx-calendar"></i> Sessions du jour
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">Cette semaine</h6>
                                <p class="card-text display-6 fw-bold text-info" id="kpiWeek">{{ $stats['week_logins'] }}
                                </p>
                                <small class="text-warning fw-medium">
                                    <i class="bx bx-trending-up"></i> Activité hebdomadaire
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">Ce mois</h6>
                                <p class="card-text display-6 fw-bold text-warning" id="kpiMonth">
                                    {{ $stats['month_logins'] }}</p>
                                <small class="text-danger fw-medium">
                                    <i class="bx bx-chart"></i> Vue mensuelle
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des connexions par pays -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-globe me-2"></i>Connexions par pays
                        </h6>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="bx bx-sign-in me-1"></i> Total: {{ $stats['total_logins'] }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="loginStatsTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">#</th>
                                <th class="bg-primary text-white border-0">Pays</th>
                                <th class="bg-primary text-white border-0 text-center">Drapeau</th>
                                <th class="bg-primary text-white border-0">Connexions</th>
                                <th class="bg-primary text-white border-0">Pourcentage</th>
                                <th class="bg-primary text-white border-0">Dernière activité</th>
                            </tr>
                            <tr class="filters">
                                <th class="border-bottom"></th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom"></th>
                                <th class="border-bottom"></th>
                                <th class="border-bottom"></th>
                                <th class="border-bottom"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stats['map_data'] as $index => $country)
                                <tr>
                                    <td class="fw-semibold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <strong class="text-dark">{{ $country['country'] }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if ($country['code'])
                                            <span class="fi fi-{{ strtolower($country['code']) }}"></span>
                                        @else
                                            <i class="bx bx-globe text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-3" style="height: 25px;">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: {{ ($country['value'] / $stats['total_logins']) * 100 }}%"
                                                    aria-valuenow="{{ $country['value'] }}" aria-valuemin="0"
                                                    aria-valuemax="{{ $stats['total_logins'] }}">
                                                    <span class="fw-semibold">{{ $country['value'] }}</span>
                                                </div>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $country['value'] }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        {{ number_format(($country['value'] / $stats['total_logins']) * 100, 2) }}%
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-medium">
                                                {{ \Carbon\Carbon::parse($country['last_activity'])->diffForHumans() }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="bx bx-time me-1"></i>
                                                {{ \Carbon\Carbon::parse($country['last_activity'])->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Utilisateurs en ligne -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-user-voice me-2"></i>Utilisateurs actuellement en ligne
                        </h6>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-success px-3 py-2">
                            <i class="bx bx-circle me-1"></i> {{ $stats['online_users'] }} en ligne
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if ($onlineUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="bg-primary text-white border-0">Nom</th>
                                    <th class="bg-primary text-white border-0">Rôle</th>
                                    <th class="bg-primary text-white border-0">Dernière activité</th>
                                    <th class="bg-primary text-white border-0">Connecté depuis</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($onlineUsers as $user)
                                    <tr>
                                        <td class="fw-semibold text-dark">{{ $user->name }}</td>
                                        <td>
                                            @php
                                                $roleColors = [
                                                    'administrateur' => 'bg-danger',
                                                    'formateur' => 'bg-info',
                                                    'stagiaire' => 'bg-success',
                                                    'commercial' => 'bg-warning',
                                                ];
                                            @endphp
                                            <span class="badge {{ $roleColors[$user->role] ?? 'bg-secondary' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-medium">
                                                {{ \Carbon\Carbon::parse($user->last_activity)->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                <i class="bx bx-time me-1"></i>
                                                {{ \Carbon\Carbon::parse($user->last_activity_at)->diffForHumans() }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bx bx-user-x fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun utilisateur en ligne</h5>
                        <p class="text-muted mb-0">Aucune activité utilisateur détectée actuellement</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal pour afficher les utilisateurs par pays -->
    <div class="modal fade" id="usersModal" tabindex="-1" aria-labelledby="usersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-semibold" id="usersModalLabel">
                        <i class="bx bx-group me-2"></i>Utilisateurs de <span id="modalCountryName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="bg-primary text-white border-0">Nom</th>
                                    <th class="bg-primary text-white border-0">Email</th>
                                    <th class="bg-primary text-white border-0">Dernière connexion</th>
                                    <th class="bg-primary text-white border-0 text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Les données seront chargées via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="loadingSpinner" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="text-muted mt-3">Chargement des utilisateurs...</p>
                    </div>
                </div>
                <div class="modal-footer border-0 py-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/6.6.6/css/flag-icons.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
    <style>
        .card {
            border-radius: 12px;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .badge {
            border-radius: 4px;
            font-weight: 500;
        }

        .modal-content {
            border-radius: 12px;
        }

        .progress {
            border-radius: 6px;
        }

        .form-control {
            border-radius: 6px;
        }

        .alert {
            border-radius: 8px;
        }

        .fi {
            font-size: 1.5em;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            border-radius: 4px;
        }

        .progress-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialisation de DataTable
            var table = $('#loginStatsTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json"
                },
                paging: true,
                searching: true,
                ordering: true,
                lengthMenu: [10, 25, 50, 100],
                pageLength: 25,
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"l><"col-md-6"p>>',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-sm btn-outline-secondary',
                        text: '<i class="bx bx-copy me-1"></i>Copier'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-outline-primary',
                        text: '<i class="bx bx-file me-1"></i>CSV'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm btn-outline-success',
                        text: '<i class="bx bx-spreadsheet me-1"></i>Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-sm btn-outline-danger',
                        text: '<i class="bx bx-file me-1"></i>PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-outline-info',
                        text: '<i class="bx bx-printer me-1"></i>Imprimer'
                    }
                ],
                initComplete: function() {
                    this.api().columns().every(function() {
                        var that = this;
                        $('input', this.header()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                }
            });

            // Gestion du modal pour afficher les utilisateurs par pays
            $('#usersModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var country = button.data('country');

                $('#modalCountryName').text(country);
                $('#usersTable tbody').empty();
                $('#loadingSpinner').show();

                // Requête AJAX pour charger les utilisateurs
                $.ajax({
                    url: '{{ route('dashboard.activity-user') }}',
                    method: 'GET',
                    data: {
                        country: country
                    },
                    success: function(response) {
                        $('#loadingSpinner').hide();

                        if (response.users.length > 0) {
                            $.each(response.users, function(index, user) {
                                var statusBadge = user.is_online ?
                                    '<span class="badge bg-success">En ligne</span>' :
                                    '<span class="badge bg-secondary">Hors ligne</span>';

                                var lastLogin = user.last_login_at ?
                                    new Date(user.last_login_at).toLocaleString(
                                        'fr-FR') :
                                    'Jamais';

                                $('#usersTable tbody').append(
                                    '<tr>' +
                                    '<td class="fw-semibold text-dark">' + user
                                    .name + '</td>' +
                                    '<td>' + user.email + '</td>' +
                                    '<td>' + lastLogin + '</td>' +
                                    '<td class="text-center">' + statusBadge +
                                    '</td>' +
                                    '</tr>'
                                );
                            });
                        } else {
                            $('#usersTable tbody').append(
                                '<tr><td colspan="4" class="text-center py-4">' +
                                '<i class="bx bx-user-x text-muted fs-2 mb-2 d-block"></i>' +
                                '<span class="text-muted">Aucun utilisateur trouvé pour ce pays</span>' +
                                '</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        $('#loadingSpinner').hide();
                        $('#usersTable tbody').append(
                            '<tr><td colspan="4" class="text-center py-4 text-danger">' +
                            '<i class="bx bx-error-circle fs-2 mb-2 d-block"></i>' +
                            '<span>Erreur lors du chargement des données</span>' +
                            '</td></tr>'
                        );
                    }
                });
            });

            // Réinitialiser le modal quand il est fermé
            $('#usersModal').on('hidden.bs.modal', function() {
                $('#usersTable tbody').empty();
            });

            // Rafraîchissement temps réel des KPIs et du tableau pays
            function refreshActivity() {
                $.get('{{ route('dashboard.activity.data') }}', function(resp) {
                    if (!resp || !resp.stats) return;
                    const stats = resp.stats;

                    // KPIs avec animation
                    animateCounter('#kpiOnlineNow', stats.online_users);
                    animateCounter('#kpiToday', stats.today_logins);
                    animateCounter('#kpiWeek', stats.week_logins);
                    animateCounter('#kpiMonth', stats.month_logins);

                    // Tableau pays
                    const total = stats.total_logins || 1;
                    const tbody = $('#loginStatsTable tbody');
                    tbody.empty();
                    (stats.map_data || []).forEach((country, idx) => {
                        const percent = ((country.value / total) * 100).toFixed(2);
                        const code = (country.code || '').toLowerCase();
                        const last = country.last_activity;
                        const row = `
                        <tr>
                          <td class="fw-semibold text-muted">${idx + 1}</td>
                          <td><strong class="text-dark">${country.country}</strong></td>
                          <td class="text-center">${code ? `<span class="fi fi-${code}"></span>` : '<i class="bx bx-globe text-muted"></i>'}</td>
                          <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-3" style="height: 25px;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: ${percent}%" aria-valuenow="${country.value}" aria-valuemin="0" aria-valuemax="${total}">
                                        <span class="fw-semibold">${country.value}</span>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark">${country.value}</span>
                            </div>
                          </td>
                          <td class="fw-bold text-primary">${percent}%</td>
                          <td>
                            <div class="d-flex flex-column">
                                <span class="text-dark fw-medium">${moment(last).fromNow()}</span>
                                <small class="text-muted">
                                    <i class="bx bx-time me-1"></i>
                                    <span>${moment(last).format('DD/MM/YYYY HH:mm')}</span>
                                </small>
                            </div>
                          </td>
                        </tr>`;
                        tbody.append(row);
                    });
                });
            }

            // Fonction d'animation des compteurs
            function animateCounter(selector, newValue) {
                const $element = $(selector);
                const current = parseInt($element.text()) || 0;
                const diff = newValue - current;

                if (diff !== 0) {
                    $element.prop('counter', current).animate({
                        counter: newValue
                    }, {
                        duration: 800,
                        easing: 'swing',
                        step: function(now) {
                            $(this).text(Math.ceil(now));
                        }
                    });
                }
            }

            // Charger moment.js pour formats (CDN léger)
            if (typeof moment === 'undefined') {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js';
                s.onload = function() {
                    moment.locale('fr');
                    refreshActivity();
                    setInterval(refreshActivity, 30000); // 30s
                };
                document.body.appendChild(s);
            } else {
                moment.locale('fr');
                refreshActivity();
                setInterval(refreshActivity, 30000);
            }
        });
    </script>
@endsection
