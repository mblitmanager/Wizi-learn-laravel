@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
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
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Détails du Stagiaire
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
                    <!-- Contenu des détails du stagiaire -->
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Informations personnelles</h4>
                            <p><strong>Nom :</strong> {{ $stagiaire->prenom }} {{ $stagiaire->nom }}</p>
                            <p><strong>Email :</strong> {{ $stagiaire->user->email ?? 'N/A' }}</p>
                            <p><strong>Date début :</strong> {{ \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y H:i') }}</p>
                            <p><strong>Date fin :</strong> {{ $stagiaire->date_fin_formation ? \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y H:i') : 'Non définie' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>Formations</h4>
                            @foreach($stagiaire->catalogue_formations as $formation)
                                <span class="badge bg-primary">{{ $formation->titre }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection