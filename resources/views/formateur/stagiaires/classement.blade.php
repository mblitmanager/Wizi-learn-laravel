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
                    <h3 class="card-title mb-0 text-white">
                        <i class="fas fa-trophy"></i> Classement Général
                    </h3>
                </div>
                <div class="card-body">
                    @if(count($classementAvecRang) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="80">Rang</th>
                                        <th>Stagiaire</th>
                                        <th width="120">Points Total</th>
                                        <th width="120">Quiz Complétés</th>
                                        <th width="120">Meilleur Rang</th>
                                        <th width="100">Statut App</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classementAvecRang as $item)
                                        @php
                                            $stagiaire = $item['stagiaire'];
                                            $medaille = match($item['rang_global']) {
                                                1 => '🥇',
                                                2 => '🥈', 
                                                3 => '🥉',
                                                default => $item['rang_global']
                                            };
                                        @endphp
                                        <tr class="@if($item['rang_global'] <= 3) table-warning @endif">
                                            <td class="text-center fw-bold">
                                                <span style="font-size: 1.2em">{{ $medaille }}</span>
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
                                                        <strong>{{ $stagiaire->civilite }} {{ $stagiaire->prenom }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $stagiaire->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success fs-6">{{ $item['total_points'] }} pts</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $item['quiz_completes'] }} quiz</span>
                                            </td>
                                            <td class="text-center">
                                                @if($item['meilleur_rang'])
                                                    <span class="badge bg-warning">#{{ $item['meilleur_rang'] }}</span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item['a_utilise_app'])
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
                                                {{-- <a href="{{ route('formateur.formateur.stagiaires.show', $stagiaire->id) }}" 
                                                   class="btn btn-sm btn-info" title="Profil complet">
                                                    <i class="fas fa-eye"></i>
                                                </a> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5>Aucun classement disponible</h5>
                            <p>Vos stagiaires n'ont pas encore participé à des quiz évalués.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection