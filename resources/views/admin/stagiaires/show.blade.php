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
                        <div class="row">
                            <div class="card-body">
                                <div class="mb-3">
                                    <h4 class="mb-2">{{ $stagiaire->civilite }}.
                                        {{ $stagiaire->user->name }}-{{ $stagiaire->prenom }}
                                    </h4>
                                    <ul class="list-group">
                                        <li class="list-group-item"><strong>Nom</strong> :
                                            {{ $stagiaire->user->name }}
                                        </li>
                                        <li class="list-group-item"><strong> Prenom</strong> : {{ $stagiaire->prenom }}
                                        </li>
                                        <li class="list-group-item"><strong>Date de naissance</strong> :
                                            {{ $stagiaire->date_naissance }}
                                        </li>

                                        <li class="list-group-item"><strong>Telephone</strong> :
                                            {{ $stagiaire->telephone }}
                                        </li>
                                        <li class="list-group-item"><strong>Code postal</strong> :
                                            {{ $stagiaire->code_postal }}
                                        </li>
                                        <li class="list-group-item"><strong>Ville</strong> :
                                            {{ $stagiaire->ville }}
                                        </li>

                                        <li class="list-group-item"><strong>Date de creation</strong> :
                                            {{ $stagiaire->created_at->format('d/m/Y à H:i') }}
                                        </li>
                                    </ul>
                                </div>
                                <h5>Liste des formations</h5>
                                <hr>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="accordion accordion-flush d-flex flex-wrap gap-3"
                                            id="accordionFormation">
                                            @foreach ($stagiaire->formations as $index => $formation)
                                                <div class="accordion-item col-md-3 p-0">
                                                    <h2 class="accordion-header"
                                                        id="formation-heading-{{ $index }}">
                                                        <button class="accordion-button bg-success text-white collapsed"
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
                                                                <li class="list-group-item"><strong>Categorie</strong> :
                                                                    {{ $formation->categorie }}</li>
                                                                <li class="list-group-item"><strong>Durée</strong> :
                                                                    {{ $formation->duree }}</li>
                                                                <li class="list-group-item"><strong>Description</strong> :
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

                                <hr>
                                <h5>Liste des formateurs</h5>
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

                                <hr>
                                <h5>Liste des commerciaux</h5>
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
