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
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Détails de la Permission : {{ $permission->name }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                         <a href="{{ route('permissions.index') }}" class="btn btn-primary float-right">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
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
                            <h4>Informations de la Permission</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Nom :</th>
                                    <td>
                                        <code>{{ $permission->name }}</code>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description :</th>
                                    <td>{{ $permission->description ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Groupe :</th>
                                    <td>
                                        <span class="badge badge-info">{{ $permission->group ?? 'Général' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut :</th>
                                    <td>
                                        <span class="badge badge-{{ $permission->is_active ? 'success' : 'danger' }}">
                                            {{ $permission->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Créé le :</th>
                                    <td>{{ $permission->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Modifié le :</th>
                                    <td>{{ $permission->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Rôles associés ({{ $permission->roles->count() }})</h4>
                            @if($permission->roles->count() > 0)
                                <div class="list-group">
                                    @foreach($permission->roles as $role)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $role->name }}</strong>
                                                @if($role->description)
                                                <br>
                                                <small class="text-muted">{{ $role->description }}</small>
                                                @endif
                                            </div>
                                            <div>
                                                @if($role->is_protected)
                                                    <span class="badge badge-warning">Protégé</span>
                                                @else
                                                    <span class="badge badge-{{ $role->is_active ? 'success' : 'danger' }}">
                                                        {{ $role->is_active ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">Aucun rôle n'a cette permission.</p>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Utilisateurs avec cette permission</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Rôles</th>
                                            <th>Date d'inscription</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $usersWithPermission = collect();
                                            foreach($permission->roles as $role) {
                                                $usersWithPermission = $usersWithPermission->merge($role->users);
                                            }
                                            $usersWithPermission = $usersWithPermission->unique('id');
                                        @endphp

                                        @if($usersWithPermission->count() > 0)
                                            @foreach($usersWithPermission as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @foreach($user->roles as $role)
                                                        <span class="badge badge-primary">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    Aucun utilisateur n'a cette permission directement ou via ses rôles.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection