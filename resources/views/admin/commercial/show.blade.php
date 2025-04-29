@extends('admin.layout')
@section('title', 'Ajouter un formateur')
@section('content')
    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="breadcrumb-title pe-3"></div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Gestion commercial</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('commercials.index') }}" type="button" class="btn btn-sm btn-primary px-4"> <i
                                class="fadeIn animated bx bx-log-out"></i> Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="main-body">
                            <div class="row">

                                <div class="text-center mb-4">
                                    <!-- Profile Image Section -->
                                    <img src="{{ $commercial->user->image ? asset($commercial->user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($commercial->user->name) . '&background=0D8ABC&color=fff&size=128' }}"
                                        class="rounded-circle shadow" width="200" height="200" alt="Avatar"
                                        style="object-fit: cover">
                                    <h3 class="mt-3 mb-1">{{ $commercial->user->name }}</h3>
                                    <span
                                        class="badge bg-info text-dark px-3 py-1">{{ ucfirst($commercial->user->role) }}</span>
                                </div>

                                <h2 class="text-center">
                                    @if ($commercial->stagiaire)
                                        {{ $commercial->stagiaire->civilite }}
                                    @endif
                                    . {{ $commercial->user->name }} @if ($commercial->stagiaire)
                                        {{ $commercial->stagiaire->prenom }}
                                    @endif
                                </h2>

                                <hr>

                                <!-- Commercial Details -->
                                <div class="row mb-3">
                                    <label class="col-sm-4 fw-bold">Nom :</label>
                                    <div class="col-sm-8">{{ $commercial->user->name }}</div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-4 fw-bold">Prénom :</label>
                                    <div class="col-sm-8">{{ $commercial->prenom }}</div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-4 fw-bold">Email :</label>
                                    <div class="col-sm-8">
                                        <a href="mailto:{{ $commercial->user->email }}">{{ $commercial->user->email }}</a>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <h5 class="d-flex align-items-center mb-3">
                                            <i class="bx bx-group me-2"></i> Stagiaires associés
                                        </h5>

                                        <!-- Accordion for Stagiaires -->
                                        <div class="accordion" id="stagiairesAccordion">
                                            @foreach ($commercial->stagiaires as $key => $row)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading{{ $key }}">
                                                        <button class="accordion-button" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#collapse{{ $key }}"
                                                            aria-expanded="true"
                                                            aria-controls="collapse{{ $key }}">
                                                            <i class="bx bx-user me-2"></i>{{ $row->user->name }}
                                                            {{ $row->prenom }}
                                                        </button>
                                                    </h2>
                                                    <div id="collapse{{ $key }}"
                                                        class="accordion-collapse collapse @if ($key == 0) show @endif"
                                                        aria-labelledby="heading{{ $key }}"
                                                        data-bs-parent="#stagiairesAccordion">
                                                        <div class="accordion-body">
                                                            <strong>Adresse:</strong> {{ $row->adresse }} <br>
                                                            <strong>Téléphone:</strong> {{ $row->telephone }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="text-end mt-4">
                                            <a href="{{ route('commercials.edit', $commercial->id) }}"
                                                class="btn btn-outline-warning btn-sm me-2">
                                                <i class="bx bx-edit-alt"></i> Modifier
                                            </a>
                                            <form action="{{ route('commercials.destroy', $commercial->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commercial ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm"><i class="bx bx-trash"></i>
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
@endsection
@section('scripts')
@endsection
