@extends('admin.layout')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-trophy me-2"></i> Classements par partenaire</h2>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Liste des partenaires</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Logo</th>
                                <th>Partenaire</th>
                                <th class="text-center">Nombre de stagiaires</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partenaires as $partenaire)
                                <tr>
                                    <td>
                                        @if ($partenaire->logo)
                                            <img src="{{ asset('storage/' . $partenaire->logo) }}"
                                                alt="{{ $partenaire->identifiant }}" class="rounded-circle" width="40">
                                        @else
                                            <div class="avatar-initials bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                {{ substr($partenaire->identifiant, 0, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $partenaire->identifiant }}</strong><br>
                                        <small class="text-muted">{{ $partenaire->ville }}
                                            ({{ $partenaire->departement }})</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill">{{ $partenaire->stagiaires_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('classements.show', $partenaire->id) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-chart-line me-1"></i> Voir classement
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Aucun partenaire disponible</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .avatar-initials {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .table th {
            font-weight: 600;
        }
    </style>
@endpush
