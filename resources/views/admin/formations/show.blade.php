@extends('admin.layout')
@section('title', 'Ajouter un Quiz')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('formations.index') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Formation</li>
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

    <div class="card">
        <div class="card-body">
            <h5>Details de la formations : <span class="text-success">{{ $formation->titre }}</span></h5>
            <hr>
            <ul class="list-group">
                
                <li class="list-group-item"><strong>Description</strong> : {{ $formation->description }}</li>
                <li class="list-group-item"><strong>Categorie</strong> : {{ $formation->categorie }}</li>
                <li class="list-group-item"><strong>Duree</strong> : {{ $formation->duree }}</li>
                <li class="list-group-item"><strong>Date de creation</strong> :
                    {{ $formation->created_at->format('d/m/Y à H:i') }}</li>
            </ul>
        </div>
    </div>
@endsection