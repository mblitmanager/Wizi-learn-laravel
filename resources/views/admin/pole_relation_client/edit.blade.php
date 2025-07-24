@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">

                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('pole_relation_clients.index') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modification d'un
                                pôle relation client
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('pole_relation_clients.index') }}" type="button"
                            class="btn btn-sm btn-primary"><i class="fadeIn animated bx bx-chevron-left-circle"></i>
                            Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- <div class="col-md-4">

            </div> --}}
            <div class="card">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body p-4 border rounded">
                    <form class="row g-3" action="{{ route('pole_relation_clients.update', $poleRelationClient->id) }}"
                        enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-md-4">
                            <!-- Nom -->
                            <div class="mb-3">
                                <label for="name">Nom</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $poleRelationClient->user->name ?? '') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Prénom -->
                            <div class="mb-3">
                                <label for="prenom">Prénom</label>
                                <input type="text" name="prenom" id="prenom"
                                    class="form-control @error('prenom') is-invalid @enderror"
                                    value="{{ old('prenom', $poleRelationClient->prenom ?? '') }}">
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email">Adresse e-mail</label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $poleRelationClient->user->email ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Mot de passe -->
                            <div class="mb-3">
                                <label for="telephone">Téléphone</label>
                                <input type="text" name="telephone" id="telephone"
                                    class="form-control @error('telephone') is-invalid @enderror"
                                    value="{{ old('telephone', $poleRelationClient->telephone ?? '') }}">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Mot de passe -->
                            <div class="mb-3">
                                <label for="password">Mot de passe</label>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" value="">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Champ rôle libre --}}
                        <div class="form-group mb-3">
                            <label for="role">Rôle (Pole Relation Client)</label>
                            <input type="text" class="form-control" id="role" name="role"
                                placeholder="Ex : conseiller, consultant 1er accueil, interlocuteur, autre..."
                                value="{{ old('role', $poleRelationClient->role ?? '') }}" list="role-options" required>
                            <datalist id="role-options">
                                <option value="conseiller">
                                <option value="consultant 1er accueil">
                                <option value="interlocuteur">
                                <option value="autre">
                            </datalist>
                        </div>


                        <div class="col-md-4">
                            <!-- Stagiaire -->
                            <div class="mb-3">
                                <label for="stagiaire_id">Stagiaire</label>
                                <select name="stagiaire_id[]" id="stagiaire_id"
                                    class="form-select select2 @error('stagiaire_id') is-invalid @enderror" multiple>
                                    <option value="">Choisir un ou plusieurs stagiaires</option>
                                    @foreach ($stagiaires as $stagiaire)
                                        <option value="{{ $stagiaire->id }}"
                                            {{ in_array($stagiaire->id, old('stagiaire_id', $poleRelationClient->stagiaires->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $stagiaire->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('stagiaire_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- Photo de profil -->
                            <div class="mb-3">
                                <label for="photo" class="form-label">Photo de profil</label>
                                <input type="file" class="form-control" id="photo" name="photo"
                                    accept="image/*">
                                @if (isset($poleRelationClient->user->image) && $poleRelationClient->user->image)
                                    <div class="mt-2">
                                        <img src="{{ asset($poleRelationClient->user->image) }}" alt="Photo actuelle"
                                            style="max-width: 120px; max-height: 120px; object-fit: cover;">
                                    </div>
                                @endif
                                @error('photo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="text-center">
                            <button type="submit" class="btn btn-sm btn-primary px-4"> <i class="lni lni-save"></i>
                                Mettre
                                à
                                jour</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Choisir des formations",
                allowClear: true
            });
        });
    </script>
@endsection
