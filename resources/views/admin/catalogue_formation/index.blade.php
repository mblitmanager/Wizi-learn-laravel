@extends('admin.layout')
@section('title', 'Gestion du catalogue de formations')
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
                                    Catalogue de formations
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('catalogue_formation.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Nouvelle formation catalogue
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

        <!-- Tableau des formations catalogue -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="bx bx-list-ul me-2"></i>Liste des formations catalogue
                    <span class="badge bg-info text-dark ms-2">{{ $catalogueFormations->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="catalogueFormationsTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Titre</th>
                                <th class="bg-primary text-white border-0 text-center">Durée</th>
                                <th class="bg-primary text-white border-0 text-center">Tarif</th>
                                <th class="bg-primary text-white border-0 text-center">Statut</th>

                                <th class="bg-primary text-white border-0">Formation</th>
                                <th class="bg-primary text-white border-0 text-center">Actions</th>
                            </tr>
                            <tr class="filters">
                                <th>
                                    <input form="catalogueFilters" type="text" name="titre" placeholder="Filtrer titre"
                                        value="{{ $filters['titre'] ?? '' }}"
                                        class="form-control form-control-sm filter-input" />
                                </th>
                                <th>
                                    <input form="catalogueFilters" type="text" name="duree" placeholder="Filtrer durée"
                                        value="{{ $filters['duree'] ?? '' }}"
                                        class="form-control form-control-sm filter-input" />
                                </th>
                                <th>
                                    <input form="catalogueFilters" type="text" name="tarif" placeholder="Filtrer tarif"
                                        value="{{ $filters['tarif'] ?? '' }}"
                                        class="form-control form-control-sm filter-input" />
                                </th>
                                <th>
                                    <select form="catalogueFilters" name="statut"
                                        class="form-select form-select-sm filter-input">
                                        <option value="">Tous</option>
                                        <option value="1"
                                            {{ isset($filters['statut']) && $filters['statut'] === '1' ? 'selected' : '' }}>
                                            Actif</option>
                                        <option value="0"
                                            {{ isset($filters['statut']) && $filters['statut'] === '0' ? 'selected' : '' }}>
                                            Inactif</option>
                                    </select>
                                </th>

                                <th>
                                    <select form="catalogueFilters" id="formationFilter" name="formation_id"
                                        class="form-select form-select-sm filter-input">
                                        <option value="">Toutes les formations</option>
                                        @foreach ($formations as $f)
                                            <option value="{{ $f->id }}"
                                                {{ isset($selectedFormationId) && $selectedFormationId == $f->id ? 'selected' : '' }}>
                                                {{ $f->titre }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button form="catalogueFilters" type="submit" class="btn btn-sm btn-primary">
                                            <i class="bx bx-filter me-1"></i>Filtrer
                                        </button>
                                        <a href="{{ route('catalogue_formation.index') }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="bx bx-reset me-1"></i>Réinitialiser
                                        </a>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($catalogueFormations as $formation)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $formation->titre }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $formation->duree }}h</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-semibold text-dark">{{ number_format($formation->tarif ?? 0, 2) }}
                                            €</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($formation->statut == '1')
                                            <span class="badge bg-success">Actif</span>
                                        @elseif ($formation->statut == '0')
                                            <span class="badge bg-secondary">Inactif</span>
                                        @else
                                            <span class="badge bg-light text-white">{{ $formation->statut }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($formation->formation)
                                            <span
                                                class="badge bg-info text-white">{{ $formation->formation->titre }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('catalogue_formation.show', $formation->id) }}"
                                                class="btn btn-info text-white">
                                                Afficher
                                            </a>
                                            <a href="{{ route('catalogue_formation.edit', $formation->id) }}"
                                                class="btn btn-success">
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
    </div>

    <!-- Formulaire de filtres caché -->
    <form id="catalogueFilters" method="GET" action="{{ route('catalogue_formation.index') }}" class="d-none"></form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var hasData = {{ $catalogueFormations->count() > 0 ? 'true' : 'false' }};

            var table = $('#catalogueFormationsTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucune formation catalogue trouvée."
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
                        // Gestion des filtres personnalisés
                        $('.filter-input').on('keyup change clear', function() {
                            clearTimeout(window._filterTimeout);
                            window._filterTimeout = setTimeout(function() {
                                $('#catalogueFilters').submit();
                            }, 400);
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
