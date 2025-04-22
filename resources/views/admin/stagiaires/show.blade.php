@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('stagiaires.index') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Gestion stagiaire</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('stagiaires.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                        class="fadeIn animated bx bx-log-out"></i> Retour</a>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <div class="main-body">
                        <div class="text-center mb-4">
                            <form action="{{ route('parametre.updateImage', $stagiaire->user->id) }}" method="POST"
                                  enctype="multipart/form-data" id="updateImageForm">
                                @csrf
                                @method('PUT')

                                <label for="imageInput"
                                       style="cursor: pointer; position: relative; display: inline-block;">
                                    <img
                                        src="{{ $stagiaire->user->image ? asset($stagiaire->user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($stagiaire->user->name) . '&background=0D8ABC&color=fff&size=128' }}"
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

                            <h3 class="mt-3 mb-1">{{ $stagiaire->user->name }}</h3>
                            <span class="badge bg-info text-dark px-3 py-1">{{ ucfirst($stagiaire->user->role) }}</span>
                        </div>

                        <div class="row">
                            <div class="card-body">
                                <div class="mb-3">
                                    <h4 class="mb-2">{{ $stagiaire->civilite }}.
                                        {{ $stagiaire->user->name }}-{{ $stagiaire->prenom }}
                                    </h4>
                                    <hr>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 fw-bold">Nom :</label>
                                        <div class="col-sm-8">
                                            {{ $stagiaire->user->name }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 fw-bold">Prénom :</label>
                                        <div class="col-sm-8">
                                            {{ $stagiaire->prenom }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 fw-bold">Date de naissance :</label>
                                        <div class="col-sm-8">
                                            {{ $stagiaire->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label class="col-sm-4 fw-bold">Adresse email :</label>
                                        <div class="col-sm-8">
                                            <a href="mailto:{{ $stagiaire->user->email }}">{{ $stagiaire->user->email }}</a>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 fw-bold">Téléphone :</label>
                                        <div class="col-sm-8">
                                            {{ $stagiaire->telephone }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 fw-bold">Ville :</label>
                                        <div class="col-sm-8">
                                            {{ $stagiaire->ville }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 fw-bold">Code postal :</label>
                                        <div class="col-sm-8">
                                            {{ $stagiaire->code_postal }}
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 fw-bold">Adresse :</label>
                                        <div class="col-sm-8">
                                            {{ $stagiaire->adresse }}
                                        </div>
                                    </div>

                                </div>
                                <hr>
                                <h5>Liste des formations</h5>
                                <hr>
                                @unless($stagiaire->formations->isEmpty())

                                    <div class="card">
                                        <div class="card-body">
                                            <div class="accordion accordion-flush d-flex flex-wrap gap-3"
                                                 id="accordionFormation">
                                                @foreach ($stagiaire->formations as $index => $formation)
                                                    <div class="accordion-item col-md-3 p-0">
                                                        <h2 class="accordion-header"
                                                            id="formation-heading-{{ $index }}">
                                                            <button
                                                                class="accordion-button bg-success text-white collapsed"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#formation-collapse-{{ $index }}"
                                                                aria-expanded="false"
                                                                aria-controls="formation-collapse-{{ $index }}">
                                                                {{ $formation->titre }}
                                                            </button>
                                                        </h2>
                                                        <div id="formation-collapse-{{ $index }}"
                                                             class="accordion-collapse collapse"
                                                             aria-labelledby="formation-heading-{{ $index }}"
                                                             data-bs-parent="#accordionFormation">
                                                            <div class="accordion-body">
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item">
                                                                        <strong>Categorie</strong> :
                                                                        {{ $formation->categorie }}</li>
                                                                    <li class="list-group-item"><strong>Durée</strong> :
                                                                        {{ $formation->duree }}</li>
                                                                    <li class="list-group-item">
                                                                        <strong>Description</strong> :
                                                                        {{ $formation->description }}</li>
                                                                    <li class="list-group-item"><strong>Date de
                                                                            création</strong> :
                                                                        {{ $formation->created_at->format('d/m/Y à H:i') }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="alert alert-warning">Pas de formations associées à ce stagiaire :
                                        <strong> {{$stagiaire->prenom}}</strong></p>
                                @endunless
                                <hr>
                                <h5>Liste des formateurs</h5>
                                @unless($stagiaire->formateurs->isEmpty())

                                    <hr>
                                    <div class="card">
                                        <div class="card-body">
                                            {{-- Accordéon Formateurs --}}
                                            <div class="accordion accordion-flush d-flex flex-wrap gap-3"
                                                 id="accordionFormateurs">
                                                @foreach ($stagiaire->formateurs as $index => $formateur)
                                                    <div class="accordion-item col-md-3 p-0">
                                                        <h2 class="accordion-header"
                                                            id="formateur-heading-{{ $index }}">
                                                            <button class="accordion-button bg-success text-white collapsed"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#formateur-collapse-{{ $index }}"
                                                                    aria-expanded="false"
                                                                    aria-controls="formateur-collapse-{{ $index }}">
                                                                {{ $formateur->user->name }}
                                                            </button>
                                                        </h2>
                                                        <div id="formateur-collapse-{{ $index }}"
                                                             class="accordion-collapse collapse"
                                                             aria-labelledby="formateur-heading-{{ $index }}"
                                                             data-bs-parent="#accordionFormateurs">
                                                            <div class="accordion-body">
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item"><strong>Nom</strong> :
                                                                        {{ $formateur->prenom }}</li>
                                                                    <li class="list-group-item"><strong>Date de
                                                                            création</strong> :
                                                                        {{ $formateur->created_at->format('d/m/Y à H:i') }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="alert alert-warning">Pas de formateurs associés à ce stagiaire
                                        <strong> {{$stagiaire->prenom}}</strong></p>
                                @endunless
                                <hr>
                                <h5>Liste des commerciaux</h5>
                                @unless($stagiaire->commercials->isEmpty())
                                    <hr>
                                    <div class="card">
                                        <div class="card-body">
                                            {{-- Accordéon commercial --}}
                                            <div class="accordion accordion-flush d-flex flex-wrap gap-3"
                                                 id="accordionCommercial">
                                                @foreach ($stagiaire->commercials as $index => $cormecial)
                                                    <div class="accordion-item col-md-3 p-0">
                                                        <h2 class="accordion-header"
                                                            id="cormecial-heading-{{ $index }}">
                                                            <button class="accordion-button bg-success text-white collapsed"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#cormecial-collapse-{{ $index }}"
                                                                    aria-expanded="false"
                                                                    aria-controls="cormecial-collapse-{{ $index }}">
                                                                {{ $cormecial->user->name }}
                                                            </button>
                                                        </h2>
                                                        <div id="cormecial-collapse-{{ $index }}"
                                                             class="accordion-collapse collapse"
                                                             aria-labelledby="cormecial-heading-{{ $index }}"
                                                             data-bs-parent="#accordionCommercial">
                                                            <div class="accordion-body">
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item"><strong>Nom</strong> :
                                                                        {{ $cormecial->prenom }}</li>
                                                                    <li class="list-group-item"><strong>Date de
                                                                            création</strong> :
                                                                        {{ $cormecial->created_at->format('d/m/Y à H:i') }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="alert alert-warning">Pas de commerciaux associés à ce stagiaire
                                        <strong> {{$stagiaire->prenom}}</strong></p>
                                @endunless
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
@endsection
