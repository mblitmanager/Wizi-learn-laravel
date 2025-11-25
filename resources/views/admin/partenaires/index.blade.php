@extends('admin.layout')
@section('title', 'Liste des partenaires')

@section('content')
    <div class="container-fluid">

        <!-- En-tête -->
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
                                <li class="breadcrumb-item active text-dark fw-semibold">
                                    Liste des partenaires
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-sm text-white btn-info mx-2" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="lni lni-cloud-download me-1"></i>Importer
                        </button>
                        <a href="{{ route('partenaires.create') }}" type="button" class="btn btn-sm btn-primary px-4">
                            <i class="fadeIn animated bx bx-plus me-1"></i> Nouveau partenaire
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <!-- Messages d'alerte -->
        @if (session('import_errors'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show mb-4">
                <div class="text-white">
                    <strong>Erreurs détectées durant l'import :</strong>
                    <ul class="mt-2 mb-0 ps-4">
                        @foreach (session('import_errors') as $err)
                            <li>
                                <strong>Ligne {{ $err['ligne'] }} :</strong> {{ $err['erreur'] }}
                            </li>
                        @endforeach
                    </ul>
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
                            <i class="lni lni-cloud-download me-2"></i>Importer des partenaires
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('partenaires.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Fichier Excel (.xlsx, .xls)</label>
                                <input type="file" name="file" id="file" class="form-control" required
                                    accept=".xlsx,.xls">
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
                                    <i class="lni lni-cloud-download me-1"></i>Importer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloc principal -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="bx bx-group me-2"></i>Gestion des partenaires
                </h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="partenaireTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Logo</th>
                                <th class="bg-primary text-white border-0">Partenaire</th>
                                <th class="bg-primary text-white border-0 text-center">Localisation</th>
                                <th class="bg-primary text-white border-0 text-center">Type</th>
                                <th class="bg-primary text-white border-0 text-center">Stagiaires</th>

                                <th class="bg-primary text-white border-0 text-center"></th>
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
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filtrer...">
                                </th>

                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($partenaires as $partenaire)
                                <tr>
                                    <td>
                                        @if ($partenaire->logo)
                                            <img src="{{ asset($partenaire->logo) }}" alt="{{ $partenaire->identifiant }}"
                                                class="rounded-circle" width="50" height="50"
                                                style="object-fit: cover;">
                                        @else
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                style="width:50px;height:50px;font-weight:bold;font-size:14px;">
                                                {{ strtoupper(substr($partenaire->identifiant, 0, 2)) }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('partenaires.show', $partenaire->id) }}"
                                            class="text-decoration-none text-dark fw-semibold">
                                            {{ $partenaire->identifiant }}
                                        </a>
                                    </td>

                                    <td class="text-center">
                                        <div class="text-dark fw-medium">{{ $partenaire->ville }}</div>
                                        <small class="text-muted">
                                            {{ $partenaire->departement }} • {{ $partenaire->code_postal }}
                                        </small>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2">
                                            {{ $partenaire->type }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex flex-wrap gap-1 justify-content-center"
                                            style="max-width: 200px;">
                                            @forelse ($partenaire->stagiaires as $stagiaire)
                                                <span class="badge bg-info text-white mb-1 px-2 py-1">
                                                    {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                                                </span>
                                            @empty
                                                <span class="text-muted small">Aucun stagiaire</span>
                                            @endforelse
                                        </div>
                                    </td>


                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('partenaires.show', $partenaire->id) }}"
                                                class="btn btn-sm btn-info text-white w-100 d-flex align-items-center justify-content-center"
                                                style="min-width: 100px;">
                                                </i>Afficher
                                            </a>
                                            <a href="{{ route('partenaires.edit', $partenaire->id) }}"
                                                class="btn btn-sm btn-success w-100 d-flex align-items-center justify-content-center"
                                                style="min-width: 100px;">
                                                Modifier
                                            </a>
                                            <form action="{{ route('partenaires.destroy', $partenaire->id) }}"
                                                method="POST" class="w-100">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-danger w-100 d-flex align-items-center justify-content-center"
                                                    onclick="return confirm('Supprimer ce partenaire ?')"
                                                    style="min-width: 100px;">
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var hasData = {{ count($partenaires) > 0 ? 'true' : 'false' }};

            var table = $('#partenaireTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucun partenaire trouvé."
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
            $('form').on('submit', function() {
                $('#progressBarWrapper').removeClass('d-none');
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
