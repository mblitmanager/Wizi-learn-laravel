@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class='bx bx-book-bookmark me-2'></i>Statistiques par Catalogue de Formation
                </h1>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Catalogue</th>
                                <th class="text-center">Formations</th>
                                <th class="text-center">Formateurs</th>
                                <th class="text-center">Stagiaires</th>
                                <th class="text-center">Participations</th>
                                <th class="text-center">Score Moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statsByCatalogue as $catalogue)
                                <tr>
                                    <td class="fw-medium">{{ $catalogue['name'] }}</td>
                                    <td class="text-center">{{ $catalogue['formations_count'] }}</td>
                                    <td class="text-center">{{ $catalogue['formateurs_count'] }}</td>
                                    <td class="text-center">{{ $catalogue['stagiaires_count'] }}</td>
                                    <td class="text-center">{{ $catalogue['total_participations'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $catalogue['avg_score'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Aucun catalogue
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
