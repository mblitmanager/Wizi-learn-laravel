@extends('admin.layout')
@section('title', 'Tendances des Succès')
@section('content')
    <div class="container-fluid">
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Tendances des Succès (30 derniers jours)</h4>
                <a href="{{ route('admin.achievements.index') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-chevron-left-circle"></i> Retour
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Succès débloqués</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trends as $trend)
                            <tr>
                                <td>{{ $trend->date }}</td>
                                <td>{{ $trend->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
