@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class='bx bx-book me-2'></i>Statistiques par Formation
                </h1>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Formation</th>
                                <th class="text-center">Catalogue</th>
                                <th class="text-center">Stagiaires</th>
                                <th class="text-center">Quiz</th>
                                <th class="text-center">Participations</th>
                                <th class="text-center">Score Moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statsByFormation as $formation)
                                <tr>
                                    <td class="fw-medium">{{ $formation['name'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $formation['catalogue'] }}</span>
                                    </td>
                                    <td class="text-center">{{ $formation['stagiaires_count'] }}</td>
                                    <td class="text-center">{{ $formation['quizzes_count'] }}</td>
                                    <td class="text-center">{{ $formation['total_participations'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $formation['avg_score'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Aucune formation disponible
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
