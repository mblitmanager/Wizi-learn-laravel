@extends('admin.layout')
@section('title', 'Ajouter un Formateur')
@section('content')
<div class="container-fluid">
    <div class="shadow-lg border-0 px-2 py-2 mb-3">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center">
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Modification d'un
                            formateur</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('formateur.index') }}" type="button" class="btn btn-sm btn-primary"><i
                            class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">There were some problems with your input.</span>
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
                <form class="row g-3" action="{{ route('formateur.update', $formateur->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="name">Nom</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $formateur->user->name ?? '') }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="prenom">Prenom</label>
                            <input type="text" name="prenom" id="prenom"
                                class="form-control @error('prenom') is-invalid @enderror"
                                value="{{ old('prenom', $formateur->prenom ?? '') }}">
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
                                value="{{ old('email', $formateur->user->email ?? '') }}">
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
                                value="{{ old('telephone', $formateur->telephone ?? '') }}" autofocus>
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

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stagiaire_id">Stagiaire</label>
                            <select name="stagiaire_id[]" id="stagiaire_id"
                                class="form-select select2 @error('stagiaire_id') is-invalid @enderror" multiple>
                                <option value="">Choisir un ou plusieurs stagiaires</option>
                                @foreach ($stagiaires as $stagiaire)
                                <option value="{{ $stagiaire->id }}"
                                    {{ in_array($stagiaire->id, old('stagiaire_id', $formateur->stagiaires->pluck('id')->toArray())) ? 'selected' : '' }}>
                                    {{ $stagiaire->user->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('stagiaire_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="accordion mb-3" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        Selectionz les catalogues formations associées
                                        <span class="badge bg-primary mx-4"> {{ count($catalogue_formations) }}</span>
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 row-cols-xl-3">
                                            @foreach ($catalogue_formations as $formation)
                                            <div class="col">

                                                <div class="card"
                                                    style="box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px; border-left: 5px solid #feb823;">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $formation->titre }}</h5>
                                                        <p class="card-text fw-bold">{{ $formation->categorie }}
                                                        </p>
                                                        </p>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="catalogue_formation_id[]"
                                                                id="formation_{{ $formation->id }}"
                                                                value="{{ $formation->id }}"
                                                                {{ in_array($formation->id, old('catalogue_formation_id', $formateur->catalogue_formations->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                    <hr>
                    <div class="text-center">
                        <button type="submit" class="btn btn-sm btn-primary px-4"><i class="lni lni-save"></i>Mettre
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
            placeholder: "Choisir des catalogue_formations",
            allowClear: true
        });
    });
</script>
@endsection