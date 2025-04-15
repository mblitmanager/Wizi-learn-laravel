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
                    <li class="breadcrumb-item active" aria-current="page">Gestion Quiz</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('quiz.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                        class="fadeIn animated bx bx-log-out"></i> Retour</a>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <div class="main-body">
                        <div class="row">
                            <div class="card-body">
                                {{-- <div class="mb-3">
                                    <h4 class="mb-2">{{$stagiaire->civilite}}.
                                        {{$stagiaire->user->name}}-{{$stagiaire->prenom}}
                                    </h4>
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Nom</strong> :
                                            {{ $stagiaire->user->name }}
                                        </li>
                                        <li class="list-group-item"><strong> Prenom</strong> : {{ $stagiaire->prenom }}
                                        </li>
                                        <li class="list-group-item"><strong>Date de naissance</strong> :
                                            {{ $stagiaire->date_naissance }}
                                        </li>

                                        <li class="list-group-item"><strong>Telephone</strong> :
                                            {{ $stagiaire->telephone }}
                                        </li>
                                        <li class="list-group-item"><strong>Code postal</strong> :
                                            {{ $stagiaire->code_postal }}
                                        </li>
                                        <li class="list-group-item"><strong>Ville</strong> :
                                            {{ $stagiaire->ville }}
                                        </li>

                                        <li class="list-group-item"><strong>Date de creation</strong> :
                                            {{ $stagiaire->created_at->format('d/m/Y à H:i') }}
                                        </li>
                                    </ul>
                                </div> --}}
                                <h5>Liste des questions</h5>
                                <hr>
                                <div class="card">
                                    <div class="card-body">
                                        {{-- <div class="row">
                                            @foreach($quiz->questions as $question)
                                            <p>{{ $question->text }}</p>
                                            @endforeach
                                        </div> --}}
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
                                                                <td>
                                                                    <a href="{{ route('question.edit', $row->id) }}"
                                                                        class="btn btn-sm btn-success " data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" title=""
                                                                        data-bs-original-title="Modifier">
                                                                        <i
                                                                            class="fadeIn animated bx bx-message-square-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('question.show', $row->id) }}" class=""
                                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                                        title="" data-bs-original-title="Afficher">
                                                                        <i
                                                                            class="btn btn-sm btn-info text-white fadeIn animated bx bx-show"></i>
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
        $(document).ready(function () {
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