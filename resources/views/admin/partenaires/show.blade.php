@extends('admin.layout')
@section('title', 'Détails du partenaire')
@section('content')
<div class="container-fluid">
    <div class="shadow-lg border-0 px-2 py-2 mb-3">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
            <div class="breadcrumb-title pe-3"></div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('partenaires.index') }}"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active text-uppercase " aria-current="page">Détails du partenaire</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('partenaires.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i class="fadeIn animated bx bx-log-out"></i> Retour</a>
                    <a href="{{ route('partenaires.edit', $partenaire->id) }}" type="button" class="btn btn-sm btn-warning px-4 ms-2"> <i class="bx bx-edit"></i> Modifier</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="main-body">
                        <div class="text-center mb-4">
                            @if($partenaire->logo)
                                <img src="{{ asset($partenaire->logo) }}" class="rounded shadow" width="120" height="120" alt="Logo" style="object-fit: contain">
                            @endif
                            <h3 class="mt-3 mb-1">{{ $partenaire->identifiant }}</h3>
                            <span class="badge bg-info text-dark px-3 py-1">{{ ucfirst($partenaire->type) }}</span>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4">Adresse :</label>
                            <div class="col-sm-8">{{ $partenaire->adresse }}</div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4">Ville :</label>
                            <div class="col-sm-8">{{ $partenaire->ville }}</div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4">Département :</label>
                            <div class="col-sm-8">{{ $partenaire->departement }}</div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4">Code postal :</label>
                            <div class="col-sm-8">{{ $partenaire->code_postal }}</div>
                        </div>
                        <hr>
                        <div class="container my-4">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header text-dark">
                                    <h5 class="mb-0"><i class="bx bx-group me-2"></i> Stagiaires Associés</h5>
                                </div>
                                <div class="card-body" style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">
                                    @if($partenaire->stagiaires->isNotEmpty())
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nom</th>
                                                    <th>Prénom</th>
                                                    <th>Email</th>
                                                    <th>Ville</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($partenaire->stagiaires as $stagiaire)
                                                    <tr>
                                                        <td>{{ $stagiaire->nom }}</td>
                                                        <td>{{ $stagiaire->prenom }}</td>
                                                        <td>{{ $stagiaire->user->email ?? '-' }}</td>
                                                        <td>{{ $stagiaire->ville }}</td>
                                                        <td>
                                                            <a href="{{ route('stagiaires.show', $stagiaire->id) }}" class="btn btn-sm btn-info">Voir</a>
                                                            <a href="{{ route('stagiaires.edit', $stagiaire->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-warning mt-4">
                                            <strong>Aucun stagiaire associé à ce partenaire.</strong>
                                        </div>
                                    @endif
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
