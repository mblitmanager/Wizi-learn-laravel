@extends('admin.layout')
@section('title', 'Ajouter un Formateur')
@section('content')
    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">

            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Création d'un
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
            <div class="card">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="card-body p-4 border rounded">
                    <div class="px-4 py-3"
                        style="box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;">
                        <form class="row g-3" action="{{ route('formateur.store') }}" method="POST">
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
                                    <label for="name">Prénom</label>
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
                                <div class="mb-3">
                                    <label for="stagiaire_id">Stagiaire</label>
                                    <select name="stagiaire_id[]" id="stagiaire_id"
                                        class="form-select select2 @error('stagiaire_id') is-invalid @enderror" multiple>
                                        <option value="">Choisir un ou plusieurs stagiaires</option>
                                        @foreach ($stagiaires as $stagiaire)
                                            <option value="{{ $stagiaire->id }}"
                                                {{ in_array($stagiaire->id, old('stagiaire_id', [])) ? 'selected' : '' }}>
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
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                aria-expanded="false" aria-controls="collapseOne">
                                                Selectionez les formations associées
                                                <span class="badge bg-primary float-end mx-3">
                                                    {{ count($formations) }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                            <div class="accordion-body">
                                                <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 row-cols-xl-3">
                                                    @foreach ($formations as $formation)
                                                        <div class="col">
                                                            @php
                                                                $borderColor = match ($formation->categorie) {
                                                                    'Bureautique' => '#3D9BE9',
                                                                    'Langues' => '#A55E6E',
                                                                    'Internet' => '#FFC533',
                                                                    'Création' => '#9392BE',
                                                                    default => 'transparent',
                                                                };
                                                            @endphp

                                                            <div class="card"
                                                                style="box-shadow: rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, rgba(0, 0, 0, 0.06) 0px 0px 0px 1px; border-left: 5px solid {{ $borderColor }};">
                                                                <div class="card-body">
                                                                    <h5 class="card-title">{{ $formation->titre }}</h5>
                                                                    <p class="card-text fw-bold">
                                                                        {{ $formation->categorie }}
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
