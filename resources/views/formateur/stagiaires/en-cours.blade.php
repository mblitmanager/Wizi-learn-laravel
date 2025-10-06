@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
             <div class="shadow-lg border-0 px-2 py-2 mb-3">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="breadcrumb-title pe-3"></div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Stagiaires en Cours de Formation
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('formateur.stagiaires.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                            class="fadeIn animated bx bx-log-out"></i> Retour</a>
                </div>
            </div>
        </div>
    </div>
            <div class="card">
                
                <div class="card-body">
                    @if($stagiairesEnCours->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Formation</th>
                                    <th>Date début</th>
                                    <th>Date fin prévue</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stagiairesEnCours as $stagiaire)
                                <tr>
                                    <td>{{ $stagiaire->prenom }} {{ $stagiaire->nom ?? '' }}</td>
                                    <td>{{ $stagiaire->user->email ?? 'N/A' }}</td>
                                    <td>
                                       
                                         @foreach($stagiaire->catalogue_formations as $formation)
        <span class="badge bg-primary me-1 mb-1">{{ $formation->titre }}</span>
        @if($loop->iteration % 2 == 0 && !$loop->last)
            <br>
        @endif
    @endforeach
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y') }}</td>
                                    <td>{{ $stagiaire->date_fin_formation ? \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y') : 'Non définie' }}</td>
                                    
                                    <td>
                                        <a href="{{ route('formateur.stagiaires.show', $stagiaire->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $stagiairesEnCours->links() }}
                    </div>
                    @else
                    <div class="alert alert-info text-center">
                        <h5>Aucun stagiaire en cours de formation</h5>
                        <p>Tous vos stagiaires ont terminé leur formation ou n'ont pas encore commencé.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection