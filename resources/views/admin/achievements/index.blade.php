@extends('admin.layout')
@section('title', 'Gestion des Succès')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center ">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.achievements.index') }}"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Gestion des Succès</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.achievements.create') }}" class="btn btn-sm btn-primary mx-2"><i class="fadeIn animated bx bx-plus"></i> Ajouter un succès</a>
                        <a href="{{ route('admin.achievements.statistics') }}" class="btn btn-sm btn-info mx-2"><i class="fas fa-chart-bar"></i> Statistiques</a>
                        <a href="{{ route('admin.achievements.trends') }}" class="btn btn-sm btn-secondary mx-2"><i class="fas fa-chart-line"></i> Tendances</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-4 border rounded">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Palier</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($achievements as $achievement)
                                <tr>
                                    <td>{{ $achievement->id }}</td>
                                    <td>{{ $achievement->name }}</td>
                                    <td>{{ $achievement->description }}</td>
                                    <td>{{ $achievement->tier }}</td>
                                    <td>
                                        <a href="{{ route('admin.achievements.edit', $achievement->id) }}" class="btn btn-sm btn-warning">Modifier</a>
                                        <form action="{{ route('admin.achievements.destroy', $achievement->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce succès ?')">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
