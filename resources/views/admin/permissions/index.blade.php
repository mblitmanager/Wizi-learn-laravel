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
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Gestion des Permissions
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                         <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Permission
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
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @foreach($permissions as $group => $groupPermissions)
                    <div class="card card-outline card-primary mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ $group ?? 'Permissions Générales' }}
                                <span class="badge bg-success">{{ $groupPermissions->count() }} permissions</span>
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                    <table class="table table-bordered table-striped text-black">
                                    <thead>
                                        <tr>
                                            <th width="25%">Nom</th>
                                            <th width="35%">Description</th>
                                            <th width="15%">Groupe</th>
                                            <th width="10%">Rôles</th>
                                            <th width="10%">Statut</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupPermissions as $permission)
                                        <tr>
                                            <td>
                                                <strong>{{ $permission->name }}</strong>
                                            </td>
                                            <td>{{ $permission->description ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $permission->group ?? 'Général' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $permission->roles_count }} rôles</span>
                                            </td>
                                            <td>
                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input toggle-status" 
                                                               id="perm_{{ $permission->id }}" 
                                                               data-id="{{ $permission->id }}"
                                                               {{ $permission->is_active ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="perm_{{ $permission->id }}"></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                            {{ $permission->roles_count > 0 ? 'disabled' : '' }}
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette permission ?')">
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
                    @endforeach
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
        const permissionId = $(this).data('id');
        const isActive = $(this).is(':checked');
        
        $.ajax({
            url: "{{ url('admin/permissions') }}/" + permissionId + "/toggle-status",
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