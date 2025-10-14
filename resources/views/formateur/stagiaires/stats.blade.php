@extends('admin.layout')

@section('content')
    <div class="container py-3">
        <h3>Mes stagiaires - Statistiques</h3>

        <form method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-md-4">
                <label class="form-label">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Nom, email, téléphone...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Plateforme</label>
                <select class="form-select" name="platform">
                    <option value="">Toutes</option>
                    <option value="android" @selected(request('platform') === 'android')>Android</option>
                    <option value="ios" @selected(request('platform') === 'ios')>iOS</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Inactifs</label>
                <select class="form-select" name="inactive_days">
                    <option value="">Tous</option>
                    <option value="3" @selected(request('inactive_days') === '3')>>= 3 jours</option>
                    <option value="7" @selected(request('inactive_days') === '7')>>= 7 jours</option>
                    <option value="30" @selected(request('inactive_days') === '30')>>= 30 jours</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dernière connexion (de)</label>
                <input type="datetime-local" name="last_login_from" value="{{ request('last_login_from') }}"
                    class="form-control" />
            </div>
            <div class="col-md-3">
                <label class="form-label">Dernière connexion (à)</label>
                <input type="datetime-local" name="last_login_to" value="{{ request('last_login_to') }}"
                    class="form-control" />
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Filtrer</button>
            </div>
        </form>
        <div class="mb-3">
            <a href="{{ route('formateur.stagiaires.stats.export', request()->all()) }}"
                class="btn btn-success btn-sm">Exporter CSV</a>
            <a href="{{ route('formateur.stagiaires.stats.export.xlsx', request()->all()) }}"
                class="btn btn-outline-success btn-sm">Exporter XLSX</a>
        </div>

        <div class="row mb-3">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Inactifs >= 3 jours</div>
                        <div class="h4 mb-0">{{ $inactive3 ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Inactifs >= 7 jours</div>
                        <div class="h4 mb-0">{{ $inactive7 ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted">Inactifs >= 30 jours</div>
                        <div class="h4 mb-0">{{ $inactive30 ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive bg-white shadow-sm rounded">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Stagiaire</th>
                        <th>Email</th>
                        <th>Dernière connexion</th>
                        <th>Dernière activité</th>
                        <th>Android</th>
                        <th>iOS</th>
                        <th>Dernier quiz</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stagiaires as $s)
                        <tr>
                            <td>
                                <a href="{{ route('formateur.stagiaires.show', $s->stagiaire_id) }}">{{ $s->name }}</a>
                            </td>
                            <td>{{ $s->email }}</td>
                            <td>{{ $s->last_login_at ? \Carbon\Carbon::parse($s->last_login_at)->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td>{{ $s->last_activity_at ? \Carbon\Carbon::parse($s->last_activity_at)->format('d/m/Y H:i') : '—' }}
                            </td>
                            <td>{!! $s->has_android
                                ? '<span class="badge bg-success">Oui</span>'
                                : '<span class="badge bg-secondary">Non</span>' !!}</td>
                            <td>{!! $s->has_ios ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}</td>
                            <td>{{ $s->last_quiz_at ? \Carbon\Carbon::parse($s->last_quiz_at)->format('d/m/Y H:i') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Aucun résultat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $stagiaires->links() }}</div>
    </div>
@endsection
