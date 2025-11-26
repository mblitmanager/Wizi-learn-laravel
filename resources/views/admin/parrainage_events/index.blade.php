@extends('admin.layout')
@section('title', 'Gestion des événements de parrainage')
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
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Événements de parrainage
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('parrainage_events.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Créer un événement
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

        <!-- Filtres et statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bx bx-calendar-event fs-1"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $events->total() }}</h4>
                                <p class="mb-0 opacity-75">Événements total</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bx bx-play-circle fs-1"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">
                                    {{ $events->where('date_debut', '<=', now())->where('date_fin', '>=', now())->count() }}
                                </h4>
                                <p class="mb-0 opacity-75">En cours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bx bx-time fs-1"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $events->where('date_debut', '>', now())->count() }}</h4>
                                <p class="mb-0 opacity-75">À venir</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="bx bx-check-circle fs-1"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $events->where('date_fin', '<', now())->count() }}</h4>
                                <p class="mb-0 opacity-75">Terminés</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des événements -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="bx bx-list-ul me-2"></i>Liste des événements
                    <span class="badge bg-info text-dark ms-2">{{ $events->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="eventsTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Événement</th>
                                <th class="bg-primary text-white border-0 text-center">Prix</th>
                                <th class="bg-primary text-white border-0 text-center">Date de début</th>
                                <th class="bg-primary text-white border-0 text-center">Date de fin</th>
                                <th class="bg-primary text-white border-0 text-center">Statut</th>
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
                                <th>
                                    <select class="form-control form-control-sm">
                                        <option value="">Tous</option>
                                        <option value="actif">En cours</option>
                                        <option value="a_venir">À venir</option>
                                        <option value="termine">Terminé</option>
                                    </select>
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                                @php
                                    $now = now();
                                    $dateDebut = \Carbon\Carbon::parse($event->date_debut);
                                    $dateFin = \Carbon\Carbon::parse($event->date_fin);

                                    if ($dateDebut->gt($now)) {
                                        $status = 'a_venir';
                                        $statusClass = 'bg-warning';
                                        $statusText = 'À venir';
                                    } elseif ($dateFin->lt($now)) {
                                        $status = 'termine';
                                        $statusClass = 'bg-secondary';
                                        $statusText = 'Terminé';
                                    } else {
                                        $status = 'actif';
                                        $statusClass = 'bg-success';
                                        $statusText = 'En cours';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="{{ $statusClass }} rounded-circle d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="bx bx-calendar text-white fs-6"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0 fw-semibold text-dark">{{ $event->titre }}</h6>
                                                <small class="text-muted">Créé le
                                                    {{ $event->created_at->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold text-primary fs-6">{{ $event->prix }} €</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold text-dark">{{ $dateDebut->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold text-dark">{{ $dateFin->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $statusClass }} text-white">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('parrainage_events.show', $event->id) }}"
                                                class="btn btn-info text-white d-flex align-items-center"
                                                data-bs-toggle="tooltip" data-bs-title="Voir les détails">
                                                Afficher
                                            </a>
                                            <a href="{{ route('parrainage_events.edit', $event->id) }}"
                                                class="btn btn-success d-flex align-items-center" data-bs-toggle="tooltip"
                                                data-bs-title="Modifier l'événement">
                                                Modifier
                                            </a>
                                            <form action="{{ route('parrainage_events.destroy', $event->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger d-flex align-items-center"
                                                    data-bs-toggle="tooltip" data-bs-title="Supprimer l'événement"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
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

                <!-- Pagination -->
                @if ($events->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var hasData = {{ $events->count() > 0 ? 'true' : 'false' }};

            var table = $('#eventsTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucun événement trouvé."
                },
                paging: false, // Désactive la pagination DataTable car nous utilisons Laravel pagination
                searching: true,
                ordering: true,
                info: false,
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt',
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

                            // Filtres pour les colonnes de texte
                            if (this.index() < 4) {
                                $('input', $('.filters').eq(0).children().eq(this.index()))
                                    .on('keyup change clear', function() {
                                        if (that.search() !== this.value) {
                                            that.search(this.value).draw();
                                        }
                                    });
                            }

                            // Filtre pour la colonne statut
                            if (this.index() === 4) {
                                $('select', $('.filters').eq(0).children().eq(this.index()))
                                    .on('change', function() {
                                        var val = $.fn.dataTable.util.escapeRegex(this
                                            .value);
                                        that.search(val ? '^' + val + '$' : '', true, false)
                                            .draw();
                                    });
                            }
                        });
                    }
                },
                columnDefs: [{
                        orderable: false,
                        targets: 5
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
    </style>
@endsection
