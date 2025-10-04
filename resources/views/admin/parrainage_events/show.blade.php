@extends('admin.layout')

@section('content')
    <div class="container">
        <h1>{{ $parrainageEvent->titre }}</h1>
        <p><strong>Prix :</strong> {{ $parrainageEvent->prix }} €</p>
        <p><strong>Date début :</strong> {{ $parrainageEvent->date_debut }}</p>
        <p><strong>Date fin :</strong> {{ $parrainageEvent->date_fin }}</p>
        <a href="{{ route('parrainage_events.index') }}" class="btn btn-secondary">Retour</a>
    </div>
@endsection
