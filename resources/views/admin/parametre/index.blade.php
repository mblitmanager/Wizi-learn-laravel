@extends('admin.layout')
@section('title', 'Gestion des Paramétrages')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Tableau de bord
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Paramétrages
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('parametre.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Nouvel utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('success'))
            <div class="alert alert-success border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tableau des utilisateurs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="bx bx-list-ul me-2"></i>Liste des utilisateurs
                    <span class="badge bg-info text-dark ms-2">{{ $users->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Nom</th>
                                <th class="bg-primary text-white border-0">Email</th>
                                <th class="bg-primary text-white border-0">Rôle</th>
                                <th class="bg-primary text-white border-0">Statut</th>
                                <th class="bg-primary text-white border-0 text-center">Actions</th>
                            </tr>
                            <tr class="filters">
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filtrer...">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filtrer...">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filtrer...">
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filtrer...">
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <span class="text-white fw-semibold">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <span class="fw-semibold text-dark">{{ $user->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $user->email }}" class="text-decoration-none text-dark">
                                            {{ $user->email }}
                                        </a>
                                    </td>
                                    <td>
                                        @php
                                            $roleBadge = 'secondary';
                                            $roleText = 'Utilisateur';

                                            if ($user->hasRole('admin')) {
                                                $roleBadge = 'danger';
                                                $roleText = 'Administrateur';
                                            } elseif ($user->hasRole('formateur')) {
                                                $roleBadge = 'warning';
                                                $roleText = 'Formateur';
                                            } elseif ($user->hasRole('stagiaire')) {
                                                $roleBadge = 'info';
                                                $roleText = 'Stagiaire';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $roleBadge }}">{{ $roleText }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="bx bx-check-circle me-1"></i>Actif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('parametre.show', $user->id) }}"
                                                class="btn btn-info text-white" title="Afficher les détails">
                                                Afficher
                                            </a>
                                            <a href="{{ route('parametre.edit', $user->id) }}" class="btn btn-success"
                                                title="Modifier l'utilisateur">
                                                Modifier
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section Actions administratives -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-shield me-2"></i>Actions administratives
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.parametre.reset-data') }}" class="btn btn-outline-warning text-start">
                                <i class="bx bx-reset me-2"></i>Réinitialiser les données
                            </a>
                            <a href="#" class="btn btn-outline-info text-start">
                                <i class="bx bx-backup me-2"></i>Sauvegarde des données
                            </a>
                            <a href="#" class="btn btn-outline-secondary text-start">
                                <i class="bx bx-stats me-2"></i>Statistiques système
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-user-plus me-2"></i>Gestion rapide
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('parametre.create') }}" class="btn btn-outline-primary text-start">
                                <i class="bx bx-plus me-2"></i>Ajouter un utilisateur
                            </a>
                            <a href="#" class="btn btn-outline-success text-start">
                                <i class="bx bx-import me-2"></i>Importer des utilisateurs
                            </a>
                            <a href="#" class="btn btn-outline-danger text-start">
                                <i class="bx bx-export me-2"></i>Exporter la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var hasData = {{ $users->count() > 0 ? 'true' : 'false' }};

            var table = $('#usersTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucun utilisateur trouvé."
                },
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
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
                    if (hasData) {
                        this.api().columns().every(function() {
                            var that = this;
                            $('input', $('.filters').eq(0).children().eq(this.index()))
                                .on('keyup change clear', function() {
                                    if (that.search() !== this.value) {
                                        that.search(this.value).draw();
                                    }
                                });
                        });
                    }
                }
            });
        });
    </script>

    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.04) !important;
        }

        .filters input {
            font-size: 0.8rem;
        }

        .dataTables_empty {
            text-align: center;
            padding: 2rem !important;
            color: #6c757d !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .btn-group-sm>.btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
        }
    </style>
@endsection
