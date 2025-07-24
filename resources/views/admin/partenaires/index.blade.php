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
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Liste des
                                partenaires

                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">


                        <a href="{{ route('partenaires.create') }}" type="button" class="btn btn-sm btn-primary px-4">
                            <i class="fadeIn animated bx bx-plus"></i> Nouveau partenaire</a>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="table-responsive px-4 py-4">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Identifiant</th>
                                            <th>Ville</th>
                                            <th>Département</th>
                                            <th>Code postal</th>
                                            <th>Type</th>
                                            <th>Logo</th>
                                            <th>Stagiaires</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($partenaires as $partenaire)
                                            <tr>

                                                <td>
                                                    <a href="{{ route('partenaires.show', $partenaire->id) }}"
                                                        class="text-decoration-underline">
                                                        {{ $partenaire->identifiant }}
                                                    </a>
                                                </td>
                                                <td>{{ $partenaire->adresse }}</td>
                                                <td>{{ $partenaire->departement }}</td>
                                                <td>{{ $partenaire->code_postal }}</td>
                                                <td>{{ $partenaire->type }}</td>
                                                <td>
                                                    @if ($partenaire->logo)
                                                        <img src="{{ asset($partenaire->logo) }}" alt="Logo"
                                                            style="max-width:60px;max-height:60px;">
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach ($partenaire->stagiaires as $stagiaire)
                                                        <span class="badge bg-info">{{ $stagiaire->prenom }}
                                                            {{ $stagiaire->nom }}</span>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    <a href="{{ route('partenaires.show', $partenaire->id) }}"
                                                        class="btn btn-sm btn-info text-white">Afficher</a>
                                                    <a href="{{ route('partenaires.edit', $partenaire->id) }}"
                                                        class="btn btn-sm btn-success">Modifier</a>
                                                    <form action="{{ route('partenaires.destroy', $partenaire->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Supprimer ce partenaire ?')">Supprimer</button>
                                                    </form>
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
@endsection
