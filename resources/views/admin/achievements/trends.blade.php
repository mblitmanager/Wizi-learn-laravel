@extends('admin.layout')
@section('title', 'Tendances des Succès')
@section('content')
<div class="container-fluid">
    <div class="card mt-4">
        <div class="card-header">
            <h4>Tendances des Succès (30 derniers jours)</h4>
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
                    @foreach($trends as $trend)
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
