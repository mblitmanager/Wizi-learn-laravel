@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Gestion formateurs</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <button class="btn btn-sm text-white btn-info mx-2" data-bs-toggle="modal" data-bs-target="#importModal"><i
                        class="lni lni-cloud-download"></i>importer formateurs</button>
                <a href="{{ route('formateur.create') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                        class="fadeIn animated bx bx-plus"></i> Nouveau formateur</a>
            </div>
        </div>
    </div>

    <div>
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Importer formateurs</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('formateur.import') }}" method="POST" enctype="multipart/form-data">
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
                        <table id="stagiairesTable" class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nom</th>
                                    <th>Prenom</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th><input type="text" placeholder="Filtrer" class="form-control form-control-sm" />
                                    </th>
                                    <th><input type="text" placeholder="Filtrer" class="form-control form-control-sm" />
                                    </th>
                                    <th><input type="text" placeholder="Filtrer" class="form-control form-control-sm" />
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($formateurs as $row)
                                    <tr>
                                        <td>{{ $row->id }}</td>
                                        <td>{{ $row->user->name }}</td>
                                        <td>{{ $row->prenom }}</td>
                                        <td>{{ $row->user->email }}</td>
                                        <td>
                                            <a href="{{ route('formateur.edit', $row->id) }}"
                                                class="btn btn-sm btn-success ">
                                                Modifier
                                            </a>
                                            <a href="{{ route('formateur.show', $row->id) }}"
                                                class="btn btn-sm btn-secondary">
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
    @if (session('success'))
        <script>
            $(document).ready(function() {
                Toastify({
                    text: '{{ session('success') }}',
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                    },
                    onClick: function() {}
                }).showToast();
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            $(document).ready(function() {
                Toastify({
                    text: '{{ session('error') }}',
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    },
                    onClick: function() {}
                }).showToast();
            });
        </script>
    @endif
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
