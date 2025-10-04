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
                            <li class="breadcrumb-item active text-uppercase fw-bold" aria-current="page">Événements
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('parrainage_events.create') }}" type="button" class="btn btn-sm btn-primary px-4">
                            <i class="fadeIn animated bx bx-plus"></i>Créer un événement</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="px-4 py-4">
                                <table class="table table-bordered table-hover w-100 text-wrap align-middle">
                                    <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Prix</th>
                                        <th>Date Début</th>
                                        <th>Date Fin</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($events as $event)
                                        <tr>
                                            <td>{{ $event->titre }}</td>
                                            <td>{{ $event->prix }} €</td>
                                            <td>{{ $event->date_debut }}</td>
                                            <td>{{ $event->date_fin }}</td>
                                            <td class="text-nowrap text-center">
                                                <a href="{{ route('parrainage_events.show', $event->id) }}"
                                                   class="btn btn-sm btn-info text-white mb-1">Afficher</a>
                                                <a href="{{ route('parrainage_events.edit', $event->id) }}"
                                                   class="btn btn-sm btn-success mb-1">Modifier</a>
                                                <form action="{{ route('parrainage_events.destroy', $event->id) }}"
                                                      method="POST" style="display:inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger mb-1"
                                                            onclick="return confirm('Supprimer cette evenement ?')">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{ $events->links() }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
