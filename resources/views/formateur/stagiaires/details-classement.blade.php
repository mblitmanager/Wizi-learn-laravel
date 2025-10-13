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
                                    <a href="{{ route('formateur.stagiaires.application') }}">Utilisateurs App</a>
                                </li>
                                <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">
                                    Détails de {{ $stagiaire->prenom }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="ms-auto">
                        <div class="btn-group">
                            <a href="{{ route('formateur.stagiaires.application') }}" class="btn btn-sm btn-primary px-4">
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
                            <p class="mb-1"><strong>Email:</strong> {{ $stagiaire->user->email ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Téléphone:</strong> {{ $stagiaire->telephone ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Dernière activité:</strong> 
                                @if($statistiques['derniere_activite'])
                                    {{ $statistiques['derniere_activite']->diffForHumans() }}
                                @else
                                    <span class="text-muted">Aucune activité</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="bg-primary text-white p-3 rounded">
                                <h4 class="mb-0">{{ $statistiques['total_points'] }} points</h4>
                                <small>Total des points</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques principales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="info-box bg-success text-white p-3 rounded text-center">
                        <div class="info-box-content">
                            <span class="info-box-text">Progression</span>
                            <span class="info-box-number display-6">{{ $statistiques['progression_moyenne'] }}%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-info text-white p-3 rounded text-center">
                        <div class="info-box-content">
                            <span class="info-box-text">Quiz Complétés</span>
                            <span class="info-box-number display-6">{{ $statistiques['quiz_completes'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-warning text-white p-3 rounded text-center">
                        <div class="info-box-content">
                            <span class="info-box-text">Vidéos Regardées</span>
                            <span class="info-box-number display-6">{{ $statistiques['videos_regardees'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-box bg-danger text-white p-3 rounded text-center">
                        <div class="info-box-content">
                            <span class="info-box-text">Meilleur Rang</span>
                            <span class="info-box-number display-6">#{{ $statistiques['meilleur_rang'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Détails de progression -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0 text-white">
                                <i class="fas fa-chart-line"></i> Détails de la Progression
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Progression globale:</strong>
                                <div class="progress mt-2" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $statistiques['progression_moyenne'] }}%;" 
                                         aria-valuenow="{{ $statistiques['progression_moyenne'] }}" 
                                         aria-valuemin="0" aria-valuemax="100">
                                        {{ $statistiques['progression_moyenne'] }}%
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border p-2 rounded">
                                        <strong>Score Total</strong>
                                        <div class="fs-5 text-success">{{ $statistiques['score_total'] }} pts</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border p-2 rounded">
                                        <strong>Score Max Possible</strong>
                                        <div class="fs-5 text-info">{{ $statistiques['score_max_possible'] }} pts</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3 text-center">
                                <div class="col-6">
                                    <div class="border p-2 rounded">
                                        <strong>Moyenne par Quiz</strong>
                                        <div class="fs-5 text-warning">{{ $statistiques['moyenne_score'] }} pts</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border p-2 rounded">
                                        <strong>Temps Moyen</strong>
                                        <div class="fs-5 text-primary">{{ $statistiques['temps_moyen_quiz'] }} min</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activités et temps -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h4 class="card-title mb-0 text-white">
                                <i class="fas fa-clock"></i> Temps et Activités
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Temps total passé:</strong>
                                <span class="badge bg-primary fs-6 float-end">
                                    {{ round($statistiques['temps_total_passe'] / 60, 1) }} minutes
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Total des participations:</strong>
                                <span class="badge bg-success fs-6 float-end">
                                    {{ $statistiques['participations_quiz'] }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Quiz complétés:</strong>
                                <span class="badge bg-warning fs-6 float-end">
                                    {{ $statistiques['quiz_completes'] }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Vidéos regardées:</strong>
                                <span class="badge bg-danger fs-6 float-end">
                                    {{ $statistiques['videos_regardees'] }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Points totaux:</strong>
                                <span class="badge bg-success fs-6 float-end">
                                    {{ $statistiques['total_points'] }} pts
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classements détaillés -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0 text-white">
                                <i class="fas fa-trophy"></i> Classements Détailés
                            </h4>
                        </div>
                        <div class="card-body">
                            @if($stagiaire->classements->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Rang</th>
                                                <th>Quiz</th>
                                                <th>Formation</th>
                                                <th>Points</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stagiaire->classements->sortBy('rang') as $classement)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-warning fs-6">#{{ $classement->rang }}</span>
                                                    </td>
                                                    <td>{{ $classement->quiz->titre ?? 'Quiz inconnu' }}</td>
                                                    <td>{{ $classement->quiz->formation->titre ?? 'Formation inconnue' }}</td>
                                                    <td>
                                                        <span class="badge bg-success">{{ $classement->points }} pts</span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ $classement->created_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <p>Aucun classement enregistré pour ce stagiaire.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières activités -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h4 class="card-title mb-0 text-white">
                                <i class="fas fa-history"></i> Dernières Activités
                            </h4>
                        </div>
                        <div class="card-body">
                            @if($stagiaire->quizParticipations->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Quiz</th>
                                                <th>Statut</th>
                                                <th>Score</th>
                                                <th>Date</th>
                                                <th>Temps</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stagiaire->quizParticipations->sortByDesc('created_at')->take(10) as $participation)
                                                <tr>
                                                    <td>{{ $participation->quiz->titre ?? 'Quiz inconnu' }}</td>
                                                    <td>
                                                        <span class="badge {{ $participation->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ $participation->status }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $participation->score }} pts</span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ $participation->created_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ round($participation->time_spent / 60, 1) }} min
                                                        </small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <p>Aucune activité récente.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection