@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Détails du Rôle : {{ $role->name }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('roles.index') }}" type="button" class="btn btn-sm btn-primary px-4"> Retour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
            
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Informations du Rôle</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Nom :</th>
                                    <td>{{ $role->name }}</td>
                                </tr>
                                <tr>
                                    <th>Description :</th>
                                    <td>{{ $role->description ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Statut :</th>
                                    <td>
                                        @if($role->is_protected)
                                            <span class="badge badge-warning">Protégé</span>
                                        @else
                                            <span class="badge badge-{{ $role->is_active ? 'success' : 'danger' }}">
                                                {{ $role->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Créé le :</th>
                                    <td>{{ $role->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Modifié le :</th>
                                    <td>{{ $role->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Utilisateurs associés ({{ $role->users->count() }})</h4>
                            @if($role->users->count() > 0)
                                <div class="list-group">
                                    @foreach($role->users as $user)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                            <span class="badge badge-primary">
                                                {{ $user->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucun utilisateur n'a ce rôle.</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Permissions associées ({{ $role->permissions->count() }})</h4>
                            <div class="row">
                                @foreach($role->permissions->groupBy('group') as $group => $permissions)
                                <div class="col-md-4 mb-3">
                                    <div class="card card-outline card-info">
                                        <div class="card-header">
                                            <h5 class="card-title">{{ $group ?? 'Général' }}</h5>
                                        </div>
                                        <div class="card-body">
                                            @foreach($permissions as $permission)
                                            <div class="mb-2 p-2 border rounded">
                                                <strong class="d-block">{{ $permission->name }}</strong>
                                                @if($permission->description)
                                                <small class="text-muted">{{ $permission->description }}</small>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    @if(!$role->is_protected)
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    @endif
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection