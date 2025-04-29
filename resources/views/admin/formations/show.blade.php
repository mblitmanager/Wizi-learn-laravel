@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
    <div class="container">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('pole_relation_clients.index') }}"><i
                                        class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Détails du domain de formation :
                                {{ $formation->titre }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('formations.index') }}" type="button" class="btn btn-sm btn-primary"><i
                                class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-3">
            <div class="row justify-content-center">
                <!-- Card principale -->
                <div class="col-md-12">
                    <div class="card shadow-lg rounded-lg border-0">
                        <div class="card-body">
                            <div class="row">
                                <!-- Section Description -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book-reader fa-2x text-primary me-3"></i>
                                        <div>
                                            <h6>Description</h6>
                                            <p class="text-muted">{!! $formation->description !!}</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Section Categorie -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tags fa-2x text-warning me-3"></i>
                                        <div>
                                            <h6>Catégorie</h6>
                                            <p class="text-muted">{{ $formation->categorie }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Section Durée -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock fa-2x text-success me-3"></i>
                                        <div>
                                            <h6>Durée</h6>
                                            <p class="text-muted">{{ $formation->duree }} heures</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Section Date de création -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-day fa-2x text-danger me-3"></i>
                                        <div>
                                            <h6>Date de création</h6>
                                            <p class="text-muted">{{ $formation->created_at->format('d/m/Y à H:i') }}</p>
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
