{{-- resources/views/formateur/stagiaires/classement.blade.php --}}
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
                                        Classement des Stagiaires
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

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0 text-white">
                            <i class="fas fa-trophy"></i> Classement Général
                            <small class="ms-2">(Basé sur les scores aux quiz)</small>
                        </h5>
                    </div>

                    <div class="card-body">
                        @if ($classementAvecRang && $classementAvecRang->count() > 0)
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i>
                                Classement basé sur le total des points obtenus dans tous les quiz complétés.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="80">Rang</th>
                                            <th>Stagiaire</th>
                                            <th width="120">Points Total</th>
                                            <th width="120">Quiz Complétés</th>
                                            <th width="100">Statut App</th>
                                            <th width="100">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $previousPoints = null;
                                            $previousRang = null;
                                        @endphp

                                        @foreach ($classementAvecRang as $item)
                                            @php
                                                $stagiaire = $item['stagiaire'];

                                                // Déterminer la médaille ou l'affichage du rang
                                            if ($item['rang_global'] <= 3) {
                                                $medaille = match ($item['rang_global']) {
                                                    1 => '🥇',
                                                    2 => '🥈',
                                                    3 => '🥉',
                                                    default => $item['rang_global'],
                                                };
                                            } else {
                                                $medaille = '#' . $item['rang_global'];
                                            }

                                            // Vérifier si c'est un ex-aequo avec le précédent
                                                $isExAequo =
                                                    $previousPoints !== null &&
                                                    $previousPoints == $item['total_points'];
                                                $rowClass = '';

                                                if ($item['rang_global'] <= 3) {
                                                    $rowClass = 'table-warning';
                                                } elseif ($isExAequo) {
                                                    $rowClass = 'table-info';
                                                }

                                                $previousPoints = $item['total_points'];
                                                $previousRang = $item['rang_global'];
                                            @endphp

                                            <tr class="{{ $rowClass }}">
                                                <td class="text-center fw-bold">
                                                    <span style="font-size: 1.2em">
                                                        {{ $medaille }}
                                                        @if ($isExAequo)
                                                            <small class="text-muted d-block"
                                                                style="font-size: 0.7em">ex-aequo</small>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-50px me-3">
                                                            <div class="symbol-label bg-light-primary">
                                                                <span class="text-primary fw-bold fs-4">
                                                                    {{ substr($stagiaire->prenom, 0, 1) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $stagiaire->civilite }}
                                                                {{ $stagiaire->prenom }}</strong>
                                                            <br>
                                                            <small
                                                                class="text-muted">{{ $stagiaire->user->email ?? 'N/A' }}</small>
                                                            @if ($stagiaire->date_fin_formation && \Carbon\Carbon::now()->gt($stagiaire->date_fin_formation))
                                                                <br>
                                                                <small class="text-danger">
                                                                    <i class="fas fa-graduation-cap"></i> Formation terminée
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-success fs-6">{{ number_format($item['total_points'], 0) }}
                                                        pts</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">{{ $item['quiz_completes'] }} quiz</span>
                                                </td>
                                            
                                                <td class="text-center">
                                                    @if ($item['a_utilise_app'])
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> Utilisé
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-times"></i> Non utilisé
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('formateur.stagiaires.details-classement', $stagiaire->id) }}"
                                                        class="btn btn-sm btn-primary" title="Détails du classement">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                    <a href="{{ route('formateur.stagiaires.show', $stagiaire->id) }}"
                                                        class="btn btn-sm btn-primary" title="Profil complet">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 text-muted small">
                                <i class="fas fa-info-circle"></i>
                                <strong>Légende :</strong>
                                <span class="ms-2">🥇🥈🥉 = Podium</span>
                                <span class="ms-2">•</span>
                                <span class="ms-2">Bleu = Ex-aequo</span>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <h5><i class="fas fa-trophy"></i> Aucun classement disponible</h5>
                                <p class="mb-3">Vos stagiaires n'ont pas encore complété de quiz évalués.</p>
                                <a href="{{ route('formateur.stagiaires.application') }}" class="btn btn-primary">
                                    Voir les stagiaires ayant utilisé l'application
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-warning {
            background-color: #fff3cd !important;
        }

        .table-info {
            background-color: #d1ecf1 !important;
        }

        .symbol-label {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
@endsection
