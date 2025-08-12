@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('parrainage.index') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Filleuls de
                                {{ $parrain->name }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('parrainage.index') }}" type="button" class="btn btn-sm btn-primary"><i
                                class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                    </div>
                </div>
            </div>
        </div>



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
                                            <th>Email</th>
                                            <th>Date de parrainage</th>
                                            <th>Points</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @foreach ($parrain->parrainages as $parrainage)
                                            <tr>
                                                <td>{{ $parrainage->filleul->name }}</td>
                                                <td>{{ $parrainage->filleul->email }}</td>
                                                <td>{{ \Carbon\Carbon::parse($parrainage->date_parrainage)->format('d/m/Y') }}
                                                </td>
                                                <td>{{ $parrainage->points }}</td>
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
