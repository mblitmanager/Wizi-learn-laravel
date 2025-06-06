@extends('admin.layout')
@section('title', 'Modifier un stagiaire')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('stagiaires.index') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modification d'un
                                Stagiaire</li>
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
        </div>
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
        <div class="col-md-12">
            <div class="card">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body">
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
                                value="{{ old('date_naissance', $stagiaire->date_naissance) }}"
                                onfocus="this.max=new Date(new Date().getFullYear()-16, new Date().getMonth(), new Date().getDate()).toISOString().split('T')[0]">
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
                            <label for="catalogue_formation_id">Formations</label>
                            <select name="catalogue_formation_id[]" id="catalogue_formation_id" multiple
                                class="form-control select2 @error('catalogue_formation_id') is-invalid @enderror">
                                @foreach ($formations as $formation)
                                    <option value="{{ $formation->id }}"
                                        {{ in_array($formation->id, old('catalogue_formation_id', $stagiaire->catalogue_formations->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $formation->titre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('catalogue_formation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="formateur_id">Formateur</label>
                            <select name="formateur_id[]" id="formateur_id" multiple
                                class="form-control select2 @error('formateur_id') is-invalid @enderror">
                                @foreach ($formateurs as $formateur)
                                    {{-- <option value="{{ $formateur->id }}"
                            {{ old('formateur_id', $stagiaire->formateur_id) == $formateur->id ? 'selected' : '' }}> --}}
                                    <option value="{{ $formateur->id }}"
                                        {{ in_array($formateur->id, old('formateur_id', $stagiaire->formateurs->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $formateur->user->formatted_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('formateur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="date_debut_formation">Date début formation</label>
                            <input type="date" name="date_debut_formation" id="date_debut_formation"
                                class="form-control @error('date_debut_formation') is-invalid @enderror"
                                value="{{ old('date_debut_formation', $stagiaire->date_debut_formation) }}">
                            @error('date_debut_formation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="date_inscription">Date inscription</label>
                            <input type="date" name="date_inscription" id="date_inscription"
                                class="form-control @error('date_inscription') is-invalid @enderror"
                                value="{{ old('date_inscription', $stagiaire->date_inscription) }}">
                            @error('date_inscription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="commercial_id">Commercial</label>
                            <select name="commercial_id[]" id="commercial_id" multiple
                                class="form-control select2 @error('commercial_id') is-invalid @enderror">
                                @foreach ($commercials as $commercial)
                                    <option value="{{ $commercial->id }}"
                                        {{ in_array($commercial->id, old('commercial_id', $stagiaire->commercials->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $commercial->user->formatted_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('commercial_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="poleRelation_id">Pôle relation client</label>
                            <select name="poleRelation_id[]" id="poleRelation_id" multiple
                                class="form-control select2 @error('poleRelation_id') is-invalid @enderror">
                                @foreach ($poleRelations as $poleRelation)
                                    <option value="{{ $poleRelation->id }}"
                                        {{ in_array($poleRelation->id, old('poleRelation_id', $stagiaire->poleRelations ? $stagiaire->poleRelations->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                                        {{ $poleRelation->user->formatted_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('poleRelation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn  btn-sm btn-success px-4"><i
                                    class="lni lni-save"></i>Mettre
                                à
                                jour
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
