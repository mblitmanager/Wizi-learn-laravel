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
                                    <th>Durée</th>
                                    <th>Tarif</th>
                                    <th>Statut</th>
                                    <th>Lieu</th>
                                    <th>Niveau</th>
                                    <th>Public cible</th>
                                    <th>Formation</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <th>
                                        <form id="catalogueFilters" method="GET" action="{{ route('catalogue_formation.index') }}">
                                            <input type="text" name="titre" placeholder="Filtrer titre" value="{{ $filters['titre'] ?? '' }}" class="form-control form-control-sm filter-input" />
                                        </form>
                                    </th>
                                    <th>
                                        <input form="catalogueFilters" type="text" name="duree" placeholder="Filtrer" class="form-control form-control-sm" disabled />
                                    </th>
                                    <th>
                                        <input form="catalogueFilters" type="text" name="tarif" placeholder="Filtrer" class="form-control form-control-sm" disabled />
                                    </th>
                                    <th>
                                        <select form="catalogueFilters" name="statut" class="form-select form-select-sm filter-input">
                                            <option value="">Tous</option>
                                            <option value="1" {{ (isset($filters['statut']) && $filters['statut'] === '1') ? 'selected' : '' }}>Actif</option>
                                            <option value="0" {{ (isset($filters['statut']) && $filters['statut'] === '0') ? 'selected' : '' }}>Inactif</option>
                                        </select>
                                    </th>
                                    <th>
                                        <input form="catalogueFilters" type="text" name="lieu" placeholder="Filtrer lieu" value="{{ $filters['lieu'] ?? '' }}" class="form-control form-control-sm filter-input" />
                                    </th>
                                    <th>
                                        <input form="catalogueFilters" type="text" name="niveau" placeholder="Filtrer niveau" value="{{ $filters['niveau'] ?? '' }}" class="form-control form-control-sm filter-input" />
                                    </th>
                                    <th>
                                        <input form="catalogueFilters" type="text" name="public_cible" placeholder="Filtrer public" value="{{ $filters['public_cible'] ?? '' }}" class="form-control form-control-sm filter-input" />
                                    </th>
                                    <th>
                                        <select form="catalogueFilters" id="formationFilter" name="formation_id" class="form-select form-select-sm filter-input">
                                            <option value="">Toutes les formations</option>
                                            @foreach($formations as $f)
                                                <option value="{{ $f->id }}" {{ (isset($selectedFormationId) && $selectedFormationId == $f->id) ? 'selected' : '' }}>{{ $f->titre }}</option>
                                            @endforeach
                                        </select>
                                    </th>
                                    <th>
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button form="catalogueFilters" type="submit" class="btn btn-sm btn-primary">Filtrer</button>
                                            <a href="{{ route('catalogue_formation.index') }}" class="btn btn-sm btn-outline-secondary">Réinitialiser</a>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($catalogueFormations as $row)
                                    <tr>
                                        <td class="text-break">{{ $row->titre }}</td>
                                        <td class="text-break">{{ $row->duree }} h</td>
                                        <td class="text-break">{{ number_format($row->tarif ?? 0, 2) }} €</td>
                                        <td class="text-break text-center">
                                            @if ($row->statut == '1')
                                                <span class="badge bg-success">Actif</span>
                                            @elseif ($row->statut == '0')
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                        </td>
                                        <td class="text-break">{{ $row->lieu ?? '-' }}</td>
                                        <td class="text-break">{{ $row->niveau ?? '-' }}</td>
                                        <td class="text-break">{{ $row->public_cible ?? '-' }}</td>
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
                    // keep DataTables client search but also wire our filter inputs to submit the GET form
                    $('.filter-input').on('change keyup', function(e) {
                        // small debounce
                        clearTimeout(window._filterTimeout);
                        window._filterTimeout = setTimeout(function() {
                            $('#catalogueFilters').submit();
                        }, 400);
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
