@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-edit mr-2"></i>Modifier mon profil
                    </h5>
                </div>
                <form action="{{ route('formateur.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <!-- Photo de profil -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <div class="profile-image-wrapper mb-3">
                                    <div class="avatar-upload">
                                        <div class="avatar-edit">
                                            <input type="file" id="imageUpload" name="image" accept=".png, .jpg, .jpeg, .gif">
                                            <label for="imageUpload">
                                                <i class="fas fa-camera"></i>
                                            </label>
                                        </div>
                                        <div class="avatar-preview">
                                            <div id="imagePreview" style="background-image: url('{{ $user->image ? asset('storage/' . $user->image) : asset('assets/images/default-avatar.png') }}');">
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">Cliquez sur l'appareil photo pour changer votre photo</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prenom" class="form-label">
                                        <i class="fas fa-user text-primary mr-1"></i>Prénom *
                                    </label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                           id="prenom" name="prenom" value="{{ old('prenom', $formateur->prenom) }}" 
                                           placeholder="Votre prénom" required>
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom" class="form-label">
                                        <i class="fas fa-user text-primary mr-1"></i>Nom *
                                    </label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom', $formateur->nom) }}" 
                                           placeholder="Votre nom" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope text-primary mr-1"></i>Email *
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" 
                                           placeholder="votre@email.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone" class="form-label">
                                        <i class="fas fa-phone text-primary mr-1"></i>Téléphone
                                    </label>
                                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" 
                                           id="telephone" name="telephone" value="{{ old('telephone', $formateur->telephone) }}" 
                                           placeholder="+33 1 23 45 67 89">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="adresse" class="form-label">
                                <i class="fas fa-map-marker-alt text-primary mr-1"></i>Adresse
                            </label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                      id="adresse" name="adresse" rows="2" 
                                      placeholder="Votre adresse complète">{{ old('adresse', $user->adresse) }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Section changement de mot de passe -->
                        <div class="card card-outline card-warning mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-lock mr-2"></i>Changer le mot de passe
                                    <small class="text-muted ml-2">(Optionnel)</small>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="current_password" class="form-label">
                                        Mot de passe actuel
                                    </label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password" 
                                           placeholder="Entrez votre mot de passe actuel">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="new_password" class="form-label">
                                        Nouveau mot de passe
                                    </label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password" name="new_password" 
                                           placeholder="Entrez votre nouveau mot de passe">
                                    <small class="form-text text-muted">
                                        Minimum 8 caractères avec des chiffres et lettres
                                    </small>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation" class="form-label">
                                        Confirmer le nouveau mot de passe
                                    </label>
                                    <input type="password" class="form-control" 
                                           id="new_password_confirmation" name="new_password_confirmation" 
                                           placeholder="Confirmez votre nouveau mot de passe">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('formateur.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar des statistiques -->
        <div class="col-md-4">
            <!-- Carte profil -->
            <div class="card card-info">
                <div class="card-header text-center">
                    <h6 class="card-title mb-0">Mon Profil</h6>
                </div>
                <div class="card-body text-center">
                    <div class="profile-summary-avatar mb-3">
                        <img src="{{ $user->image ? asset($user->image) : asset('assets/images/default-avatar.png') }}" 
                             alt="Photo de profil" class="img-circle elevation-2" width="100" height="100">
                    </div>
                    <h5 class="mb-1">{{ $formateur->prenom }} {{ $formateur->nom }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <p class="text-muted mb-3">
                        <i class="fas fa-user-tag mr-1"></i>Formateur
                    </p>
                    
                    @if($user->adresse)
                    <p class="text-muted mb-3">
                        <i class="fas fa-map-marker-alt mr-1"></i>{{ Str::limit($user->adresse, 30) }}
                    </p>
                    @endif
                    
                    @if($formateur->telephone)
                    <p class="text-muted mb-3">
                        <i class="fas fa-phone mr-1"></i>{{ $formateur->telephone }}
                    </p>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card card-success mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar mr-2"></i>Mes Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item text-center mb-3">
                            <div class="stat-icon text-primary mb-2">
                                <i class="fas fa-graduation-cap fa-2x"></i>
                            </div>
                            <div class="stat-number h6 font-weight-bold text-primary">
                                {{ $formateur->catalogue_formations->count() }}
                            </div>
                            <div class="stat-label text-muted">Formations</div>
                        </div>
                        
                        <div class="stat-item text-center mb-3">
                            <div class="stat-icon text-success mb-2">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <div class="stat-number h6 font-weight-bold text-success">
                                {{ $formateur->stagiaires()->where('statut', 1)->count() }}
                            </div>
                            <div class="stat-label text-muted">Stagiaires actifs</div>
                        </div>
                        
                        <div class="stat-item text-center">
                            <div class="stat-icon text-info mb-2">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                            <div class="stat-number h6 font-weight-bold text-info">
                                {{ $formateur->stagiaires()->where('statut', 0)->count() }}
                            </div>
                            <div class="stat-label text-muted">Formations terminées</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de compte -->
            <div class="card card-warning mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle mr-2"></i>Informations du compte
                    </h6>
                </div>
                <div class="card-body">
                    <div class="account-info">
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Membre depuis :</span>
                            <span class="font-weight-bold">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Dernière connexion :</span>
                            <span class="font-weight-bold">{{ \Carbon\Carbon::parse($user->last_login_at) ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                        <div class="info-item d-flex justify-content-between">
                            <span class="text-muted">Statut :</span>
                            <span class="badge badge-success">Actif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-upload {
    position: relative;
    max-width: 150px;
    margin: 0 auto;
}
.avatar-upload .avatar-edit {
    position: absolute;
    right: 10px;
    z-index: 1;
    bottom: 10px;
}
.avatar-upload .avatar-edit input {
    display: none;
}
.avatar-upload .avatar-edit label {
    display: inline-block;
    width: 40px;
    height: 40px;
    margin-bottom: 0;
    border-radius: 100%;
    background: #FFFFFF;
    border: 1px solid transparent;
    box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
    cursor: pointer;
    font-weight: normal;
    transition: all 0.2s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-upload .avatar-edit label:hover {
    background: #f1f1f1;
    border-color: #d6d6d6;
}
.avatar-upload .avatar-edit label:after {
    color: #757575;
    font-family: 'FontAwesome';
}
.avatar-upload .avatar-preview {
    width: 150px;
    height: 150px;
    position: relative;
    border-radius: 100%;
    border: 6px solid #F8F8F8;
    box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
}
.avatar-upload .avatar-preview > div {
    width: 100%;
    height: 100%;
    border-radius: 100%;
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
}
.profile-summary-avatar img {
    border: 4px solid #dee2e6;
    object-fit: cover;
}
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.stat-item {
    padding: 15px;
    border-radius: 10px;
    background: #f8f9fa;
}
.account-info .info-item {
    border-bottom: 1px solid #f1f1f1;
    padding-bottom: 8px;
}
.account-info .info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.form-label {
    font-weight: 600;
    margin-bottom: 8px;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Preview de l'image uploadée
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').css('background-image', 'url('+e.target.result +')');
                $('.profile-summary-avatar img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#imageUpload").change(function() {
        readURL(this);
    });

    // Toggle la section mot de passe
    $('#changePasswordToggle').click(function() {
        $('#passwordSection').slideToggle();
    });

    // Validation en temps réel du téléphone
    $('#telephone').on('input', function() {
        var phone = $(this).val();
        var cleanPhone = phone.replace(/[^\d+]/g, '');
        $(this).val(cleanPhone);
    });
});
</script>
@endpush