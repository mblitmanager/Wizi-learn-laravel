@extends('admin.layout')
@section('title', 'Usages des applications mobiles')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Usages des applications mobiles
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="bx bx-mobile-alt me-1"></i> {{ $usages->total() }} usages
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte principale -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-dark fw-semibold">
                            <i class="bx bx-stats me-2"></i>Statistiques d'utilisation
                        </h6>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <!-- Boutons d'export -->
                            <a href="{{ route('admin.user_app_usages.export', ['platform' => request('platform')]) }}"
                                class="btn btn-success btn-sm">
                                <i class="bx bx-download me-1"></i> Exporter CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Filtres -->
                <form method="GET" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label for="platform" class="form-label fw-semibold small">Plateforme</label>
                        <select name="platform" id="platform" class="form-select form-select-sm">
                            <option value="">Toutes plateformes</option>
                            <option value="android" {{ request('platform') === 'android' ? 'selected' : '' }}>Android
                            </option>
                            <option value="ios" {{ request('platform') === 'ios' ? 'selected' : '' }}>iOS</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-sm w-100" type="submit">
                            <i class="bx bx-filter-alt me-1"></i> Filtrer
                        </button>
                    </div>
                    <div class="col-md-7 text-md-end">
                        <div class="text-muted small">
                            Affichage de {{ $usages->firstItem() ?? 0 }} à {{ $usages->lastItem() ?? 0 }} sur
                            {{ $usages->total() }} résultats
                        </div>
                    </div>
                </form>

                <!-- Tableau -->
                <div class="table-responsive">
                    <table id="usagesTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Utilisateur</th>
                                <th class="bg-primary text-white border-0 text-center">Plateforme</th>
                                <th class="bg-primary text-white border-0">Première utilisation</th>
                                <th class="bg-primary text-white border-0">Dernière utilisation</th>
                                <th class="bg-primary text-white border-0">Version app</th>
                                <th class="bg-primary text-white border-0">Modèle</th>
                                <th class="bg-primary text-white border-0">OS</th>
                            </tr>
                            <tr class="filters">
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom"></th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usages as $usage)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark">
                                                {{ $usage->user?->name ?? '—' }}
                                            </span>
                                            <small class="text-muted">
                                                ID: {{ $usage->user_id }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($usage->platform === 'android')
                                            <span class="badge bg-success">
                                                <i class="bx bxl-android me-1"></i>ANDROID
                                            </span>
                                        @elseif($usage->platform === 'ios')
                                            <span class="badge bg-dark">
                                                <i class="bx bxl-apple me-1"></i>iOS
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-uppercase">
                                                {{ $usage->platform }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($usage->first_used_at)
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-medium">
                                                    {{ $usage->first_used_at->format('d/m/Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $usage->first_used_at->format('H:i') }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($usage->last_used_at)
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-medium">
                                                    {{ $usage->last_used_at->format('d/m/Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $usage->last_used_at->format('H:i') }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($usage->app_version)
                                            <span class="badge bg-info text-dark">
                                                v{{ $usage->app_version }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-dark">
                                            {{ $usage->device_model ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($usage->os_version)
                                            <span class="badge bg-light text-dark border">
                                                {{ $usage->os_version }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="bx bx-mobile-off fs-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun usage trouvé</h5>
                                            <p class="text-muted mb-0">Aucune donnée d'utilisation d'application mobile n'a
                                                été enregistrée.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($usages->hasPages())
                    <div class="mt-4">
                        <nav aria-label="Pagination">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Page {{ $usages->currentPage() }} sur {{ $usages->lastPage() }}
                                </div>
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($usages->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="bx bx-chevron-left"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $usages->previousPageUrl() }}" rel="prev">
                                                <i class="bx bx-chevron-left"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($usages->getUrlRange(1, $usages->lastPage()) as $page => $url)
                                        @if ($page == $usages->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($usages->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $usages->nextPageUrl() }}" rel="next">
                                                <i class="bx bx-chevron-right"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="bx bx-chevron-right"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialisation des filtres DataTable (optionnel)
            $('input[placeholder="Filtrer..."]').on('keyup', function() {
                const columnIndex = $(this).closest('th').index();
                const value = $(this).val().toLowerCase();

                $('#usagesTable tbody tr').filter(function() {
                    const cellText = $(this).find('td').eq(columnIndex).text().toLowerCase();
                    $(this).toggle(cellText.indexOf(value) > -1);
                });
            });

            // Reset des filtres
            $('#resetFilters').on('click', function() {
                $('input[placeholder="Filtrer..."]').val('').trigger('keyup');
                $('#platform').val('');
            });

            // Confirmation d'export
            $('a[href*="export"]').on('click', function(e) {
                const platform = $('#platform').val();
                const platformText = platform ? ` pour ${platform.toUpperCase()}` : '';

                if (!confirm(`Voulez-vous exporter les données${platformText} ?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .card {
            border-radius: 12px;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .badge {
            border-radius: 4px;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
        }

        .pagination .page-link {
            border-radius: 4px;
            margin: 0 2px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.04);
        }
    </style>
@endsection
