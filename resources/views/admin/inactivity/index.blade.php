@extends('admin.layout')

@section('content')
    <div class="container py-3">
        <h3>Inactivité</h3>

        <form method="GET" class="row g-2 align-items-end mb-3">
            <div class="col-md-2">
                <label class="form-label">Période</label>
                <select name="days" class="form-select">
                    <option value="3" @selected(request('days') == '3')>>= 3 jours</option>
                    <option value="7" @selected(request('days') == '7')>>= 7 jours</option>
                    <option value="30" @selected(request('days') == '30')>>= 30 jours</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Formateur</label>
                <select class="form-select" name="formateur_id">
                    <option value="">Tous</option>
                    @foreach ($formateurs as $f)
                        <option value="{{ $f->id }}" @selected((string) $f->id === (string) request('formateur_id'))>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Partenaire</label>
                <select class="form-select" name="partenaire_id">
                    <option value="">Tous</option>
                    @foreach ($partenaires as $p)
                        <option value="{{ $p->id }}" @selected((string) $p->id === (string) request('partenaire_id'))>{{ $p->identifiant }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Onglet</label>
                <div class="btn-group d-flex" role="group">
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'quiz']) }}"
                        class="btn btn-outline-primary {{ request('tab', 'quiz') === 'quiz' ? 'active' : '' }}">Inactifs
                        quiz</a>
                    <a href="{{ request()->fullUrlWithQuery(['tab' => 'platform']) }}"
                        class="btn btn-outline-primary {{ request('tab') === 'platform' ? 'active' : '' }}">Plateformes</a>
                </div>
            </div>
        </form>

        @if (request('tab', 'quiz') === 'platform')
            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label">Plateforme</label>
                    <select class="form-select" name="platform">
                        <option value="">Toutes</option>
                        <option value="android" @selected(request('platform') === 'android')>Android</option>
                        <option value="ios" @selected(request('platform') === 'ios')>iOS</option>
                        <option value="web" @selected(request('platform') === 'web')>Web</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Appliquer</button>
                </div>
            </form>
        @endif

        <form method="POST" action="{{ route('admin.inactivity.notify') }}">
            @csrf
            <div class="mb-2 d-flex gap-2">
                <input type="text" name="message" class="form-control" placeholder="Message de notification" required />
                <button type="submit" class="btn btn-success">Envoyer notification</button>
            </div>

            <div class="table-responsive bg-white shadow-sm rounded">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox"
                                    onclick="document.querySelectorAll('.chk-user').forEach(c=>c.checked=this.checked)" />
                            </th>
                            <th>Stagiaire</th>
                            <th>Email</th>
                            <th>Dernière connexion</th>
                            <th>Dernière activité</th>
                            <th>Dernier quiz</th>
                            <th>Dernière vidéo</th>
                            <th>Android</th>
                            <th>iOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stagiaires as $s)
                            <tr>
                                <td><input type="checkbox" class="chk-user" name="user_ids[]" value="{{ $s->user_id }}" />
                                </td>
                                <td><a href="{{ route('stagiaires.show', $s->stagiaire_id) }}">{{ $s->name }}</a>
                                </td>
                                <td>{{ $s->email }}</td>
                                <td>{{ $s->last_login_at ? \Carbon\Carbon::parse($s->last_login_at)->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td>{{ $s->last_activity_at ? \Carbon\Carbon::parse($s->last_activity_at)->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td>{{ $s->last_quiz_at ? \Carbon\Carbon::parse($s->last_quiz_at)->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td>{{ $s->last_video_at ? \Carbon\Carbon::parse($s->last_video_at)->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td>{!! $s->has_android
                                    ? '<span class="badge bg-success">Oui</span>'
                                    : '<span class="badge bg-secondary">Non</span>' !!}</td>
                                <td>{!! $s->has_ios ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Aucun résultat</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $stagiaires->links() }}</div>
        </form>
    </div>
@endsection
