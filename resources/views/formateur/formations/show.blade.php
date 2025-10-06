@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
         <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Détails de la formation
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('formateur.formations.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-log-out"></i> Retour aux formations</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Informations principales -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-info-circle"></i> Informations de la Formation
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-item mb-3">
                                                <strong><i class="fas fa-tag text-primary"></i> Titre :</strong>
                                                <p class="mb-0">{{ $formation->titre }}</p>
                                            </div>
                                            <div class="info-item mb-3">
                                                <strong><i class="fas fa-clock text-primary"></i> Durée :</strong>
                                                <p class="mb-0">
                                                    @if($formation->duree)
                                                        {{ $formation->duree }} heures
                                                    @else
                                                        Non spécifiée
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="info-item mb-3">
                                                <strong><i class="fas fa-certificate text-primary"></i> Certification :</strong>
                                                <p class="mb-0">
                                                    @if($formation->certification)
                                                        <span class="badge badge-success">Oui</span>
                                                    @else
                                                        <span class="badge badge-secondary">Non</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-item mb-3">
                                                <strong><i class="fas fa-euro-sign text-primary"></i> Tarif :</strong>
                                                <p class="mb-0">
                                                    @if($formation->tarif)
                                                        {{ number_format($formation->tarif, 2, ',', ' ') }} €
                                                    @else
                                                        Gratuit
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="info-item mb-3">
                                                <strong><i class="fas fa-circle text-primary"></i> Statut :</strong>
                                                <p class="mb-0">
                                                    <span class="badge bg-{{ $formation->statut ? 'success' : 'secondary' }}">
                                                        {{ $formation->statut ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </p>
                                            </div>
                                            @if($formation->formation)
                                            <div class="info-item mb-3">
                                                <strong><i class="fas fa-layer-group text-primary"></i> Domaine :</strong>
                                                <p class="mb-0">{{ $formation->formation->titre }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    @if($formation->description)
                                    <div class="info-item mt-4">
                                        <strong><i class="fas fa-align-left text-primary"></i> Description :</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {!! $formation->description !!}
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Prérequis -->
                                    @if($formation->prerequis)
                                    <div class="info-item mt-4">
                                        <strong><i class="fas fa-list-check text-primary"></i> Prérequis :</strong>
                                        <div class="mt-2 p-3 bg-light rounded">
                                            {{ $formation->prerequis }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-chart-bar"></i> Statistiques
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <div class="stats-box bg-primary">
                                            <i class="fas fa-users fa-2x"></i>
                                            <h3 class="mt-2">{{ $formation->stagiaires->count() }}</h3>
                                            <p class="mb-0">Stagiaires assignés</p>
                                        </div>
                                    </div>
                                    
                                    @if($formation->stagiaires->count() > 0)
                                    <div class="stats-breakdown">
                                        <h6 class="text-center mb-3">Répartition des stagiaires :</h6>
                                        
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>En cours :</span>
                                            <span>{{ $formation->stagiaires->where('statut', 1)->count() }}</span>
                                        </div>
                                        <div class="progress mb-3" style="height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ ($formation->stagiaires->where('statut', 1)->count() / $formation->stagiaires->count()) * 100 }}%">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Terminés :</span>
                                            <span>{{ $formation->stagiaires->where('statut', 0)->count() }}</span>
                                        </div>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-warning" 
                                                 style="width: {{ ($formation->stagiaires->where('statut', 0)->count() / $formation->stagiaires->count()) * 100 }}%">
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>Aucun stagiaire dans cette formation</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions rapides -->
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-bolt"></i> Actions rapides
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('formateur.stagiaires.index') }}?formation={{ $formation->id }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> Voir tous les stagiaires
                                        </a>
                                        @if($formation->cursus_pdf)
                                        <a href="{{ $formation->cursus_pdf_url }}" 
                                           target="_blank" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-file-pdf"></i> Télécharger le cursus
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des stagiaires -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="fas fa-users"></i> Stagiaires de cette formation
                                        <span class="badge badge-primary ml-2">{{ $formation->stagiaires->count() }}</span>
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @if($formation->stagiaires->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Nom & Prénom</th>
                                                    <th>Email</th>
                                                    <th>Date début</th>
                                                    <th>Date fin</th>
                                                    <th>Statut</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($formation->stagiaires as $stagiaire)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $stagiaire->prenom }} {{ $stagiaire->nom }}</strong>
                                                    </td>
                                                    <td>
                                                        <i class="fas fa-envelope text-muted"></i>
                                                        {{ $stagiaire->user->email ?? 'N/A' }}
                                                    </td>
                                                    <td>
                                                        @if($stagiaire->pivot->date_debut)
                                                            {{ \Carbon\Carbon::parse($stagiaire->pivot->date_debut)->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-muted">Non définie</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($stagiaire->pivot->date_fin)
                                                            {{ \Carbon\Carbon::parse($stagiaire->pivot->date_fin)->format('d/m/Y') }}
                                                        @else
                                                            <span class="text-muted">Non définie</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-{{ $stagiaire->statut === 'actif' ? 'success' : 'secondary' }}">
                                                            {{ $stagiaire->statut === 'actif' ? 'En cours' : 'Terminé' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('formateur.stagiaires.show', $stagiaire->id) }}" 
                                                           class="btn btn-info btn-sm" 
                                                           title="Voir le profil">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <h5>Aucun stagiaire dans cette formation</h5>
                                        <p class="mb-0">Aucun stagiaire ne suit actuellement cette formation.</p>
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

@push('styles')
<style>
.stats-box {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 15px;
}
.stats-box i {
    margin-bottom: 10px;
}
.stats-box h3 {
    margin: 10px 0;
    font-size: 2rem;
}
.stats-box p {
    margin: 0;
    opacity: 0.9;
}
.info-item {
    border-bottom: 1px solid #f8f9fa;
    padding-bottom: 10px;
}
.info-item:last-child {
    border-bottom: none;
}
.progress {
    border-radius: 10px;
}
.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Animation pour les progress bars
    $('.progress-bar').each(function() {
        var width = $(this).attr('style');
        $(this).css('width', '0%').animate({
            width: width
        }, 1000);
    });
});
</script>
@endpush