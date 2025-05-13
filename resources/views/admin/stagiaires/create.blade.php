@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
<div class="container-fluid">
    <div class="shadow-lg border-0 px-2 py-2 mb-3">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="{{ route('stagiaires.index') }}"><i
                                    class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Création d'un
                            stagiaire</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('stagiaires.index') }}" type="button" class="btn btn-sm btn-primary mx-4"><i
                            class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <div class="card-body p-4 border rounded">
                <form class="row g-3" action="{{ route('stagiaires.store') }}" method="POST">
                    @csrf
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="name">Nom</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $stagiaire->user->name ?? '') }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="prenom">Prénom</label>
                            <input type="text" name="prenom" id="prenom"
                                class="form-control @error('prenom') is-invalid @enderror"
                                value="{{ old('prenom', $stagiaire->user->prenom ?? '') }}">
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
                                value="{{ old('email', $stagiaire->user->email ?? '') }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>


                    <div class="col-md-4">
                        <!-- Mot de passe -->
                        <div class="mb-3">
                            <label for="password">Mot de passe</label>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                value="{{ old('password', $stagiaire->user->password ?? '') }}">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Civilité -->
                        <div class="mb-3">
                            <label for="civilite">Civilité</label>
                            <input type="text" name="civilite" id="civilite"
                                class="form-control @error('civilite') is-invalid @enderror"
                                value="{{ old('civilite', $stagiaire->civilite ?? '') }}">
                            @error('civilite')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Téléphone -->
                        <div class="mb-3">
                            <label for="telephone">Téléphone</label>
                            <input type="text" name="telephone" id="telephone"
                                class="form-control @error('telephone') is-invalid @enderror"
                                value="{{ old('telephone', $stagiaire->telephone ?? '') }}">
                            @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Adresse -->
                        <div class="mb-3">
                            <label for="adresse">Adresse</label>
                            <input type="text" name="adresse" id="adresse"
                                class="form-control @error('adresse') is-invalid @enderror"
                                value="{{ old('adresse', $stagiaire->adresse ?? '') }}">
                            @error('adresse')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Date de naissance -->
                        <div class="mb-3">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" name="date_naissance" id="date_naissance"
                                class="form-control @error('date_naissance') is-invalid @enderror"
                                value="{{ old('date_naissance', $stagiaire->date_naissance ?? '') }}">
                            @error('date_naissance')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Ville -->
                        <div class="mb-3">
                            <label for="ville">Ville</label>
                            <input type="text" name="ville" id="ville"
                                class="form-control @error('ville') is-invalid @enderror"
                                value="{{ old('ville', $stagiaire->ville ?? '') }}">
                            @error('ville')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Code postal -->
                        <div class="mb-3">
                            <label for="code_postal">Code postal</label>
                            <input type="text" name="code_postal" id="code_postal"
                                class="form-control @error('code_postal') is-invalid @enderror"
                                value="{{ old('code_postal', $stagiaire->code_postal ?? '') }}">
                            @error('code_postal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Date début formation -->
                        <div class="mb-3">
                            <label for="date_debut_formation">Date début formation</label>
                            <input type="date" name="date_debut_formation" id="date_debut_formation"
                                class="form-control @error('date_debut_formation') is-invalid @enderror"
                                value="{{ old('date_debut_formation', $stagiaire->date_debut_formation ?? '') }}">
                            @error('date_debut_formation')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Date inscription -->
                        <div class="mb-3">
                            <label for="date_inscription">Date inscription</label>
                            <input type="date" name="date_inscription" id="date_inscription"
                                class="form-control @error('date_inscription') is-invalid @enderror"
                                value="{{ old('date_inscription', $stagiaire->date_inscription ?? '') }}">
                            @error('date_inscription')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="accordion mb-3" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false"
                                        aria-controls="collapseOne">
                                        Selectionz les formations
                                        <span class="badge bg-primary mx-2"> {{ count($formations) }}</span>
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 row-cols-xl-3">
                                            @foreach ($formations as $formation)
                                            <div class="col">
                                                <div class="card border-warning border-bottom border-3 border-0">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $formation->titre }}</h5>
                                                        <p class="card-text">Description rapide de la formation.
                                                        </p>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="formation_id[]"
                                                                id="formation_{{ $formation->id }}"
                                                                value="{{ $formation->id }}"
                                                                {{ in_array($formation->id, old('formation_id', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="formation_{{ $formation->id }}">
                                                                Sélectionner
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row">
                        {{-- <div class="col-md-4">
                            <!-- Formateur -->
                            <div class="mb-3">
                                <label for="formateur_id">Formateur (optionnel)</label>
                                <select name="formateur_id" id="formateur_id"
                                    class="form-control @error('formateur_id') is-invalid @enderror">
                                    <option value="">-- Choisir un formateur --</option>
                                    @foreach ($formateurs as $formateur)
                                        <option value="{{ $formateur->id }}"
                        {{ old('formateur_id', $stagiaire->formateur_id ?? '') == $formateur->id ? 'selected' : '' }}>
                        {{ $formateur->user->name }}
                        </option>
                        @endforeach
                        </select>
                        @error('formateur_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
            </div> --}}
            <div class="col-md-4">
                <label for="formateur_id">Formateur(optionnel)</label>
                <select name="formateur_id[]" id="formateur_id" multiple
                    class="form-control select2 @error('formateur_id') is-invalid @enderror">
                    @foreach ($formateurs as $formateur)
                    <option value="{{ $formateur->id }}"
                        {{ old('formateur_id', $stagiaire->formateur_id ?? '') == $formateur->id ? 'selected' : '' }}>
                        {{ $formateur->user->name }}
                    </option>
                    @endforeach
                </select>
                @error('formateur_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Commercial -->
            {{-- <div class="col-md-4">
                            <div class="mb-3">
                                <label for="commercial_id">Commercial (optionnel)</label>
                                <select name="commercial_id" id="commercial_id"
                                    class="form-control @error('commercial_id') is-invalid @enderror">
                                    <option value="">-- Choisir un commercial --</option>
                                    @foreach ($commercials as $commercial)
                                        <option value="{{ $commercial->id }}"
            {{ old('commercial_id', $stagiaire->commercial_id ?? '') == $commercial->id ? 'selected' : '' }}>
            {{ $commercial->user->name }}
            </option>
            @endforeach
            </select>
            @error('commercial_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div> --}}
    <div class="col-md-4">
        <label for="commercial_id">Commercial (optionnel)</label>
        <select name="commercial_id[]" id="commercial_id" multiple
            class="form-control select2 @error('commercial_id') is-invalid @enderror">
            @foreach ($commercials as $commercial)
            <option value="{{ $commercial->id }}"
                {{ old('commercial_id', $stagiaire->commercial_id ?? '') == $commercial->id ? 'selected' : '' }}>
                {{ $commercial->user->name }}
            </option>
            @endforeach
        </select>
        @error('commercial_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="text-center">
    <button type="submit" class="btn btn-primary btn-sm px-4">
        <i class="lni lni-save"></i> Enregistrer
    </button>
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