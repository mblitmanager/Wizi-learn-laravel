@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class='bx bx-bar-chart-alt-2 me-2'></i>Statistiques d'Affluence
                </h1>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Période</th>
                                <th class="text-center">Participations</th>
                                <th class="text-center">Utilisateurs Uniques</th>
                                <th class="text-center">Score Moyen</th>
                                <th class="text-center">Score Max</th>
                                <th class="text-center">Score Min</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($affluenceData as $stat)
                                <tr>
                                    <td class="fw-medium">{{ $stat->period }}</td>
                                    <td class="text-center">{{ $stat->total_participations }}</td>
                                    <td class="text-center">{{ $stat->unique_users }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ round($stat->avg_score, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ round($stat->max_score, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ round($stat->min_score, 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Aucune donnée disponible
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
