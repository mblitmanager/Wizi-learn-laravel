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
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="civilite">Civilité</label>
                                    <select name="civilite" id="civilite"
                                        class="form-control @error('civilite') is-invalid @enderror">
                                        <option value="">Sélectionner</option>
                                        <option value="M."
                                            {{ old('civilite', $stagiaire->civilite ?? '') == 'M.' ? 'selected' : '' }}>M.
                                        </option>
                                        <option value="Mme"
                                            {{ old('civilite', $stagiaire->civilite ?? '') == 'Mme' ? 'selected' : '' }}>Mme
                                        </option>
                                        <option value="Mlle"
                                            {{ old('civilite', $stagiaire->civilite ?? '') == 'Mlle' ? 'selected' : '' }}>
                                            Mlle</option>
                                        <option value="Autre"
                                            {{ old('civilite', $stagiaire->civilite ?? '') == 'Autre' ? 'selected' : '' }}>
                                            Autre</option>
                                    </select>
                                    @error('civilite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="text-muted mb-2">Identité du stagiaire</div>
                        <div class="row">
                            <div class="col-md-4">
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
                                <div class="mb-3">
                                    <label for="prenom">Prénom</label>
                                    <input type="text" name="prenom" id="prenom"
                                        class="form-control @error('prenom') is-invalid @enderror"
                                        value="{{ old('prenom', $stagiaire->prenom ?? '') }}">
                                    @error('prenom')
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
                                        value="{{ old('date_naissance', $stagiaire->date_naissance ?? '') }}"
                                        onfocus="this.max=new Date(new Date().getFullYear()-16, new Date().getMonth(), new Date().getDate()).toISOString().split('T')[0]">
                                    @error('date_naissance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="text-muted mb-2">Coordonnées</div>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="email">Adresse mail</label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $stagiaire->user->email ?? '') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="password">Mot de passe</label>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-2">
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
                            <div class="col-md-2">
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
                        </div>
                        <hr class="my-2">
                        <div class="text-muted mb-2">Formations du stagiaire</div>


                        <div class="col-md-4">
                            <!-- Date début formation -->
                            <div class="mb-3">
                                <label for="date_debut_formation">Date de lancement</label>
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
                                <label for="date_inscription">Date de vente</label>
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
                                            Sélectionnez les formations
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
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="formations[{{ $formation->id }}][selected]"
                                                                        id="formation_{{ $formation->id }}"
                                                                        value="1"
                                                                        {{ old("formations.{$formation->id}.selected") ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="formation_{{ $formation->id }}">
                                                                        Sélectionner
                                                                    </label>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label>Date de début</label>
                                                                    <input type="date"
                                                                        name="formations[{{ $formation->id }}][date_debut]"
                                                                        class="form-control"
                                                                        value="{{ old("formations.{$formation->id}.date_debut") }}">
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label>Formateur</label>
                                                                    <select
                                                                        name="formations[{{ $formation->id }}][formateur_id]"
                                                                        class="form-control">
                                                                        <option value="">-- Choisir --</option>
                                                                        @foreach ($formateurs as $formateur)
                                                                            <option value="{{ $formateur->id }}"
                                                                                {{ old("formations.{$formation->id}.formateur_id") == $formateur->id ? 'selected' : '' }}>
                                                                                {{ $formateur->user->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label>Date d'inscription</label>
                                                                    <input type="date"
                                                                        name="formations[{{ $formation->id }}][date_inscription]"
                                                                        class="form-control"
                                                                        value="{{ old("formations.{$formation->id}.date_inscription") }}">
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label>Date de fin</label>
                                                                    <input type="date"
                                                                        name="formations[{{ $formation->id }}][date_fin]"
                                                                        class="form-control"
                                                                        value="{{ old("formations.{$formation->id}.date_fin") }}">
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

                        <hr class="my-2">
                        <div class="text-muted mb-2">Référents (commerciaux, pôle relation)</div>
                        <div class="row">
                            {{-- <div class="col-md-4">
                                <label for="formateur_id">Formateur (optionnel)</label>
                                <select name="formateur_id[]" id="formateur_id" multiple
                                    class="form-control select2 @error('formateur_id') is-invalid @enderror">
                                    @foreach ($formateurs as $formateur)
                                        <option value="{{ $formateur->id }}"
                                            {{ old('formateur_id', $stagiaire->formateur_id ?? '') == $formateur->id ? 'selected' : '' }}>
                                            {{ strtoupper($formateur->user->formatted_name) }}</option>
                                    @endforeach
                                </select>
                                @error('formateur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}
                            <div class="col-md-4">
                                <label for="commercial_id">Commercial (optionnel)</label>
                                <select name="commercial_id[]" id="commercial_id" multiple
                                    class="form-control select2 @error('commercial_id') is-invalid @enderror">
                                    @foreach ($commercials as $commercial)
                                        <option value="{{ $commercial->id }}"
                                            {{ old('commercial_id', $stagiaire->commercial_id ?? '') == $commercial->id ? 'selected' : '' }}>
                                            {{ strtoupper($commercial->user->formatted_name) }}</option>
                                    @endforeach
                                </select>
                                @error('commercial_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="poleRelation_id">Pôle Relation Client (optionnel)</label>
                                <select name="pole_relation_client_id[]" id="poleRelation_id" multiple
                                    class="form-control select2 @error('pole_relation_client_id') is-invalid @enderror">
                                    @foreach ($poleRelations as $poleRelation)
                                        <option value="{{ $poleRelation->id }}"
                                            {{ in_array($poleRelation->id, old('pole_relation_client_id', [])) ? 'selected' : '' }}>
                                            {{ strtoupper($poleRelation->user->formatted_name) }}</option>
                                    @endforeach
                                </select>
                                @error('pole_relation_client_id')
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
    @if (request()->isMethod('post'))
        <pre style="background:#222;color:#fff;padding:10px;">@php dd(request()->all()) @endphp</pre>
    @endif
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
