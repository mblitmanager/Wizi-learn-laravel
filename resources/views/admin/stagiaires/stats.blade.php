@extends('admin.layout')

@section('content')
    <div class="container-fluid py-3">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Statistiques
                                stagiaires
                            </li>
                        </ol>
                    </nav>
                </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
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
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Filtrer</button>
                        </div>
                    </form>
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Formateur</label>
                            <select class="form-select" name="formateur_id" form="">
                                <option value="">Tous</option>
                                @foreach ($formateurs as $f)
                                    <option value="{{ $f->id }}" @selected((string) $f->id === (string) request('formateur_id'))>{{ $f->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Partenaire</label>
                            <select class="form-select" name="partenaire_id" form="">
                                <option value="">Tous</option>
                                @foreach ($partenaires as $p)
                                    <option value="{{ $p->id }}" @selected((string) $p->id === (string) request('partenaire_id'))>{{ $p->identifiant }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dernière connexion (de)</label>
                            <input type="datetime-local" name="last_login_from" value="{{ request('last_login_from') }}"
                                class="form-control" form="" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dernière connexion (à)</label>
                            <input type="datetime-local" name="last_login_to" value="{{ request('last_login_to') }}"
                                class="form-control" form="" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('admin.stagiaires.stats.export', request()->all()) }}"
                            class="btn btn-success btn-sm">Exporter
                            CSV</a>
                        <a href="{{ route('admin.stagiaires.stats.export.xlsx', request()->all()) }}"
                            class="btn btn-outline-success btn-sm">Exporter XLSX</a>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <div class="card bg-info text-white rounded">
                                <div class="card-body text-white">
                                    <div class="text-white">Inactifs >= 3 jours</div>
                                    <div class="h4 mb-0 text-white">{{ $inactive3 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card bg-primary text-white rounded">
                                <div class="card-body">
                                    <div class="text-white">Inactifs >= 7 jours</div>
                                    <div class="h4 mb-0 text-white">{{ $inactive7 }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card bg-danger text-white rounded">
                                <div class="card-body">
                                    <div class="text-white">Inactifs >= 30 jours</div>
                                    <div class="h4 mb-0 text-white">{{ $inactive30 }}</div>
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
                                            <a
                                                href="{{ route('stagiaires.show', $s->stagiaire_id) }}">{{ $s->name }}</a>
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
            </div>
        </div>
    </div>
@endsection
