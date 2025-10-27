@extends('admin.layout')
@section('title', 'Rapports d\'import')
@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h4>Rapports d'import des stagiaires</h4>
                @if (empty($files) || count($files) === 0)
                    <div class="alert alert-info">Aucun rapport trouvé.</div>
                @else
                    <div class="mb-3">
                        <form id="purgeReportsForm" action="{{ route('stagiaires.import.purge') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" id="purgeReportsBtn">Purger tous les rapports</button>
                        </form>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fichier</th>
                                <th>Dernière modification</th>
                                <th>Taille</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($files as $f)
                                <tr>
                                    <td>{{ $f['filename'] }}</td>
                                    <td>{{ $f['modified'] }}</td>
                                    <td>{{ number_format($f['size'] / 1024, 2) }} KB</td>
                                    <td>
                                    <a href="{{ $f['url'] }}" class="btn btn-sm btn-primary">Télécharger</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
