@extends('admin.layout')
@section('title', 'Gestion de l\'inactivité')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
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
                                    Gestion de l'inactivité
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('success'))
            <div class="alert alert-success border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filtres et contrôles -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Période d'inactivité</label>
                        <select name="days" class="form-select">
                            <option value="3" @selected(request('days') == '3')>>= 3 jours</option>
                            <option value="7" @selected(request('days') == '7')>>= 7 jours</option>
                            <option value="30" @selected(request('days') == '30')>>= 30 jours</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Formateur</label>
                        <select class="form-select" name="formateur_id">
                            <option value="">Tous les formateurs</option>
                            @foreach ($formateurs as $f)
                                <option value="{{ $f->id }}" @selected((string) $f->id === (string) request('formateur_id'))>{{ $f->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Partenaire</label>
                        <select class="form-select" name="partenaire_id">
                            <option value="">Tous les partenaires</option>
                            @foreach ($partenaires as $p)
                                <option value="{{ $p->id }}" @selected((string) $p->id === (string) request('partenaire_id'))>{{ $p->identifiant }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Type d'activité</label>
                        <div class="btn-group w-100" role="group">
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'quiz']) }}"
                                class="btn btn-outline-primary {{ request('tab', 'quiz') === 'quiz' ? 'active' : '' }}">
                                <i class="bx bx-task me-1"></i>Inactifs quiz
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['tab' => 'platform']) }}"
                                class="btn btn-outline-primary {{ request('tab') === 'platform' ? 'active' : '' }}">
                                <i class="bx bx-devices me-1"></i>Plateformes
                            </a>
                        </div>
                    </div>

                    @if (request('tab', 'quiz') === 'platform')
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Plateforme</label>
                            <select class="form-select" name="platform">
                                <option value="">Toutes les plateformes</option>
                                <option value="android" @selected(request('platform') === 'android')>Android</option>
                                <option value="ios" @selected(request('platform') === 'ios')>iOS</option>
                                <option value="web" @selected(request('platform') === 'web')>Web</option>
                            </select>
                        </div>
                    @endif

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bx bx-filter-alt me-1"></i>Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Formulaire de notification -->
        <form method="POST" action="{{ route('admin.inactivity.notify') }}">
            @csrf
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0 text-dark fw-semibold">
                        <i class="bx bx-bell me-2"></i>Notification de rappel
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-9">
                            <label class="form-label fw-semibold">Message de notification</label>
                            <input type="text" name="message" class="form-control"
                                placeholder="Saisissez votre message de rappel..." required />
                        </div>
                        <div class="col-md-3">
                            <button type="submit"
                                class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                <i class="bx bx-send me-2"></i>Envoyer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des stagiaires inactifs -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom-0 py-3">
                    <h6 class="mb-0 text-dark fw-semibold">
                        <i class="bx bx-list-ul me-2"></i>Liste des stagiaires inactifs
                        <span class="badge bg-info text-dark ms-2">{{ $stagiaires->count() }}</span>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="inactivityTable" class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="bg-primary text-white border-0" width="50">
                                        <input type="checkbox" id="selectAll" />
                                    </th>
                                    <th class="bg-primary text-white border-0">Stagiaire</th>
                                    <th class="bg-primary text-white border-0">Email</th>
                                    <th class="bg-primary text-white border-0">Dernière connexion</th>
                                    <th class="bg-primary text-white border-0">Dernière activité</th>
                                    <th class="bg-primary text-white border-0">Dernier quiz</th>
                                    <th class="bg-primary text-white border-0">Dernière vidéo</th>
                                    <th class="bg-primary text-white border-0 text-center">Android</th>
                                    <th class="bg-primary text-white border-0 text-center">iOS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stagiaires as $s)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="chk-user" name="user_ids[]"
                                                value="{{ $s->user_id }}" />
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="bx bx-user-circle text-primary fs-5"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0 fw-semibold text-dark">
                                                        <a href="{{ route('stagiaires.show', $s->stagiaire_id) }}"
                                                            class="text-decoration-none text-dark">
                                                            {{ $s->name }}
                                                        </a>
                                                    </h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $s->email }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-semibold {{ $s->last_login_at ? 'text-dark' : 'text-muted' }}">
                                                {{ $s->last_login_at ? \Carbon\Carbon::parse($s->last_login_at)->format('d/m/Y H:i') : '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-semibold {{ $s->last_activity_at ? 'text-dark' : 'text-muted' }}">
                                                {{ $s->last_activity_at ? \Carbon\Carbon::parse($s->last_activity_at)->format('d/m/Y H:i') : '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold {{ $s->last_quiz_at ? 'text-dark' : 'text-muted' }}">
                                                {{ $s->last_quiz_at ? \Carbon\Carbon::parse($s->last_quiz_at)->format('d/m/Y H:i') : '—' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="fw-semibold {{ $s->last_video_at ? 'text-dark' : 'text-muted' }}">
                                                {{ $s->last_video_at ? \Carbon\Carbon::parse($s->last_video_at)->format('d/m/Y H:i') : '—' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {!! $s->has_android
                                                ? '<span class="badge bg-success">Oui</span>'
                                                : '<span class="badge bg-secondary">Non</span>' !!}
                                        </td>
                                        <td class="text-center">
                                            {!! $s->has_ios ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="bx bx-user-x fs-1 mb-3 d-block"></i>
                                            Aucun stagiaire inactif trouvé pour les critères sélectionnés
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($stagiaires->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $stagiaires->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Configuration DataTable
            var table = $('#inactivityTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucun stagiaire inactif trouvé."
                },
                paging: false, // Désactive la pagination DataTable car nous utilisons Laravel pagination
                searching: true,
                ordering: true,
                info: false,
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-sm btn-outline-secondary',
                        text: '<i class="bx bx-copy me-1"></i>Copier'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-outline-primary',
                        text: '<i class="bx bx-file me-1"></i>CSV'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm btn-outline-success',
                        text: '<i class="bx bx-spreadsheet me-1"></i>Excel'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-outline-info',
                        text: '<i class="bx bx-printer me-1"></i>Imprimer'
                    }
                ]
            });

            // Sélection/désélection de tous les utilisateurs
            $('#selectAll').on('click', function() {
                $('.chk-user').prop('checked', this.checked);
            });

            // Désélectionner "Sélectionner tout" si un utilisateur est désélectionné
            $('.chk-user').on('change', function() {
                if (!this.checked) {
                    $('#selectAll').prop('checked', false);
                } else {
                    // Vérifier si tous les utilisateurs sont sélectionnés
                    var allChecked = $('.chk-user:checked').length === $('.chk-user').length;
                    $('#selectAll').prop('checked', allChecked);
                }
            });

            // Style personnalisé pour les boutons DataTable
            $('.dt-buttons .btn').addClass('btn-sm');
        });
    </script>

    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .badge {
            border-radius: 4px;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.04) !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .pagination {
            margin-bottom: 0;
        }
    </style>
@endsection
