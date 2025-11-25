@extends('admin.layout')
@section('title', 'Modifier un Utilisateur')
@section('content')
    <div class="container-fluid">
        <!-- En-tête de page -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div
                    class="page-breadcrumb d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                    <div class="mb-3 mb-md-0">
                        <h5 class="card-title mb-1 text-primary">
                            <i class="bx bx-edit me-2"></i>Modification d'un utilisateur
                        </h5>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('parametre.index') }}" class="text-decoration-none">
                                        <i class="bx bx-home-alt"></i> Paramétrages
                                    </a>
                                </li>
                                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">
                                    Modifier l'utilisateur
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('parametre.index') }}" class="btn btn-outline-primary">
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

        @if (session('error'))
            <div class="alert alert-danger border-0 alert-dismissible fade show shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <i class="bx bx-error-circle me-2 fs-5"></i>
                    <span class="fw-medium">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form class="row g-3" action="{{ route('parametre.update', $user->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="alert alert-danger border-0 alert-dismissible fade show mb-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-error-circle me-2 fs-5"></i>
                                        <span class="fw-medium">Veuillez corriger les erreurs ci-dessous</span>
                                    </div>
                                    <ul class="mt-2 mb-0 ps-4">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Section Informations de base -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-user me-2"></i>Informations de base
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label fw-semibold text-dark">Nom</label>
                                                <input type="text" name="name" id="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    value="{{ old('name', $user->name) }}" placeholder="Entrez le nom">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label fw-semibold text-dark">Adresse
                                                    e-mail</label>
                                                <input type="email" name="email" id="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    value="{{ old('email', $user->email) }}"
                                                    placeholder="email@exemple.com">
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="role" class="form-label fw-semibold text-dark">Rôle</label>
                                                <select name="role" id="role"
                                                    class="form-select @error('role') is-invalid @enderror"
                                                    onchange="toggleRoleFields()">
                                                    <option value="">Sélectionner un rôle</option>
                                                    <option value="administrateur"
                                                        {{ old('role', $user->role) == 'administrateur' ? 'selected' : '' }}>
                                                        Administrateur</option>
                                                    <option value="stagiaire"
                                                        {{ old('role', $user->role) == 'stagiaire' ? 'selected' : '' }}>
                                                        Stagiaire</option>
                                                    <option value="formateur"
                                                        {{ old('role', $user->role) == 'formateur' ? 'selected' : '' }}>
                                                        Formateur</option>
                                                    <option value="commercial"
                                                        {{ old('role', $user->role) == 'commercial' ? 'selected' : '' }}>
                                                        Commercial</option>
                                                    <option value="pole relation client"
                                                        {{ old('role', $user->role) == 'pole relation client' ? 'selected' : '' }}>
                                                        Pôle relation client</option>
                                                </select>
                                                @error('role')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="image" class="form-label fw-semibold text-dark">Photo de
                                                    profil</label>
                                                <input type="file" name="image" id="image"
                                                    class="form-control @error('image') is-invalid @enderror"
                                                    accept="image/*">
                                                <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB
                                                </div>
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror

                                                @if ($user->image)
                                                    <div class="mt-2">
                                                        <span class="fw-medium text-dark">Photo actuelle :</span>
                                                        <img src="{{ asset($user->image) }}" alt="Image actuelle"
                                                            class="ms-2 rounded"
                                                            style="width: 60px; height: 60px; object-fit: cover;">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Sécurité -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-lock me-2"></i>Sécurité
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label fw-semibold text-dark">Nouveau mot
                                                    de passe</label>
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="●●●●●●●●">
                                                <div class="form-text">Laisser vide pour ne pas modifier le mot de passe
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password_confirmation"
                                                    class="form-label fw-semibold text-dark">Confirmation du mot de
                                                    passe</label>
                                                <input type="password" name="password_confirmation"
                                                    id="password_confirmation" class="form-control"
                                                    placeholder="●●●●●●●●">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Informations stagiaire -->
                            <div class="card border-0 bg-light mb-4" id="stagiaire-fields"
                                style="{{ old('role', $user->role) == 'stagiaire' ? '' : 'display:none;' }}">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-user-circle me-2"></i>Informations du stagiaire
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="civilite"
                                                    class="form-label fw-semibold text-dark">Civilité</label>
                                                <input type="text" name="civilite" id="civilite"
                                                    class="form-control"
                                                    value="{{ old('civilite', $user->stagiaire->civilite ?? '') }}"
                                                    placeholder="M., Mme, Mlle">
                                            </div>
                                            <div class="mb-3">
                                                <label for="prenom"
                                                    class="form-label fw-semibold text-dark">Prénom</label>
                                                <input type="text" name="prenom" id="prenom" class="form-control"
                                                    value="{{ old('prenom', $user->stagiaire->prenom ?? '') }}"
                                                    placeholder="Entrez le prénom">
                                            </div>
                                            <div class="mb-3">
                                                <label for="telephone"
                                                    class="form-label fw-semibold text-dark">Téléphone</label>
                                                <input type="text" name="telephone" id="telephone"
                                                    class="form-control"
                                                    value="{{ old('telephone', $user->stagiaire->telephone ?? '') }}"
                                                    placeholder="+33 ...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="date_naissance" class="form-label fw-semibold text-dark">Date
                                                    de naissance</label>
                                                <input type="date" name="date_naissance" id="date_naissance"
                                                    class="form-control"
                                                    value="{{ old('date_naissance', $user->stagiaire->date_naissance ?? '') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="adresse"
                                                    class="form-label fw-semibold text-dark">Adresse</label>
                                                <input type="text" name="adresse" id="adresse" class="form-control"
                                                    value="{{ old('adresse', $user->stagiaire->adresse ?? '') }}"
                                                    placeholder="Entrez l'adresse">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="code_postal"
                                                            class="form-label fw-semibold text-dark">Code postal</label>
                                                        <input type="text" name="code_postal" id="code_postal"
                                                            class="form-control"
                                                            value="{{ old('code_postal', $user->stagiaire->code_postal ?? '') }}"
                                                            placeholder="75000">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="ville"
                                                            class="form-label fw-semibold text-dark">Ville</label>
                                                        <input type="text" name="ville" id="ville"
                                                            class="form-control"
                                                            value="{{ old('ville', $user->stagiaire->ville ?? '') }}"
                                                            placeholder="Entrez la ville">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Informations autres rôles -->
                            <div class="card border-0 bg-light mb-4" id="other-role-fields"
                                style="{{ in_array(old('role', $user->role), ['formateur', 'commercial', 'pole relation client']) ? '' : 'display:none;' }}">
                                <div class="card-header bg-transparent border-bottom-0 py-3">
                                    <h6 class="mb-0 text-dark fw-semibold">
                                        <i class="bx bx-id-card me-2"></i>Informations complémentaires
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="prenom_other"
                                                    class="form-label fw-semibold text-dark">Prénom</label>
                                                <input type="text" name="prenom" id="prenom_other"
                                                    class="form-control"
                                                    value="{{ old('prenom', $user->formateur->prenom ?? ($user->commercial->prenom ?? ($user->poleRelationClient->prenom ?? ''))) }}"
                                                    placeholder="Entrez le prénom">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Boutons de soumission -->
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 me-3">
                                    <i class="bx bx-save me-2"></i> Mettre à jour
                                </button>
                                <a href="{{ route('parametre.index') }}" class="btn btn-outline-secondary px-5 py-2">
                                    <i class="bx bx-x me-2"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleRoleFields() {
            const role = document.getElementById('role').value;

            // Gestion des champs stagiaire
            const stagiaireFields = document.getElementById('stagiaire-fields');
            if (stagiaireFields) {
                stagiaireFields.style.display = role === 'stagiaire' ? 'block' : 'none';
            }

            // Gestion des champs autres rôles
            const otherRoleFields = document.getElementById('other-role-fields');
            if (otherRoleFields) {
                otherRoleFields.style.display = ['formateur', 'commercial', 'pole relation client'].includes(role) ?
                    'block' : 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleRoleFields(); // Exécuter au chargement

            // Validation des mots de passe
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');

            function validatePassword() {
                if (password.value && password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Les mots de passe ne correspondent pas");
                } else {
                    confirmPassword.setCustomValidity("");
                }
            }

            password.addEventListener('change', validatePassword);
            confirmPassword.addEventListener('keyup', validatePassword);
        });
    </script>

    <style>
        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .alert {
            border-radius: 10px;
        }

        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
@endsection
