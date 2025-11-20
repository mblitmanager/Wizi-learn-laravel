@extends('admin.layout')
@section('title', 'Détails du quiz')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-clipboard me-2"></i>Détails du quiz
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('quiz.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Quiz
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    {{ $quiz->titre }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('quiz.index') }}" class="btn btn-outline-primary">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <!-- Carte d'informations du quiz -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-0">
                                    <i class="bx bx-clipboard me-2"></i>{{ $quiz->titre }}
                                </h4>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="badge bg-light text-dark px-3 py-2 fs-6">
                                    Niveau : {{ ucfirst($quiz->niveau) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bx bx-detail text-primary me-3 fs-4"></i>
                                            <h5 class="mb-0 text-dark fw-semibold">Description</h5>
                                        </div>
                                        <p class="text-muted mb-0 ps-5">
                                            {!! $quiz->description ?? 'Aucune description disponible.' !!}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bx bx-time text-primary me-3 fs-4"></i>
                                            <h5 class="mb-0 text-dark fw-semibold">Informations</h5>
                                        </div>
                                        <div class="ps-5">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-time-five text-muted me-2"></i>
                                                <span class="text-dark fw-medium">Durée :</span>
                                                <span class="text-muted ms-2">{{ $quiz->duree ?? '-' }} minutes</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bx bx-target-lock text-muted me-2"></i>
                                                <span class="text-dark fw-medium">Points totaux :</span>
                                                <span class="text-muted ms-2">{{ $quiz->nb_points_total ?? 'Non défini' }}
                                                    points</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bx bx-list-check text-muted me-2"></i>
                                                <span class="text-dark fw-medium">Nombre de questions :</span>
                                                <span class="text-muted ms-2">{{ $quiz->questions->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte des questions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-0 text-dark fw-semibold">
                                    <i class="bx bx-question-mark me-2"></i>Liste des questions
                                    <span class="badge bg-primary ms-2">{{ $quiz->questions->count() }}</span>
                                </h6>
                            </div>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="questionsTable" class="table table-hover table-striped align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="bg-primary text-white border-0">Question</th>
                                        <th class="bg-primary text-white border-0">Type</th>
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
                                        <th class="border-bottom"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quiz->questions as $row)
                                        <tr>
                                            <td class="fw-semibold text-dark">
                                                <div class="d-flex align-items-start">
                                                    <span class="badge bg-secondary me-2 mt-1">{{ $loop->iteration }}</span>
                                                    <span class="text-truncate" style="max-width: 400px;"
                                                        title="{{ $row->text }}">
                                                        {{ $row->text }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $row->type == 'multiple' ? 'bg-info' : 'bg-warning text-dark' }}">
                                                    {{ $row->type }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('question.show', $row->id) }}"
                                                        class="btn btn-sm btn-info text-white" title="Afficher les détails">
                                                        Afficher
                                                    </a>
                                                    <a href="{{ route('question.edit', $row->id) }}"
                                                        class="btn btn-sm btn-warning text-white"
                                                        title="Modifier la question">
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
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#questionsTable').DataTable({
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

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
@endsection
