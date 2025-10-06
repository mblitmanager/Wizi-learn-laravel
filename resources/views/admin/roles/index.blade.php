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
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Gestion des Rôles
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('roles.create') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-plus"></i> Nouveau Rôle</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-black">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th>Utilisateurs</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                    @if($role->is_protected)
                                        <span class="badge badge-warning">Protégé</span>
                                    @endif
                                </td>
                                <td>{{ $role->description ?? 'N/A' }}</td>
                                <td>
                                    @foreach($role->permissions->take(3) as $permission)
                                        <span class="badge bg-info">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                        <span class="badge bg-secondary">+{{ $role->permissions->count() - 3 }} plus</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $role->users_count }} utilisateurs</span>
                                </td>
                                <td>
                                    @if($role->is_protected)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <div class="form-group mb-0">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input toggle-status" 
                                                        id="role_{{ $role->id }}" 
                                                        data-id="{{ $role->id }}"
                                                        {{ $role->is_active ? 'checked' : '' }}
                                                        {{ $role->is_protected ? 'disabled' : '' }}>
                                                <label class="custom-control-label" for="role_{{ $role->id }}"></label>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm"
                                        {{ $role->is_protected ? 'disabled' : '' }}>
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                {{ $role->is_protected || $role->users_count > 0 ? 'disabled' : '' }}
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.toggle-status').change(function() {
        const roleId = $(this).data('id');
        const isActive = $(this).is(':checked');
        
        $.ajax({
            url: "{{ url('admin/roles') }}/" + roleId + "/toggle-status",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                toastr.success(response.message);
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.error || 'Une erreur est survenue');
                location.reload();
            }
        });
    });
});
</script>
@endpush