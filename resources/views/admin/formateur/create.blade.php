@extends('admin.layout')
@section('title', 'Ajouter un Formateur')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Formateur</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('formateur.index') }}" type="button" class="btn btn-primary">Retour</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <h5 class="card-title">Ajouter Formateur</h5>
        <hr>
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
                            <label for="name">Prenom</label>
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
                    <div class="col-md-12">
                        <div class="accordion mb-3" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        Selectionz les formations associées 
                                        <span class="badge bg-primary float-end"> {{ count($formations) }}</span>
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                    data-bs-parent="#accordionExample" style="">
                                    <div class="accordion-body">
                                        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 row-cols-xl-3">
                                            @foreach($formations as $formation)
                                            <div class="col">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">{{ $formation->titre }}</h5>
                                                        <p class="card-text">Description rapide de la formation.</p>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="formation_id[]"
                                                                id="formation_{{ $formation->id }}" value="{{ $formation->id }}"
                                                                {{ in_array($formation->id, old('formation_id', [])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="formation_{{ $formation->id }}">
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

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-5">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @section('scripts')
        <script>
            $(document).ready(function () {
                $('.js-example-basic-multiple').select2({
                    placeholder: "Choisir une ou plusieurs formations", // Placeholder
                    allowClear: true
                });
            });
        </script>
    @endsection
@endsection
