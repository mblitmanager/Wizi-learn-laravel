@extends('admin.layout')

@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>
                    <img src="{{ asset('storage/' . $partenaire->logo) }}" alt="Logo {{ $partenaire->identifiant }}"
                        width="50" class="rounded-circle me-2">
                    Classement des stagiaires - {{ $partenaire->identifiant }}
                </h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('classement.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour aux partenaires
                </a>
            </div>
        </div>
        <!-- Le reste du code de la vue show reste inchangé -->
        @include('admin.classements.classement-details')
    </div>
@endsection
