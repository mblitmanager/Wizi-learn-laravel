@extends('admin.layout')

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
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Gestion parrainage
                                events
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('parrainage_events.index') }}" type="button" class="btn btn-sm btn-primary px-4">
                            <i class="fadeIn animated bx bx-chevron-left-circle"></i>Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h1>{{ $parrainageEvent->titre }}</h1>
                    <p><strong>Prix :</strong> {{ $parrainageEvent->prix }} €</p>
                    <p><strong>Date début :</strong> {{ $parrainageEvent->date_debut }}</p>
                    <p><strong>Date fin :</strong> {{ $parrainageEvent->date_fin }}</p>
                </div>

            </div>
        </div>
    </div>
@endsection
