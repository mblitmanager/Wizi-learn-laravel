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
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modifier le Rôle : {{ $role->name }}
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
                
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nom du rôle *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="1">{{ old('description', $role->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                    {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Rôle actif</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Permissions</label>
                            <div class="row">
                                @foreach($permissions as $group => $groupPermissions)
                                <div class="col-md-4 mb-3">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                {{ $group ?? 'Général' }}
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool select-all-group" data-group="{{ $group ?? 'general' }}">
                                                        <i class="fas fa-check-double"></i>
                                                    </button>
                                                </div>
                                            </h3>
                                        </div>
                                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                            @foreach($groupPermissions as $permission)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox" type="checkbox" 
                                                       name="permissions[]" value="{{ $permission->id }}"
                                                       id="perm_{{ $permission->id }}"
                                                       data-group="{{ $group ?? 'general' }}"
                                                       {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    <strong>{{ $permission->name }}</strong>
                                                    @if($permission->description)
                                                    <small class="text-muted d-block">{{ $permission->description }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Sélectionner/désélectionner toutes les permissions d'un groupe
    $('.select-all-group').click(function() {
        const group = $(this).data('group');
        const checkboxes = $(`.permission-checkbox[data-group="${group}"]`);
        const allChecked = checkboxes.length === checkboxes.filter(':checked').length;
        
        checkboxes.prop('checked', !allChecked);
    });
});
</script>
@endpush