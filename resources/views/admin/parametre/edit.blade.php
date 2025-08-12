@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="shadow-lg border-0 px-2 py-2 mb-3">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('parametre.index') }}"><i
                                    class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modification d'un
                            utilisateur</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('parametre.index') }}" type="button" class="btn btn-sm btn-primary"><i
                            class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                </div>
            </div>
        </div>
    </div>
    @if (session('success'))
    <div class="alert alert-success border-0 bg-success alert-dismissible fade show">
        <div class="text-white"> {{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show">
        <div class="text-white"> {{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div class="card">
        <div class="card-body">
            <h4>Modifier l'utilisateur</h4>
            <hr>

            <form action="{{ route('parametre.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.parametre.form-user', ['edit' => true])

                <div class="text-center">
                    <button type="submit" class="btn btn-sm btn-primary px-4"><i class="lni lni-save"></i>Mettre à
                        jour
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    function toggleRoleFields() {
        const role = document.getElementById('role').value;
        document.getElementById('stagiaire-fields').style.display = role === 'stagiaire' ? '' : 'none';
        document.getElementById('other-role-fields').style.display = ['formateur', 'commercial', 'pole relation client']
            .includes(role) ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleRoleFields(); // exécuter au chargement
    });
</script>
@endsection