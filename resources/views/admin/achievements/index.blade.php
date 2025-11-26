@extends('admin.layout')
@section('title', 'Gestion des Succès')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="fas fa-trophy me-2"></i>Gestion des Succès
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                        <i class="fas fa-home-alt"></i> Tableau de bord
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Succès
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('admin.achievements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Ajouter un succès
                        </a>
                        <a href="{{ route('admin.achievements.statistics') }}" class="btn btn-info text-white ms-2">
                            <i class="fas fa-chart-bar me-1"></i> Statistiques
                        </a>
                        <a href="{{ route('admin.achievements.trends') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-chart-line me-1"></i> Tendances
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('success'))
            <div class="alert alert-success border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="fas fa-list-ul me-2"></i>Liste des succès
                            <span class="badge bg-primary ms-2">{{ $achievements->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="bg-primary text-white border-0">#</th>
                                        <th class="bg-primary text-white border-0">Icône</th>
                                        <th class="bg-primary text-white border-0">Nom</th>
                                        <th class="bg-primary text-white border-0">Description</th>
                                        <th class="bg-primary text-white border-0">Palier</th>
                                        <th class="bg-primary text-white border-0 text-center">Actions</th>
                                    </tr>
                                    <tr class="filters">
                                        <th></th>
                                        <th></th>
                                        <th><input type="text" class="form-control form-control-sm"
                                                placeholder="Filtrer..."></th>
                                        <th><input type="text" class="form-control form-control-sm"
                                                placeholder="Filtrer..."></th>
                                        <th><input type="text" class="form-control form-control-sm"
                                                placeholder="Filtrer..."></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($achievements as $achievement)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-dark">{{ $achievement->id }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $emoji =
                                                        $achievement->icon === 'gold'
                                                            ? '🏆'
                                                            : ($achievement->icon === 'silver'
                                                                ? '🥈'
                                                                : ($achievement->icon === 'bronze'
                                                                    ? '🥉'
                                                                    : '⭐'));
                                                    $badgeClass =
                                                        $achievement->icon === 'gold'
                                                            ? 'bg-warning text-dark'
                                                            : ($achievement->icon === 'silver'
                                                                ? 'bg-secondary'
                                                                : ($achievement->icon === 'bronze'
                                                                    ? 'bg-danger'
                                                                    : 'bg-info'));
                                                @endphp
                                                <span class="badge {{ $badgeClass }} p-2" style="font-size: 16px;">
                                                    {{ $emoji }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $achievement->name }}</div>
                                            </td>
                                            <td>
                                                <div class="text-muted">{{ Str::limit($achievement->description, 50) }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-10 text-white ">
                                                    Niveau {{ $achievement->tier }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.achievements.edit', $achievement->id) }}"
                                                        class="btn btn-success">
                                                        Modifier
                                                    </a>
                                                    <form
                                                        action="{{ route('admin.achievements.destroy', $achievement->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce succès ?')">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
            var hasData = {{ $achievements->count() > 0 ? 'true' : 'false' }};

            var table = $('table').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucun succès trouvé."
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
                        text: '<i class="fas fa-copy me-1"></i>Copier'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-outline-primary',
                        text: '<i class="fas fa-file-csv me-1"></i>CSV'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm btn-outline-success',
                        text: '<i class="fas fa-file-excel me-1"></i>Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-sm btn-outline-danger',
                        text: '<i class="fas fa-file-pdf me-1"></i>PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-outline-info',
                        text: '<i class="fas fa-print me-1"></i>Imprimer'
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

            // Style personnalisé pour les boutons DataTable
            $('.dt-buttons .btn').addClass('btn-sm');
        });
    </script>

    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .badge {
            border-radius: 4px;
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
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endsection
