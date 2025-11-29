@extends('admin.layout')
@section('title', 'Gestion des médias')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Médias
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('medias.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i> Nouveau média
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        @if (session('success'))
            <div class="alert alert-success border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-check-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tableau des médias -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom-0 py-3">
                <h6 class="mb-0 text-dark fw-semibold">
                    <i class="bx bx-list-ul me-2"></i>Liste des médias
                    <span class="badge bg-info text-dark ms-2">{{ $media->total() }}</span>
                </h6>
            </div>

            <!-- Filtres -->
            <div class="card-body border-bottom pb-3">
                <form method="GET" action="{{ route('medias.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="formation" class="form-label fw-semibold text-dark">Formation</label>
                        <select name="formation" id="formation" class="form-select form-select-sm">
                            <option value="">-- Toutes les formations --</option>
                            @foreach ($formations as $formation)
                                <option value="{{ $formation->id }}"
                                    {{ request('formation') == $formation->id ? 'selected' : '' }}>
                                    {{ $formation->titre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="type" class="form-label fw-semibold text-dark">Type de média</label>
                        <select name="type" id="type" class="form-select form-select-sm">
                            <option value="">-- Tous les types --</option>
                            @foreach ($types as $typeOption)
                                <option value="{{ $typeOption }}"
                                    {{ request('type') == $typeOption ? 'selected' : '' }}>
                                    {{ ucfirst($typeOption) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="category" class="form-label fw-semibold text-dark">Catégorie</label>
                        <select name="category" id="category" class="form-select form-select-sm">
                            <option value="">-- Toutes les catégories --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}"
                                    {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ ucfirst($cat) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bx bx-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('medias.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-refresh me-1"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="mediasTable" class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-primary text-white border-0">Aperçu</th>
                                <th class="bg-primary text-white border-0">Titre</th>
                                <th class="bg-primary text-white border-0">Formation</th>
                                <th class="bg-primary text-white border-0">Catégorie</th>
                                <th class="bg-primary text-white border-0">Type</th>
                                <th class="bg-primary text-white border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($media as $row)
                                <tr>
                                    <td class="text-center">
                                        @if ($row->type === 'image')
                                            <img src="{{ asset($row->url) }}" alt="Aperçu" class="media-preview">
                                        @elseif ($row->type === 'audio')
                                            <img src="{{ asset('assets/images/mp3.png') }}" alt="Audio"
                                                class="media-preview">
                                        @elseif ($row->type === 'document')
                                            <img src="{{ asset('assets/images/des-documents.png') }}" alt="Document"
                                                class="media-preview">
                                        @elseif($row->type === 'video')
                                            <img src="{{ asset('assets/images/mp4.png') }}" alt="Vidéo"
                                                class="media-preview">
                                        @else
                                            <span class="badge bg-secondary">Type inconnu</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $row->titre }}</div>
                                    </td>
                                    <td>
                                        @if ($row->formation)
                                            <span class="badge bg-light text-dark">{{ $row->formation->titre }}</span>
                                        @else
                                            <span class="badge bg-secondary">Non assignée</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ ucfirst($row->categorie) }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge
                                            @if ($row->type === 'image') bg-success
                                            @elseif($row->type === 'audio') bg-warning text-dark
                                            @elseif($row->type === 'document') bg-info
                                            @elseif($row->type === 'video') bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst($row->type) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('medias.show', $row->id) }}" class="btn btn-info text-white">
                                                Afficher
                                            </a>
                                            <a href="{{ route('medias.edit', $row->id) }}" class="btn btn-success">
                                                Modifier
                                            </a>
                                            <form action="{{ route('medias.destroy', $row->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce média ?')">
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#mediasTable').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.5/i18n/fr-FR.json",
                    emptyTable: "Aucun média trouvé."
                },
                paging: true,
                searching: true,
                ordering: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                dom: '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"l><"col-md-6"p>>',
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn btn-sm btn-outline-secondary',
                        text: '<i class="bx bx-copy me-1"></i>Copier'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-sm btn-outline-primary',
                        text: '<i class="bx bx-file me-1"></i>CSV'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-sm btn-outline-success',
                        text: '<i class="bx bx-spreadsheet me-1"></i>Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-sm btn-outline-danger',
                        text: '<i class="bx bx-file me-1"></i>PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-sm btn-outline-info',
                        text: '<i class="bx bx-printer me-1"></i>Imprimer'
                    }
                ]
            });
        });
    </script>

    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 6px;
            font-weight: 500;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .badge {
            border-radius: 4px;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.04) !important;
        }

        .filters input {
            font-size: 0.8rem;
        }

        .dataTables_empty {
            text-align: center;
            padding: 2rem !important;
            color: #6c757d !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .btn-group-sm>.btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .btn-group-sm>button {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .media-preview {
            max-width: 60px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
