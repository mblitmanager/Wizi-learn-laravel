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
                                    <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">
                                        Stagiaires ayant utilisé l'application
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

                <!-- Statistiques -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box px-2 py-2" style="background: #75c988; color: white;">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Utilisateurs actifs</span>
                                <span class="info-box-number">{{ $stats['total_avec_app'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box px-2 py-2" style="background: #65baee; color: white;">
                            <span class="info-box-icon"><i class="fas fa-question-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Quiz complétés</span>
                                <span class="info-box-number">{{ $stats['quiz_completes'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box px-2 py-2" style="background: #f39c12; color: white;">
                            <span class="info-box-icon"><i class="fas fa-film"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Vidées regardées</span>
                                <span class="info-box-number">{{ $stats['videos_regardees'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box px-2 py-2" style="background: #e74c3c; color: white;">
                            <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Progression moyenne</span>
                                <span class="info-box-number">{{ $stats['progression_moyenne'] }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-mobile-alt"></i> Stagiaires Actifs sur l'Application
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($stagiairesAvecApp->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Stagiaire</th>
                                            <th>Dernière activité</th>
                                            <th>Quiz complétés</th>
                                            <th>Vidéos regardées</th>
                                            <th>Progression</th>
                                            <th>Points total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($stagiairesAvecApp as $stagiaire)
                                            @php
                                                $derniereActivite = $stagiaire->derniere_activite;
                                                $totalPoints = $stagiaire->classements->sum('points');
                                                $quizCompletes = $stagiaire->quizParticipations
                                                    ->where('status', 'completed')
                                                    ->count();
                                                $videosRegardees = $stagiaire->watchedVideos->count();

                                                // Utiliser la progression calculée dans le contrôleur
                                                $progressionMoyenne = $stagiaire->progression_calculee ?? 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $stagiaire->civilite }} {{ $stagiaire->prenom }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $stagiaire->user->email ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    @if ($derniereActivite)
                                                        <span
                                                            class="badge bg-info">{{ $derniereActivite->diffForHumans() }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inconnue</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">{{ $quizCompletes }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning">{{ $videosRegardees }}</span>
                                                </td>
                                                <td>
                                                    @if ($progressionMoyenne > 0)
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" role="progressbar"
                                                                style="width: {{ $progressionMoyenne }}%;"
                                                                aria-valuenow="{{ $progressionMoyenne }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                                {{ number_format($progressionMoyenne, 1) }}%
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $quizCompletes }} quiz complété(s)
                                                        </small>
                                                    @else
                                                        <span class="badge bg-secondary">Aucune progression</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success fs-6">{{ $totalPoints }} pts</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('formateur.stagiaires.details-classement', $stagiaire->id) }}"
                                                        class="btn btn-sm btn-primary" title="Détails activité">
                                                        <i class="fas fa-chart-bar"></i>
                                                    </a>
                                                    <a href="{{ route('formateur.stagiaires.show', $stagiaire->id) }}"
                                                        class="btn btn-sm btn-info" title="Profil">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $stagiairesAvecApp->links() }}
                            </div>
                        @else
                            <div class="alert alert-warning text-center">
                                <h5>Aucun stagiaire n'a utilisé l'application</h5>
                                <p>Vos stagiaires n'ont pas encore d'activité enregistrée sur l'application.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
