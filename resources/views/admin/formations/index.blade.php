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
                <a href="{{ route('formations.create') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                        class="fadeIn animated bx bx-plus"></i> Nouveau formations</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-responsive px-3 py-3">
                        <table id="stagiairesTable" class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>

                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Duree</th>
                                    <th>Action</th>
                                </tr>
                                <tr>

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
                                @foreach ($formations as $row)
                                    <tr>

                                        <td>{{ $row->titre }}</td>
                                        <td>{{ $row->description }}</td>
                                        <td>{{ $row->duree }}</td>
                                        <td>
                                            <a href="{{ route('formations.edit', $row->id) }}"
                                                class=" btn btn-sm btn-success">
                                                Modifier
                                            </a>
                                            <a href="{{ route('formations.show', $row->id) }}"
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
@endsection
