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
                            <li class="breadcrumb-item"><a href="{{ route('pole_relation_clients.index') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Détails d'un pôle
                                relation client</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('pole_relation_clients.index') }}" type="button"
                            class="btn btn-sm btn-primary px-4"> <i class="fadeIn animated bx bx-log-out"></i> Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="main-body">
                            <div class="row">


                                <div class="text-center mb-4">
                                    <!-- Profile Image Section -->
                                    <img src="{{ $poleRelationClient->user->image ? asset($poleRelationClient->user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($poleRelationClient->user->name) . '&background=0D8ABC&color=fff&size=128' }}"
                                        class="rounded-circle shadow" width="200" height="200" alt="Avatar"
                                        style="object-fit: cover; max-width: 160px; max-height: 160px; border-radius: 50%;">
                                    <h3 class="mt-3 mb-1">{{ $poleRelationClient->user->name }}</h3>
                                    <span
                                        class="badge bg-info text-dark px-3 py-1">{{ ucfirst($poleRelationClient->role) }}</span>
                                </div>

                                <h2 class="text-center">
                                    @if ($poleRelationClient->stagiaire)
                                        {{ $poleRelationClient->stagiaire->civilite }}
                                    @endif
                                    . {{ $poleRelationClient->user->name }} @if ($poleRelationClient->stagiaire)
                                        {{ $poleRelationClient->stagiaire->prenom }}
                                    @endif
                                </h2>

                                <hr>

                                <!-- Formateur Details -->
                                <div class="row mb-3">
                                    <label class="col-sm-4 fw-bold">Nom :</label>
                                    <div class="col-sm-8">{{ $poleRelationClient->user->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-4 fw-bold">Rôle :</label>
                                    <div class="col-sm-8">{{ ucfirst($poleRelationClient->role) }}</div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-4 fw-bold">Prénom :</label>
                                    <div class="col-sm-8">{{ $poleRelationClient->prenom }}</div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-4 fw-bold">Email :</label>
                                    <div class="col-sm-8">
                                        <a
                                            href="mailto:{{ $poleRelationClient->user->email }}">{{ $poleRelationClient->user->email }}</a>
                                    </div>
                                </div>


                                <hr>


                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 class="d-flex align-items-center mb-3">
                                            <i class="bx bx-group me-2"></i> Stagiaires associés
                                        </h5>

                                        <!-- Accordion for Stagiaires -->
                                        <div class="card-body">
                                            <div class="accordion" id="stagiairesAccordion">
                                                @foreach ($poleRelationClient->stagiaires as $key => $row)
                                                    <div class="accordion-item mb-3">
                                                        <h2 class="accordion-header" id="heading{{ $key }}">
                                                            <button
                                                                class="accordion-button d-flex justify-content-between align-items-center shadow-sm p-3"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#collapse{{ $key }}"
                                                                aria-expanded="{{ $key == 0 ? 'true' : 'false' }}"
                                                                aria-controls="collapse{{ $key }}">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bx bx-user-circle me-3"
                                                                        style="font-size: 1.5rem;"></i>
                                                                    <div class="fw-bold">{{ $row->user->name }}
                                                                        {{ $row->prenom }}
                                                                    </div>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapse{{ $key }}"
                                                            class="accordion-collapse collapse @if ($key == 0) show @endif"
                                                            aria-labelledby="heading{{ $key }}"
                                                            data-bs-parent="#stagiairesAccordion"
                                                            style="box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;">
                                                            <div class="accordion-body p-4 bg-light shadow-sm rounded-3">
                                                                <div class="row">
                                                                    <div class="col-12 col-md-6 mb-3">
                                                                        <strong>Adresse:</strong> <span
                                                                            class="text-muted">{{ $row->adresse }}</span>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 mb-3">
                                                                        <strong>Téléphone:</strong> <span
                                                                            class="text-muted">{{ $row->telephone }}</span>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 mb-3">
                                                                        <strong>Date de naissance:</strong> <span
                                                                            class="text-muted">{{ $row->date_naissance }}</span>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 mb-3">
                                                                        <strong>Ville:</strong> <span
                                                                            class="text-muted">{{ $row->ville }}</span>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 mb-3">
                                                                        <strong>Code postal:</strong> <span
                                                                            class="text-muted">{{ $row->code_postal }}</span>
                                                                    </div>
                                                                </div>
                                                                <!-- Add additional info or buttons if needed -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="text-end mt-4">
                                                <a href="{{ route('pole_relation_clients.edit', $poleRelationClient->id) }}"
                                                    class="btn btn-outline-warning btn-sm me-2">
                                                    <i class="bx bx-edit-alt"></i> Modifier
                                                </a>
                                                <form
                                                    action="{{ route('pole_relation_clients.destroy', $poleRelationClient->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce pole relation client ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm"><i
                                                            class="bx bx-trash"></i>
                                                        Supprimer</button>
                                                </form>
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
    </div>
@endsection
@section('scripts')
@endsection
