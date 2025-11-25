@extends('admin.layout')
@section('title', 'Détails de l\'Utilisateur')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-user-circle me-2"></i>Détails de l'utilisateur
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('parametre.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Paramétrages
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Détails utilisateur
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('parametre.edit', $user->id) }}" class="btn btn-outline-warning">
                            <i class="bx bx-edit me-1"></i> Modifier
                        </a>
                        <a href="{{ route('parametre.index') }}" class="btn btn-outline-primary ms-2">
                            <i class="bx bx-arrow-back me-1"></i> Retour
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

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Section Profil -->
                            <div class="col-md-4 mb-4">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-user me-2"></i>Profil
                                        </h6>
                                    </div>
                                    <div class="card-body text-center p-4">
                                        <form action="{{ route('parametre.updateImage', $user->id) }}" method="POST"
                                            enctype="multipart/form-data" id="updateImageForm">
                                            @csrf
                                            @method('PUT')

                                            <label for="imageInput" class="profile-image-container">
                                                <img src="{{ $user->image ? asset($user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0D8ABC&color=fff&size=200' }}"
                                                    class="rounded-circle shadow profile-image" alt="Photo de profil"
                                                    id="profileImage">
                                                <span class="profile-image-overlay">
                                                    <i class="bx bx-camera"></i>
                                                </span>
                                            </label>
                                            <input type="file" name="image" id="imageInput" class="d-none"
                                                accept="image/*"
                                                onchange="document.getElementById('updateImageForm').submit();">
                                        </form>

                                        <h4 class="mt-4 mb-2 fw-bold text-dark">{{ $user->name }}</h4>
                                        <span
                                            class="badge 
                                            @if ($user->role === 'administrateur') bg-danger
                                            @elseif($user->role === 'formateur') bg-warning text-dark
                                            @elseif($user->role === 'stagiaire') bg-info
                                            @elseif($user->role === 'commercial') bg-success
                                            @else bg-secondary @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>

                                        <div class="mt-4">
                                            <h5 class="fw-semibold text-dark mb-3">
                                                @if ($user->stagiaire && $user->stagiaire->civilite)
                                                    {{ $user->stagiaire->civilite }}.
                                                @endif
                                                {{ $user->name }}
                                                @if ($user->stagiaire && $user->stagiaire->prenom)
                                                    {{ $user->stagiaire->prenom }}
                                                @endif
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Informations personnelles -->
                            <div class="col-md-8">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-info-circle me-2"></i>Informations personnelles
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <span class="fw-semibold text-dark">Nom :</span>
                                            </div>
                                            <div class="col-sm-8">
                                                <span class="text-muted">{{ $user->name }}</span>
                                            </div>
                                        </div>

                                        @if ($user->stagiaire && $user->stagiaire->prenom)
                                            <div class="row mb-3">
                                                <div class="col-sm-4">
                                                    <span class="fw-semibold text-dark">Prénom :</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="text-muted">{{ $user->stagiaire->prenom }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <span class="fw-semibold text-dark">Adresse email :</span>
                                            </div>
                                            <div class="col-sm-8">
                                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                    {{ $user->email }}
                                                </a>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <span class="fw-semibold text-dark">Rôle :</span>
                                            </div>
                                            <div class="col-sm-8">
                                                <span
                                                    class="badge 
                                                    @if ($user->role === 'administrateur') bg-danger
                                                    @elseif($user->role === 'formateur') bg-warning text-dark
                                                    @elseif($user->role === 'stagiaire') bg-info
                                                    @elseif($user->role === 'commercial') bg-success
                                                    @else bg-secondary @endif">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </div>
                                        </div>

                                        @if ($user->stagiaire)
                                            <div class="row mb-3">
                                                <div class="col-sm-4">
                                                    <span class="fw-semibold text-dark">Téléphone :</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span
                                                        class="text-muted">{{ $user->stagiaire->telephone ?? '-' }}</span>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-sm-4">
                                                    <span class="fw-semibold text-dark">Adresse :</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="text-muted">{{ $user->stagiaire->adresse ?? '-' }}</span>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-sm-4">
                                                    <span class="fw-semibold text-dark">Date de naissance :</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="text-muted">
                                                        {{ $user->stagiaire->date_naissance
                                                            ? \Carbon\Carbon::parse($user->stagiaire->date_naissance)->format('d/m/Y')
                                                            : '-' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-sm-4">
                                                    <span class="fw-semibold text-dark">Ville :</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span class="text-muted">{{ $user->stagiaire->ville ?? '-' }}</span>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-sm-4">
                                                    <span class="fw-semibold text-dark">Code postal :</span>
                                                </div>
                                                <div class="col-sm-8">
                                                    <span
                                                        class="text-muted">{{ $user->stagiaire->code_postal ?? '-' }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Informations système -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-bottom-0 py-3">
                                        <h6 class="mb-0 text-dark fw-semibold">
                                            <i class="bx bx-time me-2"></i>Informations système
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <span class="fw-semibold text-dark">Date de création :</span>
                                                    <br>
                                                    <span class="text-muted">
                                                        {{ $user->created_at->format('d/m/Y à H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <span class="fw-semibold text-dark">Dernière modification :</span>
                                                    <br>
                                                    <span class="text-muted">
                                                        {{ $user->updated_at->format('d/m/Y à H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center py-3">
                                        <div class="btn-group">
                                            <a href="{{ route('parametre.edit', $user->id) }}"
                                                class="btn btn-warning px-4">
                                                <i class="bx bx-edit me-2"></i> Modifier l'utilisateur
                                            </a>
                                            <form action="{{ route('parametre.destroy', $user->id) }}" method="POST"
                                                class="d-inline ms-2"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger px-4">
                                                    <i class="bx bx-trash me-2"></i> Supprimer
                                                </button>
                                            </form>
                                            <a href="{{ route('parametre.index') }}"
                                                class="btn btn-outline-secondary px-4 ms-2">
                                                <i class="bx bx-list-ul me-2"></i> Voir tous les utilisateurs
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .badge {
            border-radius: 6px;
            font-weight: 500;
        }

        .alert {
            border-radius: 10px;
        }

        .profile-image-container {
            cursor: pointer;
            position: relative;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .profile-image-container:hover {
            transform: scale(1.05);
        }

        .profile-image {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .profile-image-overlay {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #fff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .profile-image-container:hover .profile-image-overlay {
            background: #0d6efd;
            color: white;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@endsection
