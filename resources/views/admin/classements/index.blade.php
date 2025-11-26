@extends('admin.layout')
@section('title', 'Classements par partenaire')

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
                                    Classements partenaires
                                </li>
                            </ol>
                        </nav>
                    </div>

                </div>
            </div>
        </div>

        <!-- Bloc principal -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="bx bx-pie-chart-alt me-2"></i>Liste des partenaires
                </h6>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table id="partenaireTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Logo</th>
                                <th class="bg-primary text-white border-0">Partenaire</th>
                                <th class="bg-primary text-white border-0 text-center">Stagiaires</th>
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
                            @forelse ($partenaires as $partenaire)
                                <tr>
                                    <td>
                                        @if ($partenaire->logo)
                                            <img src="{{ asset($partenaire->logo) }}" alt="{{ $partenaire->identifiant }}"
                                                class="rounded-circle" width="45" height="45">
                                        @else
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width:45px;height:45px;font-weight:bold;">
                                                {{ strtoupper(substr($partenaire->identifiant, 0, 2)) }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <strong class="text-dark">{{ $partenaire->identifiant }}</strong><br>
                                        <small class="text-muted">
                                            {{ $partenaire->ville }} ({{ $partenaire->departement }})
                                        </small>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge bg-info text-white px-3 py-2 fw-semibold">
                                            {{ $partenaire->stagiaires_count }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('classements.show', $partenaire->id) }}"
                                            class="btn btn-sm btn-primary text-white">
                                            Voir classement
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <!-- SUPPRIMEZ cette ligne -->
                                <!-- <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    Aucun partenaire trouvé.
                                                </td>
                                            </tr> -->
                            @endforelse
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
            // Vérifiez d'abord si la table a des données
            var hasData = {{ count($partenaires) > 0 ? 'true' : 'false' }};

            var table = $('#partenaireTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    // Ajoutez un message personnalisé pour les tables vides
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
                    // Appliquez les filtres uniquement s'il y a des données
                    if (hasData) {
                        this.api().columns().every(function() {
                            var that = this;
                            // Correction : utilisez .filters au lieu de .footer()
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

            // Alternative : si vous voulez garder la ligne vide personnalisée
            // Supprimez complètement le initComplete et utilisez cette approche :

        });
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

        /* Style pour le message de table vide */
        .dataTables_empty {
            text-align: center;
            padding: 2rem !important;
            color: #6c757d !important;
        }
    </style>
@endsection
