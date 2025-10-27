@extends('admin.layout')
@section('title', 'Ajouter un formateur')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Gestion formateur
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('formateur.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-log-out"></i> Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">

                            <!-- Profile Image Section -->
                            @if(isset($formateur->user->image) && $formateur->user->image)
                                <img src="{{ asset($formateur->user->image) }}"
                                    class="rounded-circle shadow" width="200" height="200" alt="Avatar"
                                    style="object-fit: cover; max-width: 160px; max-height: 160px; border-radius: 50%;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($formateur->user->name) }}&background=0D8ABC&color=fff&size=128"
                                    class="rounded-circle shadow" width="200" height="200" alt="Avatar"
                                    style="object-fit: cover; max-width: 160px; max-height: 160px; border-radius: 50%;">
                            @endif
                            <h3 class="mt-3 mb-1">{{ $formateur->user->name }} {{ $formateur->prenom }}</h3>
                            <span class="badge bg-info text-dark px-3 py-1">{{ ucfirst($formateur->user->role) }}</span>
                        </div>

                        <h2 class="text-center">
                            @if ($formateur->stagiaire)
                                {{ $formateur->stagiaire->civilite }}
                            @endif
                            . {{ $formateur->user->name }} @if ($formateur->stagiaire)
                                {{ $formateur->stagiaire->prenom }}
                            @endif
                        </h2>

                        <hr>

                        <!-- Formateur Details -->
                        <div class="row mb-3">
                            <label class="col-sm-4 fw-bold">Nom :</label>
                            <div class="col-sm-8">{{ $formateur->user->name }}</div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-4 fw-bold">Prénom :</label>
                            <div class="col-sm-8">{{ $formateur->prenom }}</div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-4 fw-bold">Email :</label>
                            <div class="col-sm-8">
                                <a href="mailto:{{ $formateur->user->email }}">{{ $formateur->user->email }}</a>
                            </div>
                        </div>

                        <!-- catalogue_formations Section with Accordion -->
                        <div class="accordion" id="catalogue_formationsAccordion">
                            @foreach ($formateur->catalogue_formations as $key => $formation)
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
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $key }}">
                                        <button
                                            class="accordion-button d-flex justify-content-between align-items-center shadow-sm p-3 collapsed text-white"
                                            type="button" data-bs-toggle="collapse"
                                            style="background: {{ $bgColor }}"
                                            data-bs-target="#collapse{{ $key }}" aria-expanded="true"
                                            aria-controls="collapse{{ $key }}">
                                            <i class="bx bx-book-open me-2"></i>{{ $formation->titre }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $key }}"
                                        class="accordion-collapse collapse @if ($key == 0) show @endif"
                                        aria-labelledby="heading{{ $key }}"
                                        data-bs-parent="#catalogue_formationsAccordion">
                                        <div class="accordion-body">
                                            <div>
                                                <h6 class="font-weight-bold">Categorie: <badge
                                                        style="background: {{ $bgColor }}; color: #fff;border-radius: 5px; padding: 5px">
                                                        {{ $formation->formation->categorie }}</badge>
                                                </h6>
                                            </div>
                                            <h6 class="font-weight-bold">Description:</h6> {!! $formation->description !!}
                                            <h6 class="font-weight-bold">Durée:</> {{ $formation->duree }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Actions Section -->
                        <div class="text-end mt-4">
                            <a href="{{ route('formateur.edit', $formateur->id) }}"
                                class="btn btn-sm btn-outline-warning me-2">
                                <i class="bx bx-edit-alt"></i> Modifier
                            </a>
                            <form action="{{ route('formateur.destroy', $formateur->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
