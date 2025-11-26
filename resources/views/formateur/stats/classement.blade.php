@extends('admin.layout')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class='bx bx-medal me-2'></i>Classement des Stagiaires
                </h1>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">Rang</th>
                                <th>Stagiaire</th>
                                <th class="text-center">Quiz Réalisés</th>
                                <th class="text-center">Score Total</th>
                                <th class="text-center">Score Moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classement as $item)
                                <tr>
                                    <td class="text-center">
                                        @if($item['rank'] == 1)
                                            <i class='bx bxs-medal fs-5' style="color: #FFD700;"></i>
                                        @elseif($item['rank'] == 2)
                                            <i class='bx bxs-medal fs-5' style="color: #C0C0C0;"></i>
                                        @elseif($item['rank'] == 3)
                                            <i class='bx bxs-medal fs-5' style="color: #CD7F32;"></i>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $item['rank'] }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $item['name'] }}</td>
                                    <td class="text-center">{{ $item['total_quizzes'] }}</td>
                                    <td class="text-center">{{ $item['total_score'] }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $item['avg_score'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Aucun stagiaire
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
