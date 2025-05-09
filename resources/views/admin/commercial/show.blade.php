@extends('admin.layout')
@section('title', 'Ajouter un formateur')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Gestion commercial</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('commercials.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
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

                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Nom</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control"
                                                    value="{{ $commercial->user->name }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Prenom</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control"
                                                    value="{{ $commercial->prenom }}" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Email</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control"
                                                    value="{{ $commercial->user->email }}" readonly>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="d-flex align-items-center mb-3">Stagiaire associées</h5>
                                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 row-cols-xl-3">
                                        <table class="table mb-0 table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Nom</th>
                                                    <th scope="col">Prenom</th>
                                                    <th scope="col">Adresse</th>
                                                    <th scope="col">Téléphone</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($commercial->stagiaires as $row)
                                                    <tr>
                                                        <th scope="row">{{ $row->id }}</th>
                                                        <td>{{ $row->user->name }}</td>
                                                        <td>{{ $row->prenom }}</td>
                                                        <td>{{ $row->adresse }}</td>
                                                        <td>{{ $row->telephone }}</td>
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
@endsection
@section('scripts')
@endsection