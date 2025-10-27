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
                        <form action="{{ route('stagiaires.destroy', $stagiaire->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i
                                    class="fadeIn animated bx bx-trash"></i>Supprimé</button>
                        </form>

                    </div>
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
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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
                            <div class="col-md-4">
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
                                <div class="mb-3">
                                    <label for="password">Mot de passe</label>
                                    <input type="password" name="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
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
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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
                                    <div id="collapseOne" class="accordion-collapse collapse show"
                                        aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                        <div class="accordion-body">
                                            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 row-cols-xl-3">
                                                @foreach ($formations as $formation)
                                                    @php
                                                        $pivot = $stagiaire->catalogue_formations->firstWhere(
                                                            'id',
                                                            $formation->id,
                                                        )?->pivot;
                                                    @endphp
                                                    <div class="col">
                                                        <div class="card border-warning border-bottom border-3 border-0">
                                                            <div class="card-body">
                                                                <h5 class="card-title">{{ $formation->titre }}</h5>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="formations[{{ $formation->id }}][selected]"
                                                                        id="formation_{{ $formation->id }}"
                                                                        value="1"
                                                                        {{ old("formations.{$formation->id}.selected") || $pivot ? 'checked' : '' }}>
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
                                                                        value="{{ old("formations.{$formation->id}.date_debut", $pivot?->date_debut) }}">
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label>Formateur</label>
                                                                    <select
                                                                        name="formations[{{ $formation->id }}][formateur_id]"
                                                                        class="form-control">
                                                                        <option value="">-- Choisir --</option>
                                                                        @foreach ($formateurs as $formateur)
                                                                            <option value="{{ $formateur->id }}"
                                                                                {{ old("formations.{$formation->id}.formateur_id", $pivot?->formateur_id) == $formateur->id ? 'selected' : '' }}>
                                                                                {{ $formateur->user->name }} {{ $formateur->prenom }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label>Date d'inscription</label>
                                                                    <input type="date"
                                                                        name="formations[{{ $formation->id }}][date_inscription]"
                                                                        class="form-control"
                                                                        value="{{ old("formations.{$formation->id}.date_inscription", $pivot?->date_inscription) }}">
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label>Date de fin</label>
                                                                    <input type="date"
                                                                        name="formations[{{ $formation->id }}][date_fin]"
                                                                        class="form-control"
                                                                        value="{{ old("formations.{$formation->id}.date_fin", $pivot?->date_fin) }}">
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
                                <select name="formateur_id[]" id="formateur_id" multiple class="form-control select2 @error('formateur_id') is-invalid @enderror">
                                    @foreach ($formateurs as $formateur)
                                        <option value="{{ $formateur->id }}" {{ in_array($formateur->id, old('formateur_id', $stagiaire->formateurs->pluck('id')->toArray())) ? 'selected' : '' }}>{{ strtoupper($formateur->user->formatted_name) }}</option>
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
                                            {{ in_array($commercial->id, old('commercial_id', $stagiaire->commercials->pluck('id')->toArray())) ? 'selected' : '' }}>
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
                                            {{ in_array($poleRelation->id, old('pole_relation_client_id', $stagiaire->poleRelationClient ? $stagiaire->poleRelationClient->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                                            {{ strtoupper($poleRelation->user->formatted_name) }}</option>
                                    @endforeach
                                </select>
                                @error('pole_relation_client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="partenaire_id" class="form-label">Partenaire</label>
                            <select name="partenaire_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                @foreach ($partenaires as $partenaire)
                                    <option value="{{ $partenaire->id }}"
                                        @if ($stagiaire->partenaire_id == $partenaire->id) selected @endif>{{ $partenaire->identifiant }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($stagiaire->partenaire)
                                <div class="mt-2">
                                    <a href="{{ route('partenaires.show', $stagiaire->partenaire->id) }}"
                                        class="btn btn-sm btn-info text-white">
                                        <i class="bx bx-user"></i> Voir contacts du partenaire
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn  btn-sm btn-success px-4"><i
                                    class="lni lni-save"></i>Mettre à jour
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
