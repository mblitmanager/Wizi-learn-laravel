@extends('admin.layout')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Gestion des media
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('medias.create') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-plus"></i> Nouveau media</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card">
                        <div class="table-responsive px-3 py-3">
                            <table id="stagiairesTable" class="table table-bordered table-striped table-hover mb-0">

                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Type</th>
                                        <th>url</th>
                                        <th>Action</th>
                                    </tr>
                                    <tr>
                                        <th><input type="text" placeholder="Filtrer"
                                                class="form-control form-control-sm" />
                                        </th>
                                        <th><input type="text" placeholder="Filtrer"
                                                class="form-control form-control-sm" />
                                        </th>
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
                                    @foreach ($media as $row)
                                        <tr>
                                            <td>
                                                @if ($row->type === 'image')
                                                    <img src="{{ asset($row->url) }}" alt="Image"
                                                        style="max-width: 60px;max-height: 60px; object-fit: cover">
                                                @elseif ($row->type === 'audio')
                                                    <img src="{{ asset('assets/images/mp3.png') }}" alt="Audio"
                                                        style="max-width: 50px;">
                                                @elseif ($row->type === 'document')
                                                    <img src="{{ asset('assets/images/des-documents.png') }}" alt="Document"
                                                        style="max-width: 50px;">
                                                @elseif($row->type = 'video')
                                                    <img src="{{ asset('assets/images/mp4.png') }}" alt="Document"
                                                        style="max-width: 50px;">
                                                @else
                                                    <span>Type inconnu</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->titre }}</td>
                                            <td>{{ $row->categorie }}</td>
                                            <td>{{ $row->type }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('medias.edit', $row->id) }}"
                                                    class="btn btn-sm btn-success">
                                                    Modifier
                                                </a>
                                                <a href="{{ route('medias.show', $row->id) }}"
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
