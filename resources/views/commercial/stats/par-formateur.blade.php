@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class='bx bx-arrow-back me-2'></i>
                    <a href="{{ route('commercial.dashboard') }}" class="text-decoration-none">Retour</a>
                    | Statistiques par Formateur
                </h1>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Formateur</th>
                                <th class="text-center">Stagiaires Suivis</th>
                                <th class="text-center">Participations</th>
                                <th class="text-center">Score Moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statsParFormateur as $formateur)
                                <tr>
                                    <td class="fw-medium">{{ $formateur['name'] }}</td>
                                    <td class="text-center">{{ $formateur['stagiaires_count'] }}</td>
                                    <td class="text-center">{{ $formateur['total_participations'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $formateur['avg_score'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        Aucun formateur
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
