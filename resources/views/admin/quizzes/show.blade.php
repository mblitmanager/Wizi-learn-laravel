@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Gestion Quiz</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('quiz.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-log-out"></i>Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card shadow-lg rounded-4 border-0">
                <div class="card-header bg-gradient-primary text-white text-center rounded-top-4">
                    <h3 class="mb-0">
                        <i class="bx bx-clipboard"></i> {{ $quiz->titre }}
                    </h3>
                    <p class="mb-0 small">Niveau : <span
                            class="badge px- py-2  bg-primary text-black-50">{{ ucfirst($quiz->niveau) }}</span>
                    </p>
                </div>

                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded shadow-sm h-100">
                                <h5><i class="bx bx-detail me-2"></i> Description</h5>
                                <p class="text-muted">{{ $quiz->description ?? 'Aucune description disponible.' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded shadow-sm h-100">
                                <h5><i class="bx bx-time me-2"></i> Durée</h5>
                                <p class="text-muted">{{ $quiz->duree ?? '-' }} minutes</p>

                                <h5 class="mt-4"><i class="bx bx-target-lock me-2"></i> Points Totals</h5>
                                <p class="text-muted">{{ $quiz->nb_points_total ?? 'Non défini' }} points</p>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="card">
                <div class="card-body">
                    <div class="main-body">
                        <div class="row">
                            <div class="card-body">
                                <h5>Liste des questions</h5>
                                <hr>
                                <div class="card">
                                    <div class="card-body">

                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table id="stagiairesTable"
                                                    class="table table-bordered table-striped table-hover mb-0">

                                                    <thead>
                                                        <tr>
                                                            <th>Question</th>
                                                            <th>type</th>
                                                            <th>Action</th>
                                                        </tr>
                                                        <tr>
                                                            <th><input type="text" placeholder="Filtrer"
                                                                    class="form-control form-control-sm" /></th>
                                                            <th><input type="text" placeholder="Filtrer"
                                                                    class="form-control form-control-sm" /></th>
                                                            <th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($quiz->questions as $row)
                                                            <tr>
                                                                <td>{{ $row->text }}</td>
                                                                <td>{{ $row->type }}</td>
                                                                <td class="text-center">
                                                                    <a href="{{ route('question.edit', $row->id) }}"
                                                                        class="btn btn-sm btn-success ">
                                                                        Modifier
                                                                    </a>
                                                                    <a href="{{ route('question.show', $row->id) }}"
                                                                        class="btn btn-sm btn-info text-white">
                                                                        Afficher
                                                                    </a>

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
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#stagiairesTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json"
                },
                paging: true,
                searching: true,
                ordering: true,
                lengthMenu: [5, 10, 25, 50],
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
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
@endsection
