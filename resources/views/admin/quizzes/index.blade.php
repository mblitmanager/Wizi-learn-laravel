@extends('admin.layout')
@section('title', 'Gestion des Quiz')
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
                                    <a href="javascript:;" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Quiz</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('download.quiz.model') }}" class="btn btn-success btn-sm">
                            <i class="bx bx-download me-1"></i> Modèle Excel
                        </a>
                        <button class="btn btn-info btn-sm text-white ms-2" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="bx bx-upload me-1"></i> Importer
                        </button>
                        <a href="{{ route('quiz.create') }}" class="btn btn-primary btn-sm ms-2">
                            <i class="bx bx-plus me-1"></i> Nouveau quiz
                        </a>
                        <button class="btn btn-warning btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bx bx-download me-1"></i> Exporter
                        </button>
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

        <!-- Carte principale -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-list-ul me-2"></i>Liste des quiz
                        </h6>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <span class="badge bg-primary align-self-center">
                                {{ count($quiz) }} quiz(s)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Filtres -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="formationFilter" class="form-label fw-semibold text-dark small">Formation</label>
                        <select id="formationFilter" class="form-select form-select-sm">
                            <option value="">Toutes les formations</option>
                            @foreach ($formations as $formation)
                                <option value="{{ $formation->titre }}">{{ $formation->titre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="categorieFilter" class="form-label fw-semibold text-dark small">Catégorie</label>
                        <select id="categorieFilter" class="form-select form-select-sm">
                            <option value="">Toutes les catégories</option>
                            @foreach ($categories as $categorie)
                                <option value="{{ $categorie }}">{{ $categorie }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="niveauFilter" class="form-label fw-semibold text-dark small">Niveau</label>
                        <select id="niveauFilter" class="form-select form-select-sm">
                            <option value="">Tous les niveaux</option>
                            @foreach ($niveaux as $niveau)
                                <option value="{{ $niveau }}">{{ $niveau }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label fw-semibold text-dark small">Statut</label>
                        <select id="statusFilter" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            @foreach ($status as $statut)
                                <option value="{{ $statut }}">{{ $statut }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="table-responsive">
                    <table id="quizTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Titre</th>
                                <th class="bg-primary text-white border-0 d-none">Formation</th>
                                <th class="bg-primary text-white border-0 d-none">Catégorie</th>
                                <th class="bg-primary text-white border-0">Niveau</th>
                                <th class="bg-primary text-white border-0">Statut</th>
                                <th class="bg-primary text-white border-0">Durée</th>
                                <th class="bg-primary text-white border-0 text-center">Actions</th>
                            </tr>
                            <tr class="filters">
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom d-none">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom d-none">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quiz as $row)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $row->titre }}</td>
                                    <td class="d-none">{{ $row->formation->titre ?? '' }}</td>
                                    <td class="d-none">{{ $row->formation->categorie ?? '' }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ $row->niveau }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $row->status == 'Actif' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $row->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <i class="bx bx-time me-1"></i>{{ $row->duree }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('quiz.show', $row->id) }}"
                                                class="btn btn-sm btn-info text-white" title="Afficher les détails">
                                                Afficher
                                            </a>
                                            <a href="{{ route('quiz.edit', $row->id) }}"
                                                class="btn btn-sm btn-success text-white" title="Modifier le quiz">
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

    <!-- Modal d'import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bx bx-upload me-2"></i>Importer des quiz
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('quiz.import') }}" method="POST" enctype="multipart/form-data"
                        id="importForm">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">Fichier Excel (.xlsx, .xls)</label>
                            <input type="file" name="file" id="file" class="form-control" required
                                accept=".xlsx,.xls" onchange="previewFileName(this)">
                            <div class="form-text text-muted">
                                <small>Format accepté : Excel (.xlsx, .xls)</small>
                            </div>
                        </div>

                        <div class="progress mb-3 d-none" id="progressBarWrapper">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                role="progressbar" style="width: 100%;" id="progressBar">
                                Importation en cours...
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-upload me-1"></i> Importer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'export -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="exportModalLabel">
                        <i class="bx bx-download me-2"></i>Exporter des quiz
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="exportForm" method="GET">
                        <div class="mb-3">
                            <label for="quizSelect" class="form-label fw-semibold">Sélectionnez les quiz à
                                exporter</label>
                            <select id="quizSelect" name="quiz_ids[]" class="form-select" multiple required>
                                @foreach ($quiz as $row)
                                    <option value="{{ $row->id }}">{{ $row->titre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="bx bx-download me-1"></i> Exporter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#quizTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json"
                },
                paging: true,
                searching: true,
                ordering: true,
                lengthMenu: [5, 10, 25, 50],
                pageLength: 10,
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

            // Filtre par formation
            $('#formationFilter').on('change', function() {
                var val = $(this).val();
                table.column(1).search(val).draw();
            });

            // Filtre par catégorie
            $('#categorieFilter').on('change', function() {
                var val = $(this).val();
                table.column(2).search(val).draw();
            });

            // Filtre par niveau
            $('#niveauFilter').on('change', function() {
                var val = $(this).val();
                table.column(3).search(val).draw();
            });

            // Filtre par statut
            $('#statusFilter').on('change', function() {
                var val = $(this).val();
                table.column(4).search(val).draw();
            });

            // Fonction pour afficher le nom du fichier sélectionné
            window.previewFileName = function(input) {
                const fileName = input.files[0]?.name || 'Aucun fichier sélectionné';
                const label = input.parentElement.querySelector('.form-text');
                if (label) {
                    label.innerHTML = `<small>Fichier sélectionné : <strong>${fileName}</strong></small>`;
                }
            };
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#importModal form');
            const progressBarWrapper = document.getElementById('progressBarWrapper');

            form.addEventListener('submit', function() {
                progressBarWrapper.classList.remove('d-none');
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
