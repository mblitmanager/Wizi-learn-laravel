@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Catalogue de Formation</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('catalogue_formation.index') }}">Liste des catalogues
                            formations</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Détails</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('catalogue_formation.index') }}" class="btn btn-primary btn-sm px-4">
                <i class="bx bx-arrow-back"></i> Retour
            </a>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card shadow rounded">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 text-white">Détails de la Formation : {{ $catalogueFormations->titre }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong><i class="bx bx-book"></i> Titre :</strong> {{ $catalogueFormations->titre }}</p>
                        <p><strong><i class="bx bx-time"></i> Durée :</strong> {{ $catalogueFormations->duree }} heures</p>
                        <p><strong><i class="bx bx-certification"></i> Certification :</strong>
                            {{ $catalogueFormations->certification ?? 'Aucune' }}</p>
                        <p><strong><i class="bx bx-list-check"></i> Prérequis :</strong>
                            {{ $catalogueFormations->prerequis ?? 'Aucun' }}</p>
                        <p><strong><i class="bx bx-money"></i> Tarif :</strong>
                            {{ number_format($catalogueFormations->tarif, 2) }} €</p>
                        <p><strong><i class="bx bx-check-circle"></i> Statut :</strong>
                            @if ($catalogueFormations->statut == '1')
                                <span class="badge bg-success">Publié</span>
                            @elseif ($catalogueFormations->statut == '0')
                                <span class="badge bg-secondary">Non publié</span>
                            @endif
                        </p>
                        <p><strong><i class="bx bx-layer"></i> Formation associée :</strong> {{ $formation->titre }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong><i class="bx bx-detail"></i> Description :</strong></p>
                        <p>{!! $catalogueFormations->description ?? 'Aucune description fournie.' !!}</p>

                        @if ($catalogueFormations->image_url)
                            <div class="text-center mt-3">
                                <img src="{{ asset('storage/' . $catalogueFormations->image_url) }}"
                                    alt="Image de la formation" class="img-fluid rounded shadow" style="max-height: 250px;">
                            </div>
                        @else
                            <div class="alert alert-warning text-center mt-3">
                                Aucune image n’est disponible pour cette formation.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#stagiairesTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json"
                },
                paging: true,
                searching: true,
                ordering: true,
                lengthMenu: [5, 10, 25, 50],
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                initComplete: function() {
                    this.api().columns().every(function() {
                        var that = this;
                        $('input', this.header()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    });
                }
            });
        });
    </script>
@endsection
