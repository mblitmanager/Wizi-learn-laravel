@extends('admin.layout')
@section('title', 'Gestion des commerciaux')
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
                                    Commerciaux
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('download.commercial.model') }}" class="btn btn-outline-success">
                            <i class="bx bx-download me-1"></i> Modèle commercial
                        </a>
                        <button class="btn btn-info text-white ms-2" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="lni lni-cloud-download me-1"></i> Importer
                        </button>
                        <a href="{{ route('commercials.create') }}" class="btn btn-primary ms-2">
                            <i class="bx bx-plus me-1"></i> Nouveau commercial
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('ignoredEmails'))
            <div class="alert alert-warning border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">Commerciaux déjà existants</span>
                </div>
                <div class="mt-2">
                    {!! session('ignoredMessage') !!}
                    <ul class="mt-2 mb-0 ps-4">
                        @foreach (session('ignoredEmails') as $email)
                            <li>{{ $email }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

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

        <!-- Modal d'import -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white" id="importModalLabel">
                            <i class="lni lni-cloud-download me-2"></i>Importer des commerciaux
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('commercials.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label fw-semibold text-dark">Fichier Excel (.xlsx,
                                    .xls)</label>
                                <input type="file" name="file" id="file" class="form-control" required
                                    accept=".xlsx,.xls">
                                <div class="form-text">Assurez-vous que le fichier suit le modèle fourni</div>
                            </div>

                            <div class="progress mb-3 d-none" id="progressBarWrapper">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 100%;" id="progressBar">
                                    Importation en cours...
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-sm btn-secondary"
                                    data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="lni lni-cloud-download me-1"></i> Importer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des commerciaux -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="bx bx-list-ul me-2"></i>Liste des commerciaux
                    <span class="badge bg-info text-dark ms-2">{{ $commercial->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="commerciauxTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Nom</th>
                                <th class="bg-primary text-white border-0">Prénom</th>
                                <th class="bg-primary text-white border-0">Email</th>
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
                            @foreach ($commercial as $commercialItem)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $commercialItem->user->name }}</div>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $commercialItem->prenom }}</div>
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $commercialItem->user->email }}"
                                            class="text-decoration-none text-dark">
                                            {{ $commercialItem->user->email }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('commercials.show', $commercialItem->id) }}"
                                                class="btn btn-info text-white">
                                                Afficher
                                            </a>
                                            <a href="{{ route('commercials.edit', $commercialItem->id) }}"
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var hasData = {{ $commercial->count() > 0 ? 'true' : 'false' }};

            var table = $('#commerciauxTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucun commercial trouvé."
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

            // Gestion de la barre de progression
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('#importModal form');
                const progressBarWrapper = document.getElementById('progressBarWrapper');

                if (form) {
                    form.addEventListener('submit', function() {
                        progressBarWrapper.classList.remove('d-none');
                    });
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
