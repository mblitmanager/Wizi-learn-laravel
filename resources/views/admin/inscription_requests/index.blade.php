@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Demandes d'inscription à une formation</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="GET" class="row g-2 mb-3">
        <div class="col">
            <input type="text" name="stagiaire" class="form-control" placeholder="Stagiaire" value="{{ request('stagiaire') }}">
        </div>
        <div class="col">
            <input type="text" name="formation" class="form-control" placeholder="Formation" value="{{ request('formation') }}">
        </div>
        <div class="col">
            <select name="status" class="form-select">
                <option value="">Tous statuts</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>En attente</option>
                <option value="accepted" {{ request('status')=='accepted'?'selected':'' }}>Accepté</option>
                <option value="refused" {{ request('status')=='refused'?'selected':'' }}>Refusé</option>
            </select>
        </div>
        <div class="col">
            <button class="btn btn-primary" type="submit">Rechercher</button>
        </div>
        <div class="col text-end">
            <a href="{{ route('admin.inscription_requests.export') }}" class="btn btn-success">Exporter CSV</a>
        </div>
    </form>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'stagiaire_id','dir'=>request('dir')=='asc'?'desc':'asc']) }}">Stagiaire</a></th>
                <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'catalogue_formation_id','dir'=>request('dir')=='asc'?'desc':'asc']) }}">Formation souhaitée</a></th>
                <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'status','dir'=>request('dir')=='asc'?'desc':'asc']) }}">Statut</a></th>
                <th><a href="{{ request()->fullUrlWithQuery(['sort'=>'created_at','dir'=>request('dir')=='asc'?'desc':'asc']) }}">Date</a></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @forelse($requests as $req)
            <tr>
                <td>{{ $req->id }}</td>
                <td>{{ $req->stagiaire ? $req->stagiaire->prenom : '-' }}</td>
                <td>{{ $req->catalogueFormation ? $req->catalogueFormation->titre : '-' }}</td>
                <td>
                    <span class="badge bg-{{ $req->status == 'accepted' ? 'success' : ($req->status == 'refused' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($req->status) }}
                    </span>
                </td>
                <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.inscription_requests.updateStatus', $req->id) }}">
                        @csrf
                        <select name="status" class="form-select d-inline w-auto">
                            <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="accepted" {{ $req->status == 'accepted' ? 'selected' : '' }}>Accepté</option>
                            <option value="refused" {{ $req->status == 'refused' ? 'selected' : '' }}>Refusé</option>
                        </select>
                        <button class="btn btn-primary btn-sm" type="submit">Valider</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center">Aucune demande</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $requests->links() }}
</div>
@endsection 