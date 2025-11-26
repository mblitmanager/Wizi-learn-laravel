@extends('admin.layout')
@section('title', 'Gestion des parrains')
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
                                        <i class="bx bx-home-alt"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('stagiaires.index') }}" class="text-decoration-none">
                                        Stagiaires
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Parrainage
                                </li>
                            </ol>
                        </nav>
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

        <!-- Tableau des parrains -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="bx bx-group me-2"></i>Liste des parrains
                    <span class="badge bg-info text-dark ms-2">{{ $parrains->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="parrainsTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Parrain</th>
                                <th class="bg-primary text-white border-0">Email</th>
                                <th class="bg-primary text-white border-0 text-center">Nombre de filleuls</th>
                                <th class="bg-primary text-white border-0 text-center">Actions</th>
                            </tr>
                            <tr class="filters">
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
                            @foreach ($parrains as $parrain)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="bx bx-user text-white fs-6"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0 fw-semibold text-dark">{{ $parrain->name }}</h6>
                                                <small class="text-muted">Parrain depuis
                                                    {{ $parrain->created_at ? $parrain->created_at->format('d/m/Y') : '—' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $parrain->email }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill fs-6">
                                            {{ $parrain->parrainages_count }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('parrainage.show', $parrain->id) }}"
                                                class="btn btn-info text-white d-flex align-items-center">
                                                Voir les filleuls
                                            </a>
                                            @if ($parrain->parrainages_count > 0)
                                                <a href="#" class="btn btn-success d-flex align-items-center"
                                                    data-bs-toggle="tooltip" data-bs-title="Exporter la liste">
                                                    Exporter
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistiques des parrainages -->
        @if ($parrains->count() > 0)
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bx bx-group fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-0">{{ $parrains->count() }}</h4>
                                    <p class="mb-0 opacity-75">Parrains actifs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bx bx-user-plus fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-0">{{ $parrains->sum('parrainages_count') }}</h4>
                                    <p class="mb-0 opacity-75">Total des filleuls</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bx bx-calculator fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-0">
                                        {{ $parrains->count() > 0 ? round($parrains->sum('parrainages_count') / $parrains->count(), 1) : 0 }}
                                    </h4>
                                    <p class="mb-0 opacity-75">Moyenne par parrain</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var hasData = {{ $parrains->count() > 0 ? 'true' : 'false' }};

            var table = $('#parrainsTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucun parrain trouvé."
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
                },
                columnDefs: [{
                        orderable: false,
                        targets: 3
                    } // Désactiver le tri sur la colonne Actions
                ]
            });

            // Activer les tooltips Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
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

        .bg-primary .card-body {
            border-radius: 12px;
        }
    </style>
@endsection
