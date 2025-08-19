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

                        <button class="btn btn-sm text-white btn-info mx-2" data-bs-toggle="modal"
                            data-bs-target="#importModal"><i class="lni lni-cloud-download"></i>importer partenaire</button>
                        <a href="{{ route('partenaires.create') }}" type="button" class="btn btn-sm btn-primary px-4">
                            <i class="fadeIn animated bx bx-plus"></i> Nouveau partenaire</a>
                    </div>
                </div>
            </div>
        </div>
        @if (session('import_errors'))
            <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
                <div class="text-white">
                    <strong>Erreurs détectées durant l'import :</strong>
                    <ul class="mt-2 mb-0 ps-4">
                        @foreach (session('import_errors') as $err)
                            <li>
                                <strong>Ligne {{ $err['ligne'] }} :</strong> {{ $err['erreur'] }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div>
            <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header  bg-primary">
                            <h5 class="modal-title text-white" id="exampleModalLabel">Importer des partenaires</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('partenaires.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label for="file" class="form-label">Fichier Excel (.xlsx)</label>
                                    <input type="file" name="file" id="file" class="form-control" required
                                        accept=".xlsx,.xls">
                                </div>

                                <div class="progress mb-3 d-none" id="progressBarWrapper">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                        style="width: 100%;" id="progressBar">
                                        Importation en cours...
                                    </div>
                                </div>


                                <button type="submit" class="btn btn-sm btn-primary">Importer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="px-4 py-4">
                                <table class="table table-bordered table-hover w-100 text-wrap align-middle">
                                    <thead>
                                        <tr>
                                            <th>Identifiant</th>
                                            <th>Ville</th>
                                            <th>Département</th>
                                            <th>Code postal</th>
                                            <th>Type</th>
                                            <th>Logo</th>
                                            <th>Stagiaires</th>
                                            <th>Contact</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($partenaires as $partenaire)
                                            <tr>
                                                <td class="text-break">
                                                    <a href="{{ route('partenaires.show', $partenaire->id) }}"
                                                        class="text-decoration-underline">
                                                        {{ $partenaire->identifiant }}
                                                    </a>
                                                </td>
                                                <td class="text-break">{{ $partenaire->ville }}</td>
                                                <td>{{ $partenaire->departement }}</td>
                                                <td>{{ $partenaire->code_postal }}</td>
                                                <td>{{ $partenaire->type }}</td>
                                                <td>
                                                    @if ($partenaire->logo)
                                                        <img src="{{ asset($partenaire->logo) }}" alt="Logo"
                                                            style="max-width:60px; max-height:60px;"
                                                            class="img-fluid rounded">
                                                    @endif
                                                </td>
                                                <td class="text-break">
                                                    @foreach ($partenaire->stagiaires as $stagiaire)
                                                        <span class="badge bg-info mb-1">
                                                            {{ $stagiaire->prenom }} {{ $stagiaire->nom }}
                                                        </span>
                                                    @endforeach
                                                </td>
                                                <td class="text-break">
                                                    @php($c = is_array($partenaire->contacts ?? null) && count($partenaire->contacts) ? $partenaire->contacts[0] : null)
                                                    @if ($c)
                                                        <div><strong>{{ $c['prenom'] ?? '' }}
                                                                {{ $c['nom'] ?? '' }}</strong></div>
                                                        @if (!empty($c['fonction']))
                                                            <div>{{ $c['fonction'] }}</div>
                                                        @endif
                                                        @if (!empty($c['tel']))
                                                            <div>{{ $c['tel'] }}</div>
                                                        @endif
                                                        @if (!empty($c['email']))
                                                            <div>{{ $c['email'] }}</div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-nowrap">
                                                    <a href="{{ route('partenaires.show', $partenaire->id) }}"
                                                        class="btn btn-sm btn-info text-white mb-1">Afficher</a>
                                                    <a href="{{ route('partenaires.edit', $partenaire->id) }}"
                                                        class="btn btn-sm btn-success mb-1">Modifier</a>
                                                    <form action="{{ route('partenaires.destroy', $partenaire->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger mb-1"
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
