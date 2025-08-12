@extends('admin.layout')
@section('title', 'Ajouter un stagiaire')
@section('content')
    <div class="container-fluid">
        <div class="shadow-lg border-0 px-2 py-2 mb-3">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center">
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active text-uppercase fw-bold"><a
                                    href="{{ route('catalogue_formation.index') }}">Détails d'un catalogue de
                                    formations.</a>
                            </li>

                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('catalogue_formation.index') }}" class="btn btn-primary btn-sm px-4">
                        <i class="bx bx-arrow-back"></i> Retour
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card shadow-lg rounded">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="bx bx-book"></i> Détails de la Formation :
                        {{ $catalogueFormations->titre }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="list-group">
                                <p class="list-group-item d-flex align-items-center">
                                    <i class="bx bx-book-open me-2"></i>
                                    <strong>Titre :</strong> {{ $catalogueFormations->titre }}
                                </p>
                                <p class="list-group-item d-flex align-items-center">
                                    <i class="bx bx-time me-2"></i>
                                    <strong>Durée :</strong> {{ $catalogueFormations->duree }} heures
                                </p>
                                <p class="list-group-item d-flex align-items-center">
                                    <i class="bx bx-certification me-2"></i>
                                    <strong>Certification :</strong>
                                    {{ $catalogueFormations->certification ?? 'Aucune' }}
                                </p>
                                <p class="list-group-item d-flex align-items-center">
                                    <i class="bx bx-list-check me-2"></i>
                                    <strong>Prérequis :</strong>
                                    {{ $catalogueFormations->prerequis ?? 'Aucun' }}
                                </p>
                                <p class="list-group-item d-flex align-items-center">
                                    <i class="bx bx-money me-2"></i>
                                    <strong>Tarif :</strong>
                                    {{ number_format($catalogueFormations->tarif, 2) }} €
                                </p>
                                <p class="list-group-item d-flex align-items-center">
                                    <i class="bx bx-check-circle me-2"></i>
                                    <strong>Statut :</strong>
                                    @if ($catalogueFormations->statut == '1')
                                        <span class="badge bg-success">Publié</span>
                                    @elseif ($catalogueFormations->statut == '0')
                                        <span class="badge bg-secondary">Non publié</span>
                                    @endif
                                </p>
                                <p class="list-group-item d-flex align-items-center">
                                    <i class="bx bx-layer me-2"></i>
                                    <strong>Formation associée :</strong> {{ $formation->titre }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <p><strong><i class="bx bx-detail me-2"></i> Description :</strong></p>
                                    <p>{!! $catalogueFormations->description ?? 'Aucune description fournie.' !!}</p>
                                </div>
                            </div>

                            @if ($catalogueFormations->image_url)
                                @php
                                    $extension = strtolower(
                                        pathinfo($catalogueFormations->image_url, PATHINFO_EXTENSION),
                                    );
                                @endphp

                                <div class="d-flex justify-content-center mt-4">
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ asset($catalogueFormations->image_url) }}" alt="Image de la formation"
                                            class="img-fluid rounded shadow-lg" style="max-height: 250px;">
                                    @elseif (in_array($extension, ['mp4', 'webm', 'ogg']))
                                        <video controls class="rounded shadow-lg" style="max-height: 250px; width: auto;">
                                            <source src="{{ asset('storage/' . $catalogueFormations->image_url) }}"
                                                type="video/{{ $extension }}">
                                            Votre navigateur ne supporte pas la lecture de vidéos.
                                        </video>
                                    @elseif (in_array($extension, ['mp3', 'wav', 'ogg']))
                                        <div class="card p-3 rounded shadow-lg"
                                            style="max-width: 400px; background: linear-gradient(135deg, #fbdfa1, #ffb923); ">
                                            <div class="mb-2 text-center">
                                                <i class="bi bi-music-note-beamed" style="font-size: 2rem; color: #4a4a4a;"></i>
                                            </div>
                                            <audio controls>
                                                <source src="{{ asset($catalogueFormations->image_url) }}"
                                                    type="audio/{{ $extension }}">
                                                Votre navigateur ne supporte pas la lecture d'audio.
                                            </audio>
                                        </div>
                                    @else
                                        <a href="{{ asset($catalogueFormations->image_url) }}" target="_blank"
                                            class="btn btn-outline-primary">
                                            Télécharger le fichier
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning text-center mt-4">
                                    Aucune image n'est disponible pour cette formation.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Cursus PDF</label>
                        @if($catalogueFormations->cursus_pdf)
                            <div class="mt-2 space-y-4">
                                <div class="d-flex gap-2 flex-wrap align-items-center mb-2">
                                    <a href="{{ asset($catalogueFormations->cursus_pdf) }}" target="_blank"
                                        class="btn btn-indigo text-white d-flex align-items-center">
                                        <i class="bx bx-link-external me-2"></i> Ouvrir le PDF
                                    </a>
                                    <a href="{{ route('catalogue_formation.download-pdf', $catalogueFormations->id) }}"
                                        class="btn btn-outline-indigo d-flex align-items-center">
                                        <i class="bx bx-download me-2"></i> Télécharger le PDF
                                    </a>
                                </div>
                                <div class="mt-4">
                                    <iframe src="{{ asset($catalogueFormations->cursus_pdf) }}"
                                        style="width:100%;min-height:400px;max-height:600px;border:1px solid #e5e7eb;border-radius:0.5rem;box-shadow:0 2px 8px rgba(0,0,0,0.05);"
                                        title="PDF Viewer"
                                        allowfullscreen>
                                    </iframe>
                                    <div class="text-center text-muted mt-2" style="font-size:0.95em;">
                                        Si le PDF ne s'affiche pas, <a href="{{ asset($catalogueFormations->cursus_pdf) }}" target="_blank">cliquez ici pour l'ouvrir dans un nouvel onglet</a>.
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="mt-2 text-gray-500">Aucun PDF</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
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
                initComplete: function () {
                    this.api().columns().every(function () {
                        var that = this;
                        $('input', this.header()).on('keyup change clear', function () {
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
