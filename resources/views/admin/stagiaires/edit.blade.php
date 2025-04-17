@extends('admin.layout')
@section('title', 'Modifier un stagiaire')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('stagiaires.index') }}"><i class="bx bx-home-alt"></i></a>
                    <li class="breadcrumb-item active" aria-current="page">Modifier Stagiaire</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('stagiaires.index') }}" type="button" class="btn btn-sm btn-primary"><i
                        class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <h5 class="card-title">Modifier stagiaire</h5>
        <hr>

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

        <div class="card">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card-body p-4 border rounded">
                <form class="row g-3" action="{{ route('stagiaires.update', $stagiaire->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="col-md-4">
                        <label for="name">Nom</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $stagiaire->user->name) }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom" id="prenom"
                            class="form-control @error('prenom') is-invalid @enderror"
                            value="{{ old('prenom', $stagiaire->prenom) }}">
                        @error('prenom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="email">Adresse e-mail</label>
                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $stagiaire->user->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="password">Mot de passe (laisser vide si inchangé)</label>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="civilite">Civilité</label>
                        <input type="text" name="civilite" id="civilite"
                            class="form-control @error('civilite') is-invalid @enderror"
                            value="{{ old('civilite', $stagiaire->civilite) }}">
                        @error('civilite')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="telephone">Téléphone</label>
                        <input type="text" name="telephone" id="telephone"
                            class="form-control @error('telephone') is-invalid @enderror"
                            value="{{ old('telephone', $stagiaire->telephone) }}">
                        @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="adresse">Adresse</label>
                        <input type="text" name="adresse" id="adresse"
                            class="form-control @error('adresse') is-invalid @enderror"
                            value="{{ old('adresse', $stagiaire->adresse) }}">
                        @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="date_naissance">Date de naissance</label>
                        <input type="date" name="date_naissance" id="date_naissance"
                            class="form-control @error('date_naissance') is-invalid @enderror"
                            value="{{ old('date_naissance', $stagiaire->date_naissance) }}">
                        @error('date_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="ville">Ville</label>
                        <input type="text" name="ville" id="ville"
                            class="form-control @error('ville') is-invalid @enderror"
                            value="{{ old('ville', $stagiaire->ville) }}">
                        @error('ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="code_postal">Code postal</label>
                        <input type="text" name="code_postal" id="code_postal"
                            class="form-control @error('code_postal') is-invalid @enderror"
                            value="{{ old('code_postal', $stagiaire->code_postal) }}">
                        @error('code_postal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="formation_id">Formations</label>
                        <select name="formation_id[]" id="formation_id" multiple
                            class="form-control select2 @error('formation_id') is-invalid @enderror">
                            @foreach ($formations as $formation)
                                <option value="{{ $formation->id }}"
                                    {{ in_array($formation->id, old('formation_id', $stagiaire->formations->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $formation->titre }}
                                </option>
                            @endforeach
                        </select>
                        @error('formation_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="col-md-4">
                        <label for="formateur_id">Formateur</label>
                        <select name="formateur_id" id="formateur_id"
                            class="form-control @error('formateur_id') is-invalid @enderror">
                            <option value="">-- Choisir un formateur --</option>
                            @foreach ($formateurs as $formateur)
                                <option value="{{ $formateur->id }}"
                                    {{ old('formateur_id', $stagiaire->formateur_id) == $formateur->id ? 'selected' : '' }}>
                                    {{ $formateur->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('formateur_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="commercial_id">Commercial</label>
                        <select name="commercial_id" id="commercial_id"
                            class="form-control @error('commercial_id') is-invalid @enderror">
                            <option value="">-- Choisir un commercial --</option>
                            @foreach ($commercials as $commercial)
                                <option value="{{ $commercial->id }}"
                                    {{ old('commercial_id', $stagiaire->commercial_id) == $commercial->id ? 'selected' : '' }}>
                                    {{ $commercial->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('commercial_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn  btn-sm btn-success px-4"><i class="lni lni-save"></i>Mettre à
                            jour</button>
                    </div>
                </form>
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
