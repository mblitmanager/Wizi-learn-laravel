@extends('admin.layout')
@section('title', 'Gestion des stagiaires')
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
                                <li class="breadcrumb-item"><a href="{{ route('stagiaires.index') }}"
                                        class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i>
                                    </a></li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Stagiaires</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('stagiaires.create') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus me-1"></i> Nouveau stagiaire
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('import_errors'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <div class="flex-grow-1">
                        <strong class="fw-semibold">Erreurs détectées durant l'import :</strong>
                        <ul class="mt-2 mb-0 ps-3">
                            @foreach (session('import_errors') as $err)
                                <li class="mb-1">
                                    <span class="fw-medium">Ligne {{ $err['ligne'] }} :</span> {{ $err['erreur'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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
                            <i class="bx bx-list-ul me-2"></i>Liste des stagiaires
                        </h6>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <!-- Indicateur d'import -->
                            <span id="importStatusContainer" class="align-self-center">
                                @if (!empty($jobRunning) && $jobRunning)
                                    <span class="badge bg-warning text-dark px-3 py-2" title="Un import est en cours">
                                        <i class="bx bx-loader-alt bx-spin me-1"></i> Import en cours...
                                    </span>
                                @elseif(!empty($lastReport))
                                    <a href="{{ route('stagiaires.import.report', $lastReport) }}" id="lastReportLink"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="bx bx-file me-1"></i> Dernier rapport
                                    </a>
                                @endif
                            </span>

                            <!-- Boutons d'action -->
                            <a href="{{ route('download.stagiaire.model') }}" class="btn btn-success btn-sm">
                                <i class="bx bx-download me-1"></i> Modèle Excel
                            </a>

                            <a href="{{ route('stagiaires.import.reports') }}" class="btn btn-secondary btn-sm">
                                <i class="bx bx-history me-1"></i> Rapports
                            </a>

                            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                                data-bs-target="#importModal">
                                <i class="bx bx-upload me-1"></i> Importer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Tableau -->
                <div class="table-responsive">
                    <table id="stagiairesTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Nom</th>
                                <th class="bg-primary text-white border-0">Prénom</th>
                                <th class="bg-primary text-white border-0">Téléphone</th>
                                <th class="bg-primary text-white border-0">Email</th>
                                <th class="bg-primary text-white border-0 text-center">Actions</th>
                            </tr>
                            <tr class="filters">
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
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stagiaires as $row)
                                <tr class="{{ $row->statut == 0 ? 'table-warning' : '' }}">
                                    <td class="fw-semibold text-dark">{{ strtoupper($row->user->name) }}</td>
                                    <td>{{ $row->prenom }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bx bx-phone me-1"></i>{{ $row->telephone }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                            title="{{ $row->user->email }}">
                                            <i class="bx bx-envelope me-1 text-muted"></i>{{ $row->user->email }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('stagiaires.show', $row->id) }}"
                                                class="btn btn-sm btn-info text-white" title="Afficher les détails">
                                                Afficher
                                            </a>
                                            <a href="{{ route('stagiaires.edit', $row->id) }}"
                                                class="btn btn-sm btn-warning text-white" title="Modifier">
                                                Modifier
                                            </a>
                                            @if ($row->statut == 1)
                                                <form action="{{ route('stagiaires.desactive', $row->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        title="Désactiver le stagiaire">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('stagiaires.active', $row->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        title="Activer le stagiaire">
                                                        <i class="bx bx-user-check"></i>
                                                    </button>
                                                </form>
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
    </div>

    <!-- Modal d'import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="bx bx-upload me-2"></i>Importer des stagiaires
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('stagiaires.import') }}" method="POST" enctype="multipart/form-data"
                        id="importForm">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">Fichier Excel (.xlsx, .xls)</label>
                            <input type="file" name="file" id="file" class="form-control" required
                                accept=".xlsx,.xls" onchange="previewFileName(this)">
                            <div class="form-text text-muted">
                                <small>Format accepté : Excel (.xlsx, .xls) - Taille max : 10MB</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" value="1"
                                    id="background" name="background" checked>
                                <label class="form-check-label fw-medium" for="background">
                                    Exécuter en tâche de fond (recommandé)
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <small>L'import s'exécutera en arrière-plan pour les gros fichiers</small>
                            </div>
                        </div>

                        <!-- Barre de progression -->
                        <div class="progress mb-3 d-none" id="progressBarWrapper" style="height: 25px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                role="progressbar" style="width: 0%;" id="progressBar">
                                <span class="fw-medium">Préparation de l'import...</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bx bx-upload me-1"></i> Importer
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
            // Initialisation de DataTable
            var table = $('#stagiairesTable').DataTable({
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

            // Fonction pour afficher le nom du fichier sélectionné
            window.previewFileName = function(input) {
                const fileName = input.files[0]?.name || 'Aucun fichier sélectionné';
                const label = input.parentElement.querySelector('.form-text');
                if (label) {
                    label.innerHTML = `<small>Fichier sélectionné : <strong>${fileName}</strong></small>`;
                }
            };
        });

        // Gestion de l'import avec barre de progression
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('importForm');
            const progressBarWrapper = document.getElementById('progressBarWrapper');
            const progressBar = document.getElementById('progressBar');
            const submitBtn = document.getElementById('submitBtn');

            if (form) {
                form.addEventListener('submit', function(e) {
                    const fileInput = document.getElementById('file');
                    if (!fileInput.files.length) {
                        e.preventDefault();
                        alert('Veuillez sélectionner un fichier à importer.');
                        return;
                    }

                    // Afficher la barre de progression
                    progressBarWrapper.classList.remove('d-none');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Importation...';

                    // Simulation de progression (à adapter avec votre logique réelle)
                    let progress = 0;
                    const interval = setInterval(() => {
                        progress += 5;
                        progressBar.style.width = progress + '%';

                        if (progress >= 90) {
                            clearInterval(interval);
                        }
                    }, 200);
                });
            }
        });

        // Polling pour le statut d'import
        (function() {
            const statusUrl = '{{ route('stagiaires.import.status') }}';
            const reportBase = '{{ url('/administrateur/import/report') }}';
            const pollInterval = {{ config('imports.poll_interval', 5) }} * 1000;

            function updateStatus() {
                $.getJSON(statusUrl, function(data) {
                    const container = $('#importStatusContainer');
                    if (!container.length) return;

                    if (data.running) {
                        container.html(
                            '<span class="badge bg-warning text-dark px-3 py-2" title="Un import est en cours">' +
                            '<i class="bx bx-loader-alt bx-spin me-1"></i> Import en cours...</span>'
                        );
                    } else if (data.lastReport) {
                        const url = reportBase + '/' + encodeURIComponent(data.lastReport);
                        container.html(
                            '<a href="' + url +
                            '" id="lastReportLink" class="btn btn-outline-primary btn-sm">' +
                            '<i class="bx bx-file me-1"></i> Dernier rapport</a>'
                        );
                    } else {
                        container.html('');
                    }
                }).fail(function() {
                    console.error('Erreur lors de la récupération du statut d\'import');
                });
            }

            // Configuration WebSocket/Laravel Echo (si disponible)
            if (window.Echo && typeof window.Echo.channel === 'function') {
                try {
                    window.Echo.channel('import-status')
                        .listen('ImportStatusUpdated', function(e) {
                            const container = $('#importStatusContainer');
                            if (!container.length) return;

                            switch (e.status) {
                                case 'running':
                                case 'queued':
                                    container.html(
                                        '<span class="badge bg-warning text-dark px-3 py-2" title="Un import est en cours">' +
                                        '<i class="bx bx-loader-alt bx-spin me-1"></i> Import en cours...</span>'
                                    );
                                    break;
                                case 'completed':
                                    if (e.report) {
                                        const url = reportBase + '/' + encodeURIComponent(e.report);
                                        container.html(
                                            '<a href="' + url +
                                            '" id="lastReportLink" class="btn btn-outline-primary btn-sm">' +
                                            '<i class="bx bx-file me-1"></i> Dernier rapport</a>'
                                        );
                                    }
                                    break;
                                case 'failed':
                                    container.html(
                                        '<span class="badge bg-danger px-3 py-2">' +
                                        '<i class="bx bx-error me-1"></i> Import échoué</span>'
                                    );
                                    break;
                                default:
                                    container.html('');
                            }
                        });
                } catch (err) {
                    // Fallback vers polling si Echo échoue
                    updateStatus();
                    setInterval(updateStatus, pollInterval);
                }
            } else {
                // Polling standard
                updateStatus();
                setInterval(updateStatus, pollInterval);
            }
        })();
    </script>

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
    </style>
@endsection
