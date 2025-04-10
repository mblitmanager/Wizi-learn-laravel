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
                    <li class="breadcrumb-item active" aria-current="page">Gestion stagiaire</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('stagiaires.create') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                        class="fadeIn animated bx bx-plus"></i> Nouveau stagiaire</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="stagiairesTable" class="table table-bordered table-striped table-hover mb-0">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Civilité</th>
                                <th>Nom</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Adresse</th>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><input type="text" placeholder="Filtrer" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Filtrer" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Filtrer" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Filtrer" class="form-control form-control-sm" /></th>
                                <th><input type="text" placeholder="Filtrer" class="form-control form-control-sm" /></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stagiaires as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>{{ $row->civilite }}</td>
                                    <td>{{ $row->user->name }}</td>
                                    <td>{{ $row->telephone }}</td>
                                    <td>{{ $row->user->email }}</td>
                                    <td>{{ $row->adresse }}</td>
                                    <td>
                                        <a href="{{ route('stagiaires.edit', $row->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="" data-bs-original-title="Modifier">
                                            <i class="btn btn-sm btn-success fadeIn animated bx bx-message-square-edit"></i>
                                        </a>
                                        <a href="{{ route('stagiaires.show', $row->id) }}" class="" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="" data-bs-original-title="Afficher">
                                            <i class="btn btn-sm btn-info text-white fadeIn animated bx bx-show"></i>
                                        </a>
                                        @if ($row->statut == 1)
                                            <form action="{{ route('stagiaires.desactive', $row->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="" data-bs-original-title="Désactiver">
                                                    <i class="fadeIn animated bx bx-shield-x"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('stagiaires.active', $row->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="" data-bs-original-title="Activer">
                                                    <i class="fadeIn animated bx bx-check-shield"></i>
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
    @if(session('success'))
        <script>
            $(document).ready(function () {
                Toastify({
                    text: '{{session('success')}}',
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                    },
                    onClick: function () { }
                }).showToast();
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            $(document).ready(function () {
                Toastify({
                    text: '{{session('error')}}',
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    stopOnFocus: true,
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    },
                    onClick: function () { }
                }).showToast();
            });
        </script>
    @endif
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var table = $('#stagiairesTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json"
                },
                paging: true,
                searching: true,
                ordering: true,
                lengthMenu: [5, 10, 25, 50],
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                initComplete: function () {
                    this.api().columns().every(function () {
                        var that = this;
                        $('input', this.header()).on('keyup change clear', function () {
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