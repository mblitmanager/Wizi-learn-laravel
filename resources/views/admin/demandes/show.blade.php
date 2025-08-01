@extends('admin.layout')
@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Détails de la demande #{{ $demande->id }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Informations générales</h5>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Type :</dt>
                                    <dd class="col-sm-8">
                                        <span
                                            class="badge {{ str_contains($demande->motif, 'parrainage') ? 'bg-purple' : 'bg-primary' }}">
                                            {{ $demande->motif }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-4">Statut :</dt>
                                    <dd class="col-sm-8">
                                        <span
                                            class="badge {{ $demande->statut === 'complete' ? 'bg-success' : ($demande->statut === 'annulee' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ ucfirst($demande->statut) }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-4">Date de demande :</dt>
                                    <dd class="col-sm-8">
                                        {{ optional($demande->date_demande)->format('d/m/Y H:i') ?? 'Non spécifiée' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Participants</h5>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Formation :</dt>
                                    <dd class="col-sm-8">{{ $demande->formation->titre ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4">Parrain :</dt>
                                    <dd class="col-sm-8">{{ $demande->parrain->name ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4">Filleul :</dt>
                                    <dd class="col-sm-8">{{ $demande->filleul->name ?? 'N/A' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Données du formulaire</h5>
                    </div>
                    <div class="card-body p-0">
                        @if (!empty($demande->donnees_formulaire))
                            @php
                                $donnees = is_array($demande->donnees_formulaire)
                                    ? $demande->donnees_formulaire
                                    : json_decode($demande->donnees_formulaire, true);
                            @endphp

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Champ</th>
                                            <th>Valeur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($donnees as $key => $value)
                                            <tr>
                                                <td class="font-weight-bold">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                                <td>
                                                    @if (is_array($value))
                                                        {{ json_encode($value, JSON_PRETTY_PRINT) }}
                                                    @else
                                                        {{ $value ?? 'N/A' }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                Aucune donnée de formulaire disponible
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <a href="{{ route('demande.historique.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-purple {
            background-color: #6f42c1;
        }

        dl.row dt {
            font-weight: normal;
            color: #6c757d;
        }

        dl.row dd {
            font-weight: 500;
        }
    </style>
@endsection
