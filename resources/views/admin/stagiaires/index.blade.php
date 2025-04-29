@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('stagiaires.index') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Gestion des stagiaires</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <button class="btn btn-sm text-white btn-info mx-2" data-bs-toggle="modal"
                            data-bs-target="#importModal"><i class="lni lni-cloud-download"></i>importer stagiaires</button>
                        <a href="{{ route('stagiaires.create') }}" type="button" class="btn btn-sm btn-primary mx-2"> <i
                                class="fadeIn animated bx bx-plus"></i> Nouveau stagiaire</a>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Importer des stagiaires</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('stagiaires.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="file" class="form-label">Fichier Excel (.xlsx)</label>
                                    <input type="file" name="file" id="file" class="form-control" required
                                        accept=".xlsx,.xls">
                                </div>

                                <div class="progress mb-3 d-none" id="progressBarWrapper">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                        style="width: 100%;" id="progressBar">
                                        Importation en cours...
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-primary">Importer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (session('ignored'))
            <div class="alert alert-warning border-0 bg-warning alert-dismissible fade show">
                <div class="text-white">
                    <ul>
                        @foreach (session('ignored') as $email)
                            <li>{{ $email }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success border-0 bg-success alert-dismissible fade show">
                <div class="text-white"> {{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <div class="text-white"> {{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <div class="card">
                        <div class="table-responsive px-4 py-4">
                            <div class="dataTables_wrapper dt-bootstrap5">
                                <table id="stagiairesTable" class="table table-bordered table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prenom</th>
                                            <th>Téléphone</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <th><input type="text" placeholder="Filtrer"
                                                    class="form-control form-control-sm" />
                                            </th>
                                            <th><input type="text" placeholder="Filtrer"
                                                    class="form-control form-control-sm" />
                                            </th>
                                            <th><input type="text" placeholder="Filtrer"
                                                    class="form-control form-control-sm" />
                                            </th>
                                            <th><input type="text" placeholder="Filtrer"
                                                    class="form-control form-control-sm" />
                                            </th>

                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stagiaires as $row)
                                            <tr>
                                                <td>{{ $row->user->name }}</td>
                                                <td>{{ $row->prenom }}</td>
                                                <td>{{ $row->telephone }}</td>
                                                <td>{{ $row->user->email }}</td>
                                                <td>
                                                    <a href="{{ route('stagiaires.edit', $row->id) }}"
                                                        class="btn btn-sm btn-success">
                                                        Modifier
                                                    </a>
                                                    <a href="{{ route('stagiaires.show', $row->id) }}"
                                                        class="btn btn-sm btn-info text-white">
                                                        Afficher
                                                    </a>
                                                    @if ($row->statut == 1)
                                                        <form action="{{ route('stagiaires.desactive', $row->id) }}"
                                                            method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                Désactiver
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('stagiaires.active', $row->id) }}"
                                                            method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                Activer
                                                            </button>
                                                        </form>
                                                    @endif
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#importModal form');
            const progressBarWrapper = document.getElementById('progressBarWrapper');

            form.addEventListener('submit', function() {
                progressBarWrapper.classList.remove('d-none');
            });
        });
    </script>
@endsection
