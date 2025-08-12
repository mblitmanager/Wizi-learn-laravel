@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('stagiaires.index') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase " aria-current="page">Détails d'un
                                stagiaire</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('stagiaires.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-log-out"></i> Retour</a>
                        <a href="{{ route('stagiaires.edit', $stagiaire->id) }}" type="button"
                            class="btn btn-sm btn-warning px-4 ms-2"> <i class="bx bx-edit"></i> Modifier</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="main-body">
                            <div class="text-center mb-4">
                                <form action="{{ route('parametre.updateImage', $stagiaire->user->id) }}" method="POST"
                                    enctype="multipart/form-data" id="updateImageForm">
                                    @csrf
                                    @method('PUT')

                                    <label for="imageInput"
                                        style="cursor: pointer; position: relative; display: inline-block;">
                                        <img src="{{ $stagiaire->user->image ? asset($stagiaire->user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($stagiaire->user->name) . '&background=0D8ABC&color=fff&size=128' }}"
                                            class="rounded-circle shadow" width="200" height="200" alt="Avatar"
                                            id="profileImage" style="object-fit: cover">

                                        <!-- Caméra icon -->
                                        <span
                                            style="
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
                                            <label class="col-sm-4 ">Nom :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->user->name }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
        <!-- Partenaire Section -->
        @if($stagiaire->partenaire)
            <div class="card mb-4 shadow-sm">
                <div class="card-header text-dark">
                    <h5 class="mb-0"><i class="bx bx-building me-2"></i> Partenaire Associé</h5>
                </div>
                <div class="card-body" style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Identifiant :</strong> {{ $stagiaire->partenaire->identifiant }}</li>
                        <li class="list-group-item"><strong>Adresse :</strong> {{ $stagiaire->partenaire->adresse }}</li>
                        <li class="list-group-item"><strong>Ville :</strong> {{ $stagiaire->partenaire->ville }}</li>
                        <li class="list-group-item"><strong>Département :</strong> {{ $stagiaire->partenaire->departement }}</li>
                        <li class="list-group-item"><strong>Code postal :</strong> {{ $stagiaire->partenaire->code_postal }}</li>
                        <li class="list-group-item"><strong>Type :</strong> {{ $stagiaire->partenaire->type }}</li>
                        @if($stagiaire->partenaire->logo)
                            <li class="list-group-item"><strong>Logo :</strong><br>
                                <img src="{{ asset('storage/' . $stagiaire->partenaire->logo) }}" alt="Logo Partenaire" style="max-width:120px;max-height:120px;">
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @else
            <div class="alert alert-warning mt-4">
                <strong>Aucun partenaire associé à ce stagiaire.</strong> <br>
                Stagiaire : <strong>{{ $stagiaire->prenom }}</strong>
            </div>
        @endif
                                            <label class="col-sm-4 ">Prénom :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->prenom }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 ">Date de naissance :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label class="col-sm-4 ">Adresse email :</label>
                                            <div class="col-sm-8">
                                                <a
                                                    href="mailto:{{ $stagiaire->user->email }}">{{ $stagiaire->user->email }}</a>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 ">Téléphone :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->telephone }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 ">Ville :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->ville }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 ">Code postal :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->code_postal }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 ">Adresse :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->adresse }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 ">Date de lancement :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->date_debut_formation ? \Carbon\Carbon::parse($stagiaire->date_debut_formation)->format('d/m/Y') : 'Non renseignée' }}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 ">Date de vente :</label>
                                            <div class="col-sm-8">
                                                {{ $stagiaire->date_inscription ? \Carbon\Carbon::parse($stagiaire->date_inscription)->format('d/m/Y') : 'Non renseignée' }}
                                            </div>
                                        </div>

                                    </div>
                                    <hr>
                                    <div class="container my-4">
                                        <!-- Formations Section -->
                                        @unless ($stagiaire->catalogue_formations->isEmpty())
                                            <div class="card mb-4 shadow-sm">
                                                <div class="card-header  text-white">
                                                    <h5 class="mb-0"><i class="bx bx-book-reader me-2"></i> Formations
                                                        Associées</h5>
                                                </div>
                                                <div class="card-body"
                                                    style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">
                                                    <div class="accordion accordion-flush" id="accordionFormation">
                                                        @foreach ($stagiaire->catalogue_formations as $index => $formation)
                                                            @php
                                                                $bgColor = '';
                                                                switch ($formation->formation->categorie) {
                                                                    case 'Bureautique':
                                                                        $bgColor = '#3D9BE9';
                                                                        break;
                                                                    case 'Langues':
                                                                        $bgColor = '#A55E6E';
                                                                        break;
                                                                    case 'Internet':
                                                                        $bgColor = '#FFC533';
                                                                        break;
                                                                    case 'Création':
                                                                        $bgColor = '#9392BE';
                                                                        break;
                                                                    default:
                                                                        $bgColor = 'bg-success';
                                                                }
                                                                $formateur = $formation->pivot->formateur_id
                                                                    ? \App\Models\Formateur::find(
                                                                        $formation->pivot->formateur_id,
                                                                    )
                                                                    : null;
                                                            @endphp
                                                            <div class="accordion-item  ">
                                                                <h2 class="accordion-header"
                                                                    id="formation-heading-{{ $index }}">
                                                                    <button class="accordion-button text-white"
                                                                        style="background: {{ $bgColor }}"
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
                                                                            <li class="list-group-item"><strong>Categorie
                                                                                    :</strong>
                                                                                {{ $formation->formation->categorie }}</li>
                                                                            <li class="list-group-item"><strong>Durée
                                                                                    :</strong> {{ $formation->duree }}</li>
                                                                            <li class="list-group-item"><strong>Description
                                                                                    :</strong> {!! $formation->description !!}
                                                                            </li>
                                                                            <li class="list-group-item"><strong>Date de début
                                                                                    de formation :</strong>
                                                                                {{ $formation->pivot->date_debut ? \Carbon\Carbon::parse($formation->pivot->date_debut)->format('d/m/Y') : 'Non renseignée' }}
                                                                            </li>
                                                                            <li class="list-group-item"><strong>Date
                                                                                    d'inscription :</strong>
                                                                                {{ $formation->pivot->date_inscription ? \Carbon\Carbon::parse($formation->pivot->date_inscription)->format('d/m/Y') : 'Non renseignée' }}
                                                                            </li>
                                                                            <li class="list-group-item"><strong>Date de fin
                                                                                    :</strong>
                                                                                {{ $formation->pivot->date_fin ? \Carbon\Carbon::parse($formation->pivot->date_fin)->format('d/m/Y') : 'Non renseignée' }}
                                                                            </li>
                                                                            <li class="list-group-item"><strong>Formateur
                                                                                    :</strong>
                                                                                {{ $formateur ? $formateur->user->name : 'Non renseigné' }}
                                                                            </li>
                                                                            <li class="list-group-item"><strong>Date de
                                                                                    création :</strong>
                                                                                {{ $formation->created_at ? $formation->created_at->format('d/m/Y à H:i') : 'Non renseignée' }}
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
                                            <div class="alert alert-warning mt-4">
                                                <strong>Aucune formation associée à ce stagiaire.</strong> <br>
                                                Stagiaire : <strong>{{ $stagiaire->prenom }}</strong>
                                            </div>
                                        @endunless

                                        <!-- Formateurs Section -->
                                        @unless ($stagiaire->formateurs->isEmpty())
                                            <div class="card mb-4 shadow-sm">
                                                <div class="card-header text-white">
                                                    <h5 class="mb-0"><i class="bx bx-group me-2"></i>Formateurs Associés
                                                    </h5>
                                                </div>
                                                <div class="card-body"
                                                    style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">
                                                    <div class="accordion accordion-flush" id="accordionFormateurs">
                                                        @foreach ($stagiaire->formateurs as $index => $formateur)
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header"
                                                                    id="formateur-heading-{{ $index }}">
                                                                    <button
                                                                        class="accordion-button d-flex justify-content-between align-items-center shadow-sm p-3 collapsed"
                                                                        type="button" data-bs-toggle="collapse"
                                                                        data-bs-target="#formateur-collapse-{{ $index }}"
                                                                        aria-expanded="false"
                                                                        aria-controls="formateur-collapse-{{ $index }}">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="bx bx-user-circle me-3"
                                                                                style="font-size: 1.5rem;"></i>
                                                                            <div class=""> {{ $formateur->user->name }}
                                                                            </div>
                                                                        </div>
                                                                    </button>
                                                                </h2>
                                                                <div id="formateur-collapse-{{ $index }}"
                                                                    class="accordion-collapse collapse"
                                                                    aria-labelledby="formateur-heading-{{ $index }}"
                                                                    data-bs-parent="#accordionFormateurs">
                                                                    <div class="accordion-body">
                                                                        <ul class="list-group list-group-flush">
                                                                            <li class="list-group-item"><strong>Nom :</strong>
                                                                                {{ $formateur->user->name }}</li>
                                                                            <li class="list-group-item"><strong>Prénom
                                                                                    :</strong> {{ $formateur->prenom }}</li>
                                                                            <li class="list-group-item"><strong>Email
                                                                                    :</strong> <a
                                                                                    href="mailto:{{ $formateur->user->email }}">{{ $formateur->user->email }}</a>
                                                                            </li>
                                                                            <li class="list-group-item"><strong>Téléphone
                                                                                    :</strong> {{ $formateur->telephone }}</li>
                                                                        </ul>
                                                                        <hr>
                                                                        <h6><strong>Formations proposées :</strong></h6>
                                                                        <ul class="list-group">
                                                                            @if ($formateur->catalogue_formations && $formateur->catalogue_formations->count())
                                                                                @foreach ($formateur->catalogue_formations as $row)
                                                                                    @php
                                                                                        $bgColor = '';
                                                                                        switch (
                                                                                            $row->formation->categorie
                                                                                        ) {
                                                                                            case 'Bureautique':
                                                                                                $bgColor = '#3D9BE9';
                                                                                                break;
                                                                                            case 'Langues':
                                                                                                $bgColor = '#A55E6E';
                                                                                                break;
                                                                                            case 'Internet':
                                                                                                $bgColor = '#FFC533';
                                                                                                break;
                                                                                            case 'Création':
                                                                                                $bgColor = '#9392BE';
                                                                                                break;
                                                                                            default:
                                                                                                $bgColor = 'bg-success';
                                                                                        }
                                                                                    @endphp
                                                                                    <li class="list-group-item text-white"
                                                                                        style="background: {{ $bgColor }}">
                                                                                        <strong>{{ $row->titre }}</strong> -
                                                                                        {{ $row->formation->categorie ?? ($row->categorie ?? '') }}
                                                                                    </li>
                                                                                @endforeach
                                                                            @else
                                                                                <li class="list-group-item">Aucune formation
                                                                                    associée</li>
                                                                            @endif
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-4">
                                                <strong>Aucun formateur associé à ce stagiaire.</strong> <br>
                                                Stagiaire : <strong>{{ $stagiaire->prenom }}</strong>
                                            </div>
                                        @endunless

                                        <!-- Commerciaux Section -->
                                        @unless ($stagiaire->commercials->isEmpty())
                                            <div class="card mb-4 shadow-sm">
                                                <div class="card-header text-dark">
                                                    <h5 class="mb-0"><i class="bx bx-briefcase-alt-2 me-2"></i> Commerciaux
                                                        Associés</h5>
                                                </div>
                                                <div class="card-body"
                                                    style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">
                                                    <div class="accordion accordion-flush" id="accordionCommercial">
                                                        @foreach ($stagiaire->commercials as $index => $cormecial)
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header"
                                                                    id="cormecial-heading-{{ $index }}">
                                                                    <button
                                                                        class="accordion-button d-flex justify-content-between align-items-center shadow-sm p-3 collapsed"
                                                                        type="button" data-bs-toggle="collapse"
                                                                        data-bs-target="#cormecial-collapse-{{ $index }}"
                                                                        aria-expanded="false"
                                                                        aria-controls="cormecial-collapse-{{ $index }}">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="bx bx-user-circle me-3"
                                                                                style="font-size: 1.5rem;"></i>
                                                                            <div class=""> {{ $cormecial->user->name }}
                                                                            </div>
                                                                        </div>
                                                                    </button>
                                                                </h2>
                                                                <div id="cormecial-collapse-{{ $index }}"
                                                                    class="accordion-collapse collapse"
                                                                    aria-labelledby="cormecial-heading-{{ $index }}"
                                                                    data-bs-parent="#accordionCommercial">
                                                                    <div class="accordion-body">
                                                                        <ul class="list-group list-group-flush">
                                                                            <li class="list-group-item"><strong>Nom :</strong>
                                                                                {{ $cormecial->user->name }}</li>
                                                                            <li class="list-group-item"><strong>Prénom
                                                                                    :</strong> {{ $cormecial->prenom }}</li>
                                                                            <li class="list-group-item"><strong>Email
                                                                                    :</strong> <a
                                                                                    href="mailto:{{ $cormecial->user->email }}">{{ $cormecial->user->email }}</a>
                                                                            </li>
                                                                            <li class="list-group-item"><strong>Téléphone
                                                                                    :</strong> {{ $cormecial->telephone }}</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-4">
                                                <strong>Aucun commercial associé à ce stagiaire.</strong> <br>
                                                Stagiaire : <strong>{{ $stagiaire->prenom }}</strong>
                                            </div>
                                        @endunless

                                        <!-- Pôles Relation Client Section -->
                                        @unless ($stagiaire->poleRelationClient->isEmpty())
                                            <div class="card mb-4 shadow-sm">
                                                <div class="card-header text-dark">
                                                    <h5 class="mb-0"><i class="bx bx-user-voice me-2"></i> Pôles Relation
                                                        Client Associés</h5>
                                                </div>
                                                <div class="card-body"
                                                    style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">
                                                    <div class="accordion accordion-flush" id="accordionPoleRelationClient">
                                                        @foreach ($stagiaire->poleRelationClient as $index => $pole)
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header"
                                                                    id="pole-heading-{{ $index }}">
                                                                    <button
                                                                        class="accordion-button d-flex justify-content-between align-items-center shadow-sm p-3 collapsed"
                                                                        type="button" data-bs-toggle="collapse"
                                                                        data-bs-target="#pole-collapse-{{ $index }}"
                                                                        aria-expanded="false"
                                                                        aria-controls="pole-collapse-{{ $index }}">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="bx bx-user-voice me-3"
                                                                                style="font-size: 1.5rem;"></i>
                                                                            <div class=""> {{ $pole->user->name }}</div>
                                                                        </div>
                                                                    </button>
                                                                </h2>
                                                                <div id="pole-collapse-{{ $index }}"
                                                                    class="accordion-collapse collapse"
                                                                    aria-labelledby="pole-heading-{{ $index }}"
                                                                    data-bs-parent="#accordionPoleRelationClient">
                                                                    <div class="accordion-body">
                                                                        <ul class="list-group list-group-flush">
                                                                            <li class="list-group-item"><strong>Nom :</strong>
                                                                                {{ $pole->user->name }}</li>
                                                                            <li class="list-group-item"><strong>Prénom
                                                                                    :</strong> {{ $pole->prenom }}</li>
                                                                            <li class="list-group-item"><strong>Email
                                                                                    :</strong> <a
                                                                                    href="mailto:{{ $pole->user->email }}">{{ $pole->user->email }}</a>
                                                                            </li>
                                                                            <li class="list-group-item"><strong>Téléphone
                                                                                    :</strong> {{ $pole->telephone }}</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning mt-4">
                                                <strong>Aucun pôle relation client associé à ce stagiaire.</strong> <br>
                                                Stagiaire : <strong>{{ $stagiaire->prenom }}</strong>
                                            </div>
                                        @endunless
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
