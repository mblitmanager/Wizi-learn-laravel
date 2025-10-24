@extends('admin.layout')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Gestion catalogue
                                formation
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">

                        <a href="{{ route('catalogue_formation.create') }}" type="button"
                            class="btn btn-sm btn-primary px-4"> <i class="fadeIn animated bx bx-plus"></i> Nouveau
                            catalogue formation</a>
                    </div>
                </div>
            </div>
        </div>
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
                        <div class="px-4 py-4">
                            <table  id="stagiairesTable"  class="table table-bordered table-striped table-hover w-100 text-wrap align-middle">
                                <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Formation</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <th>
                                        <input type="text" placeholder="Filtrer" class="form-control form-control-sm" />
                                    </th>
                                    <th>
                                        <input type="text" placeholder="Filtrer" class="form-control form-control-sm" />
                                    </th>
                                    <th>
                                        <select id="formationFilter" class="form-select form-select-sm">
                                            <option value="">Toutes les formations</option>
                                            @foreach($formations as $f)
                                                <option value="{{ $f->id }}" {{ (isset($selectedFormationId) && $selectedFormationId == $f->id) ? 'selected' : '' }}>{{ $f->titre }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($catalogueFormations as $row)
                                    <tr>
                                        <td class="text-break">{{ $row->titre }}</td>
                                        <td class="text-break">{!! $row->description !!}</td>
                                        <td class="text-break">{{ optional($row->formation)->titre }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('catalogue_formation.edit', $row->id) }}"
                                               class="btn btn-sm btn-success mb-1">Modifier</a>
                                            <a href="{{ route('catalogue_formation.show', $row->id) }}"
                                               class="btn btn-sm btn-info text-white mb-1">Afficher</a>
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
    <style>
        td, th {
            word-break: break-word;
            vertical-align: middle;
        }

        table {
            table-layout: fixed;
        }
    </style>

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
                    var api = this.api();
                    // text inputs on first two columns
                    api.columns([0,1]).every(function(index) {
                        var that = this;
                        $('input', this.header()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });

                    // formation select filter: trigger server-side reload with formation_id query param
                    $('#formationFilter').on('change', function() {
                        var val = $(this).val();
                        var url = new URL(window.location.href);
                        if (val) {
                            url.searchParams.set('formation_id', val);
                        } else {
                            url.searchParams.delete('formation_id');
                        }
                        window.location.href = url.toString();
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
