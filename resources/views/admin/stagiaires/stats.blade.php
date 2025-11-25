@extends('admin.layout')
@section('title', 'Statistiques stagiaires')
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
                                    Statistiques stagiaires
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="bx bx-user me-1"></i> {{ $stagiaires->total() }} stagiaires
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
                            <i class="bx bx-stats me-2"></i>Statistiques d'activité des stagiaires
                        </h6>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex flex-wrap justify-content-md-end gap-2">
                            <!-- Boutons d'export -->
                            <a href="{{ route('admin.stagiaires.stats.export', request()->all()) }}"
                                class="btn btn-success btn-sm">
                                <i class="bx bx-download me-1"></i> Exporter CSV
                            </a>
                            <a href="{{ route('admin.stagiaires.stats.export.xlsx', request()->all()) }}"
                                class="btn btn-outline-success btn-sm">
                                <i class="bx bx-file me-1"></i> Exporter XLSX
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Filtres principaux -->
                <form method="GET" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm" placeholder="Nom, email, téléphone...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Plateforme</label>
                        <select class="form-select form-select-sm" name="platform">
                            <option value="">Toutes</option>
                            <option value="android" @selected(request('platform') === 'android')>Android</option>
                            <option value="ios" @selected(request('platform') === 'ios')>iOS</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Inactifs</label>
                        <select class="form-select form-select-sm" name="inactive_days">
                            <option value="">Tous</option>
                            <option value="3" @selected(request('inactive_days') === '3')>>= 3 jours</option>
                            <option value="7" @selected(request('inactive_days') === '7')>>= 7 jours</option>
                            <option value="30" @selected(request('inactive_days') === '30')>>= 30 jours</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Formateur</label>
                        <select class="form-select form-select-sm" name="formateur_id">
                            <option value="">Tous</option>
                            @foreach ($formateurs as $f)
                                <option value="{{ $f->id }}" @selected((string) $f->id === (string) request('formateur_id'))>
                                    {{ $f->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small">Partenaire</label>
                        <select class="form-select form-select-sm" name="partenaire_id">
                            <option value="">Tous</option>
                            @foreach ($partenaires as $p)
                                <option value="{{ $p->id }}" @selected((string) $p->id === (string) request('partenaire_id'))>
                                    {{ $p->identifiant }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-sm w-100" type="submit">
                            <i class="bx bx-filter-alt me-1"></i> Filtrer
                        </button>
                    </div>
                </form>

                <!-- Filtres dates -->
                <form method="GET" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Dernière connexion (de)</label>
                        <input type="datetime-local" name="last_login_from" value="{{ request('last_login_from') }}"
                            class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Dernière connexion (à)</label>
                        <input type="datetime-local" name="last_login_to" value="{{ request('last_login_to') }}"
                            class="form-control form-control-sm" />
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button class="btn btn-outline-primary btn-sm me-2" type="submit">
                            <i class="bx bx-calendar me-1"></i> Appliquer dates
                        </button>
                        <a href="{{ route('admin.stagiaires.stats') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-reset me-1"></i> Réinitialiser
                        </a>
                    </div>
                </form>

                <!-- Cartes statistiques -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-muted mb-2">Inactifs ≥ 3 jours</h6>
                                        <p class="card-text display-6 fw-bold text-info">{{ $inactive3 }}</p>
                                        <small class="text-muted">Stagiaires non actifs</small>
                                    </div>
                                    <div class="bg-info bg-opacity-10 p-3 rounded">
                                        <i class="bx bx-time-five fs-1 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-muted mb-2">Inactifs ≥ 7 jours</h6>
                                        <p class="card-text display-6 fw-bold text-primary">{{ $inactive7 }}</p>
                                        <small class="text-muted">Stagiaires inactifs</small>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bx bx-calendar-x fs-1 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-muted mb-2">Inactifs ≥ 30 jours</h6>
                                        <p class="card-text display-6 fw-bold text-danger">{{ $inactive30 }}</p>
                                        <small class="text-muted">Stagiaires très inactifs</small>
                                    </div>
                                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                                        <i class="bx bx-user-minus fs-1 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau -->
                <div class="table-responsive">
                    <table id="statsTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Stagiaire</th>
                                <th class="bg-primary text-white border-0">Email</th>
                                <th class="bg-primary text-white border-0">Dernière connexion</th>
                                <th class="bg-primary text-white border-0">Dernière activité</th>
                                <th class="bg-primary text-white border-0 text-center">Android</th>
                                <th class="bg-primary text-white border-0 text-center">iOS</th>
                                <th class="bg-primary text-white border-0">Dernier quiz</th>
                            </tr>
                            <tr class="filters">
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
                                <th class="border-bottom"></th>
                                <th class="border-bottom"></th>
                                <th class="border-bottom">
                                    <input type="text" placeholder="Filtrer..."
                                        class="form-control form-control-sm border">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stagiaires as $s)
                                <tr>
                                    <td>
                                        <a href="{{ route('stagiaires.show', $s->stagiaire_id) }}"
                                            class="fw-semibold text-dark text-decoration-none">
                                            {{ $s->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $s->email }}</span>
                                    </td>
                                    <td>
                                        @if ($s->last_login_at)
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-medium">
                                                    {{ \Carbon\Carbon::parse($s->last_login_at)->format('d/m/Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($s->last_login_at)->format('H:i') }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($s->last_activity_at)
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-medium">
                                                    {{ \Carbon\Carbon::parse($s->last_activity_at)->format('d/m/Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($s->last_activity_at)->format('H:i') }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($s->has_android)
                                            <span class="badge bg-success">
                                                <i class="bx bxl-android me-1"></i> Oui
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Non</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($s->has_ios)
                                            <span class="badge bg-dark">
                                                <i class="bx bxl-apple me-1"></i> Oui
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Non</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($s->last_quiz_at)
                                            <div class="d-flex flex-column">
                                                <span class="text-dark fw-medium">
                                                    {{ \Carbon\Carbon::parse($s->last_quiz_at)->format('d/m/Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($s->last_quiz_at)->format('H:i') }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="bx bx-user-voice fs-1 text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun stagiaire trouvé</h5>
                                            <p class="text-muted mb-0">Aucun résultat ne correspond à vos critères de
                                                recherche.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($stagiaires->hasPages())
                    <div class="mt-4">
                        <nav aria-label="Pagination">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    Affichage de {{ $stagiaires->firstItem() ?? 0 }} à {{ $stagiaires->lastItem() ?? 0 }}
                                    sur {{ $stagiaires->total() }} stagiaires
                                </div>
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($stagiaires->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="bx bx-chevron-left"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $stagiaires->previousPageUrl() }}"
                                                rel="prev">
                                                <i class="bx bx-chevron-left"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($stagiaires->getUrlRange(1, $stagiaires->lastPage()) as $page => $url)
                                        @if ($page == $stagiaires->currentPage())
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
                                    @if ($stagiaires->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $stagiaires->nextPageUrl() }}" rel="next">
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
            // Initialisation des filtres DataTable
            $('input[placeholder="Filtrer..."]').on('keyup', function() {
                const columnIndex = $(this).closest('th').index();
                const value = $(this).val().toLowerCase();

                $('#statsTable tbody tr').filter(function() {
                    const cellText = $(this).find('td').eq(columnIndex).text().toLowerCase();
                    $(this).toggle(cellText.indexOf(value) > -1);
                });
            });

            // Confirmation d'export
            $('a[href*="export"]').on('click', function(e) {
                if (!confirm('Voulez-vous exporter les données avec les filtres actuels ?')) {
                    e.preventDefault();
                }
            });

            // Mise à jour automatique des statistiques (optionnel)
            function updateStats() {
                // Ici vous pourriez ajouter une mise à jour AJAX des compteurs
                console.log('Mise à jour des statistiques...');
            }

            // Actualisation toutes les 2 minutes
            setInterval(updateStats, 120000);
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

        a.text-decoration-none:hover {
            text-decoration: underline !important;
        }
    </style>
@endsection
