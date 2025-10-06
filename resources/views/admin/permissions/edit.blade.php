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
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modifier la Permission : {{ $permission->name }}
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
                <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nom de la permission *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $permission->name) }}" 
                                           placeholder="ex: view_users, create_posts" required>
                                    <small class="form-text text-muted">
                                        Utilisez le format snake_case (ex: manage_users, view_dashboard)
                                    </small>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="group">Groupe *</label>
                                    <input type="text" class="form-control @error('group') is-invalid @enderror" 
                                           id="group" name="group" value="{{ old('group', $permission->group) }}" 
                                           list="group-list" required>
                                    <datalist id="group-list">
                                        @foreach($groups as $group)
                                            <option value="{{ $group }}">
                                        @endforeach
                                    </datalist>
                                    <small class="form-text text-muted">
                                        Groupe d'appartenance (ex: Administration, Formations, Utilisateurs)
                                    </small>
                                    @error('group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Décrivez l'objectif de cette permission...">{{ old('description', $permission->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                    {{ old('is_active', $permission->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Permission active</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection