@extends('admin.layout')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/6.6.6/css/flag-icons.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<style>
    .progress {
        background-color: #e9ecef;
        border-radius: 4px;
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
    }

    .fi {
        font-size: 1.5em;
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
    }

    .stat-card {
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tableau de bord des connexions</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Statistiques des connexions</li>
    </ol>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">En ligne maintenant</h5>
                            <p class="card-text display-6">{{ $stats['online_users'] }}</p>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Connexions aujourd'hui</h5>
                            <p class="card-text display-6">{{ $stats['today_logins'] }}</p>
                        </div>
                        <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Cette semaine</h5>
                            <p class="card-text display-6">{{ $stats['week_logins'] }}</p>
                        </div>
                        <i class="fas fa-calendar-week fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-dark mb-4 stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Ce mois</h5>
                            <p class="card-text display-6">{{ $stats['month_logins'] }}</p>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des connexions par pays -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-globe-europe me-1"></i>
                    Connexions par pays
                </div>
                <span class="badge bg-primary">
                    <i class="fas fa-sign-in-alt"></i> Total: {{ $stats['total_logins'] }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="loginStatsTable">
                    <thead class="table-dark">
                        <tr>
                            <th width="50px">#</th>
                            <th>Pays</th>
                            <th width="70px">Drapeau</th>
                            <th>Connexions</th>
                            <th>Pourcentage</th>
                            <th>Dernière activité</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['map_data'] as $index => $country)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $country['country'] }}</strong>
                            </td>
                            <td class="text-center">
                                @if($country['code'])
                                <span class="fi fi-{{ strtolower($country['code']) }}"></span>
                                @else
                                <i class="fas fa-globe"></i>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ ($country['value'] / $stats['total_logins']) * 100 }}%"
                                        aria-valuenow="{{ $country['value'] }}"
                                        aria-valuemin="0"
                                        aria-valuemax="{{ $stats['total_logins'] }}">
                                        {{ $country['value'] }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ number_format(($country['value'] / $stats['total_logins']) * 100, 2) }}%
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($country['last_activity'])->diffForHumans() }}
                                <small class="text-muted d-block">
                                    {{ \Carbon\Carbon::parse($country['last_activity'])->format('d/m/Y H:i') }}
                                </small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Utilisateurs en ligne -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-users me-1"></i>
                    Utilisateurs actuellement en ligne
                </div>
                <span class="badge bg-success">
                    <i class="fas fa-circle"></i> {{ $stats['online_users'] }} en ligne
                </span>
            </div>
        </div>
        <div class="card-body">
            @if($onlineUsers->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th>Nom</th>
                            <th>Rôle</th>
                            <th>Dernière activité</th>
                            <th>Depuis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($onlineUsers as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>
                                <span class="badge bg-{{ [
                                    'administrateur' => 'danger',
                                    'formateur' => 'info',
                                    'stagiaire' => 'success',
                                    'commercial' => 'warning'
                                ][$user->role] ?? 'secondary' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td> {{ \Carbon\Carbon::parse($user->last_activity)->diffForHumans() }}
                            </td>
                            <td> {{ \Carbon\Carbon::parse($user->last_activity_at)->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> Aucun utilisateur en ligne actuellement
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal pour afficher les utilisateurs par pays -->
<div class="modal fade" id="usersModal" tabindex="-1" aria-labelledby="usersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="usersModalLabel">Utilisateurs de <span id="modalCountryName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-sm" id="usersTable">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Dernière connexion</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les données seront chargées via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div id="loadingSpinner" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialisation de DataTable
        $('#loginStatsTable').DataTable({
            order: [
                [3, 'desc']
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            dom: '<"top"lf>rt<"bottom"ip>',
            pageLength: 25
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
                url: '{{ route("dashboard.activity-user") }}',
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
                                new Date(user.last_login_at).toLocaleString('fr-FR') :
                                'Jamais';

                            $('#usersTable tbody').append(
                                '<tr>' +
                                '<td>' + user.name + '</td>' +
                                '<td>' + user.email + '</td>' +
                                '<td>' + lastLogin + '</td>' +
                                '<td>' + statusBadge + '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        $('#usersTable tbody').append(
                            '<tr><td colspan="4" class="text-center">Aucun utilisateur trouvé pour ce pays</td></tr>'
                        );
                    }
                },
                error: function() {
                    $('#loadingSpinner').hide();
                    $('#usersTable tbody').append(
                        '<tr><td colspan="4" class="text-center text-danger">Erreur lors du chargement des données</td></tr>'
                    );
                }
            });
        });

        // Réinitialiser le modal quand il est fermé
        $('#usersModal').on('hidden.bs.modal', function() {
            $('#usersTable tbody').empty();
        });
    });
</script>
@endsection