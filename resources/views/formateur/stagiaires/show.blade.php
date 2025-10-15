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
                                <li class="breadcrumb-item">
                                    <a href="{{ route('formateur.stagiaires.index') }}">Stagiaires</a>
                                </li>
                                <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">
                                    Détails de {{ $stagiaire->prenom }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="ms-auto">
                        <div class="btn-group">
                            <a href="{{ route('formateur.stagiaires.index') }}" class="btn btn-sm btn-primary px-4">
                                <i class="fadeIn animated bx bx-log-out"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- En-tête du stagiaire -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 class="text-primary">{{ $stagiaire->civilite }} {{ $stagiaire->prenom }}</h3>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong><i class="fas fa-envelope"></i> Email:</strong> {{ $stagiaire->user->email ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong><i class="fas fa-phone"></i> Téléphone:</strong> {{ $stagiaire->telephone ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong><i class="fas fa-map-marker-alt"></i> Adresse:</strong> {{ $stagiaire->adresse ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong><i class="fas fa-city"></i> Ville:</strong> {{ $stagiaire->ville ?? 'N/A' }} {{ $stagiaire->code_postal ? '('.$stagiaire->code_postal.')' : '' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong><i class="fas fa-birthday-cake"></i> Date de naissance:</strong> {{ $stagiaire->date_naissance ? \Carbon\Carbon::parse($stagiaire->date_naissance)->format('d/m/Y') : 'N/A' }}</p>
                                    <p class="mb-2"><strong><i class="fas fa-calendar-day"></i> Date début formation:</strong> {{ $stagiaire->date_debut_formation ? \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y') : 'N/A' }}</p>
                                    <p class="mb-2"><strong><i class="fas fa-calendar-check"></i> Date fin formation:</strong> {{ $stagiaire->date_fin_formation ? \Carbon\Carbon::parse($stagiaire->date_fin_formation)->format('d/m/Y') : 'Non définie' }}</p>
                                    <p class="mb-0"><strong><i class="fas fa-user-tag"></i> Rôle:</strong> {{ ucfirst($stagiaire->role) ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="bg-primary text-white p-3 rounded mb-3">
                                    <h4 class="mb-0 text-white">{{ $statistiques['total_points'] ?? 0 }} points</h4>
                                    <small>Total des points</small>
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Statut:</strong>
                                    <span class="badge {{ $stagiaire->statut === 1 ? 'bg-success' : ($stagiaire->statut === 'inactif' ? 'bg-secondary' : 'bg-warning') }} ms-2">
                                        {{ $stagiaire->statut === 1 ? 'Actif' : ($stagiaire->statut === 0 ? 'Inactif' : 'En attente') }}
                                    </span>
                                </div>

                                @if($stagiaire->partenaire)
                                <div class="mb-3">
                                    <strong>Partenaire:</strong>
                                    <div class="fw-bold text-primary">{{ $stagiaire->partenaire->nom ?? 'N/A' }}</div>
                                </div>
                                @endif

                                @if(isset($statistiques['derniere_activite']))
                                <div class="mb-3">
                                    <strong>Dernière activité:</strong>
                                    <div class="text-muted">{{ $statistiques['derniere_activite']->diffForHumans() }}</div>
                                </div>
                                @endif

                                @if($stagiaire->a_utilise_application ?? false)
                                    <div class="bg-success text-white p-2 rounded">
                                        <small><i class="fas fa-check-circle"></i> Application utilisée</small>
                                    </div>
                                @else
                                    <div class="bg-warning text-white p-2 rounded">
                                        <small><i class="fas fa-clock"></i> Application non utilisée</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formations du stagiaire -->
            @if($stagiaire->catalogue_formations->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="card-title mb-0 text-white">
                                <i class="fas fa-graduation-cap"></i> Formations Assignées
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($stagiaire->catalogue_formations as $catalogueFormation)
                                @php
                                    $formation = $catalogueFormation->formation;
                                    $categorie = $formation->categorie ?? 'default';
                                    $couleurCategorie = match($categorie) {
                                        'Bureautique' => 'bureautique',
                                        'Langues' => 'langues',
                                        'Internet' => 'internet',
                                        'Création' => 'creation',
                                        default => 'secondary'
                                    };
                                @endphp
                                <div class="col-md-3 mb-3">
                                    <div class="card custom-card-{{ $couleurCategorie }} h-100">
                                        <div class="card-header custom-header-{{ $couleurCategorie }} text-white d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0 text-white">
                                                <i class="fas {{ $formation->icon ?? 'fa-book' }}"></i> 
                                                {{ $catalogueFormation->titre }}
                                            </h6>
                                            @if($catalogueFormation->certification)
                                            <span class="badge bg-warning" title="Formation certifiante">
                                                <i class="fas fa-award"></i>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text small">{{ Str::limit($catalogueFormation->description, 100) }}</p>
                                            
                                            <div class="mb-2">
                                                <span class="badge custom-badge-{{ $couleurCategorie }} text-white">
                                                    {{ ucfirst($categorie) }}
                                                </span>
                                                @if($catalogueFormation->duree)
                                                <span class="badge bg-info ms-1">
                                                    {{ $catalogueFormation->duree }}h
                                                </span>
                                                @endif
                                            </div>

                                            <div class="formation-dates">
                                                @if($catalogueFormation->pivot->date_debut)
                                                <p class="mb-1 small">
                                                    <strong>Début:</strong> 
                                                    {{ \Carbon\Carbon::parse($catalogueFormation->pivot->date_debut)->format('d/m/Y') }}
                                                </p>
                                                @endif
                                                @if($catalogueFormation->pivot->date_fin)
                                                <p class="mb-1 small">
                                                    <strong>Fin prévue:</strong> 
                                                    {{ \Carbon\Carbon::parse($catalogueFormation->pivot->date_fin)->format('d/m/Y') }}
                                                </p>
                                                @endif
                                                @if($catalogueFormation->pivot->date_inscription)
                                                <p class="mb-0 small">
                                                    <strong>Inscrit le:</strong> 
                                                    {{ \Carbon\Carbon::parse($catalogueFormation->pivot->date_inscription)->format('d/m/Y') }}
                                                </p>
                                                @endif
                                            </div>

                                            @if($catalogueFormation->prerequis)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <strong>Prérequis:</strong> {{ Str::limit($catalogueFormation->prerequis, 50) }}
                                                </small>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Statistiques de progression -->
            @if(isset($statistiques))
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box bg-success text-white p-3 rounded text-center">
                        <div class="info-box-content">
                            <span class="info-box-text">Progression</span>
                            <span class="info-box-number display-6">{{ $statistiques['progression_moyenne'] ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-info text-white p-3 rounded text-center">
                        <div class="info-box-content">
                            <span class="info-box-text">Quiz Complétés</span>
                            <span class="info-box-number display-6">{{ $statistiques['quiz_completes'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-warning text-white p-3 rounded text-center">
                        <div class="info-box-content">
                            <span class="info-box-text">Vidéos Regardées</span>
                            <span class="info-box-number display-6">{{ $statistiques['videos_regardees'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-danger text-white p-3 rounded text-center">
                        <div class="info-box-content">
                            <span class="info-box-text">Meilleur Rang</span>
                            <span class="info-box-number display-6">#{{ $statistiques['meilleur_rang'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Contacts et relations -->
            <div class="row">
                <!-- Formateurs assignés -->
                @if($stagiaire->formateurs->count() > 0)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-chalkboard-teacher"></i> Formateurs Assignés
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($stagiaire->formateurs as $formateur)
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $formateur->prenom }} {{ $formateur->nom }}</h6>
                                    <p class="mb-0 small text-muted">{{ $formateur->role ?? 'Formateur' }}</p>
                                    <p class="mb-0 small">{{ $formateur->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Commercials assignés -->
                @if($stagiaire->commercials->count() > 0)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-user-tie"></i> Commerciaux Assignés
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($stagiaire->commercials as $commercial)
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $commercial->prenom }} {{ $commercial->nom }}</h6>
                                    <p class="mb-0 small text-muted">Commercial</p>
                                    <p class="mb-0 small">{{ $commercial->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
/* Styles pour la catégorie Bureautique */
.custom-card-bureautique {
    border: 2px solid #3D9BE9;
    transition: transform 0.2s;
}
.custom-card-bureautique:hover {
    transform: translateY(-2px);
}
.custom-header-bureautique {
    background-color: #3D9BE9 !important;
}
.custom-badge-bureautique {
    background-color: #3D9BE9 !important;
}

/* Styles pour la catégorie Langues */
.custom-card-langues {
    border: 2px solid #A55E6E;
    transition: transform 0.2s;
}
.custom-card-langues:hover {
    transform: translateY(-2px);
}
.custom-header-langues {
    background-color: #A55E6E !important;
}
.custom-badge-langues {
    background-color: #A55E6E !important;
}

/* Styles pour la catégorie Internet */
.custom-card-internet {
    border: 2px solid #FFC533;
    transition: transform 0.2s;
}
.custom-card-internet:hover {
    transform: translateY(-2px);
}
.custom-header-internet {
    background-color: #FFC533 !important;
}
.custom-badge-internet {
    background-color: #FFC533 !important;
    color: #000 !important;
}

/* Styles pour la catégorie Création */
.custom-card-creation {
    border: 2px solid #9392BE;
    transition: transform 0.2s;
}
.custom-card-creation:hover {
    transform: translateY(-2px);
}
.custom-header-creation {
    background-color: #9392BE !important;
}
.custom-badge-creation {
    background-color: #9392BE !important;
}

/* Styles par défaut */
.custom-card-secondary {
    border: 2px solid #6c757d;
}
.custom-header-secondary {
    background-color: #6c757d !important;
}
.custom-badge-secondary {
    background-color: #6c757d !important;
}

/* Améliorations visuelles */
.formation-dates {
    background-color: #f8f9fa;
    padding: 8px;
    border-radius: 4px;
    margin: 10px 0;
}
.info-box {
    transition: transform 0.2s;
}
.info-box:hover {
    transform: translateY(-2px);
}
</style>
@endsection