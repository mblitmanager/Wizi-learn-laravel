@extends('admin.layout')

@section('content')
    <div class="container py-4">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('parametre.index') }}"><i class="bx bx-home-alt fs-5"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Utilisateur</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <a href="{{ route('parametre.index') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-chevron-left-circle"></i> Retour
                </a>
            </div>
        </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <form action="{{ route('parametre.updateImage', $user->id) }}" method="POST"
                                  enctype="multipart/form-data" id="updateImageForm">
                                @csrf
                                @method('PUT')

                                <label for="imageInput"
                                       style="cursor: pointer; position: relative; display: inline-block;">
                                    <img
                                        src="{{ $user->image ? asset($user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D8ABC&color=fff&size=128' }}"
                                        class="rounded-circle shadow" width="200" height="200" alt="Avatar"
                                        id="profileImage"
                                    style="object-fit: cover">

                                    <!-- Caméra icon -->
                                    <span style="
                                        position: absolute;
                                        bottom: 0;
                                        right: 25px;
                                        background: #fff;
                                        width: 30px;
                                        height: 30px;
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                    ">
                                        <i class="bx bx-camera" style="font-size: 16px;"></i>
                                    </span>

                                </label>

                                <input type="file" name="image" id="imageInput" class="d-none" accept="image/*"
                                       onchange="document.getElementById('updateImageForm').submit();">
                            </form>

                            <h3 class="mt-3 mb-1">{{ $user->name }}</h3>
                            <span class="badge bg-info text-dark px-3 py-1">{{ ucfirst($user->role) }}</span>
                        </div>

                        <h2>@if($user->stagiaire){{$user->stagiaire->civilite}}@endif
                            . {{$user->name}} @if($user->stagiaire){{$user->stagiaire->prenom}}@endif</h2>

                        <hr>

                        <div class="row mb-3">
                            <label class="col-sm-4 fw-bold">Nom :</label>
                            <div class="col-sm-8">{{ $user->name }}</div>
                        </div>
                        @if($user->stagiaire)
                            <div class="row mb-3">
                                <label class="col-sm-4 fw-bold">Prénom :</label>
                                <div class="col-sm-8">
                                    {{ $user->stagiaire->prenom ?? '-' }}
                                </div>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <label class="col-sm-4 fw-bold">Adresse email :</label>
                            <div class="col-sm-8">
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-4 fw-bold">Rôle :</label>
                            <div class="col-sm-8">{{ ucfirst($user->role) }}</div>
                        </div>

                        @if($user->stagiaire)

                            <div class="row mb-3">
                                <label class="col-sm-4 fw-bold">Téléphone
                                    :</label>
                                <div class="col-sm-8">
                                    {{ $user->stagiaire->telephone ?? '-' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 fw-bold">Adresse :</label>
                                <div class="col-sm-8">
                                    {{ $user->stagiaire->adresse ?? '-' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 fw-bold">Date de naissance :</label>
                                <div class="col-sm-8">
                                    {{ $user->stagiaire && $user->stagiaire->date_naissance
                                        ? \Carbon\Carbon::parse($user->stagiaire->date_naissance)->format('d/m/Y')
                                        : '-' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 fw-bold">Ville :</label>
                                <div class="col-sm-8">
                                    {{ $user->stagiaire->ville ?? '-' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-4 fw-bold">Code postal :</label>
                                <div class="col-sm-8">
                                    {{ $user->stagiaire->code_postal ?? '-' }}
                                </div>
                            </div>
                        @endif

                        <div class="text-end mt-4">
                            <a href="{{ route('parametre.edit', $user->id) }}" class="btn btn-outline-warning me-2">
                                <i class="bx bx-edit-alt"></i> Modifier
                            </a>
                            <form action="{{ route('parametre.destroy', $user->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger"><i class="bx bx-trash"></i> Supprimer</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
