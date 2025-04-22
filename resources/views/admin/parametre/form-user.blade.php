{{-- Champs Nom --}}
<div class="mb-3">
    <label for="name" class="form-label">Nom</label>
    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="form-control @error('name') is-invalid @enderror">
    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
</div>

{{-- Email --}}
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="form-control @error('email') is-invalid @enderror">
    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
</div>

{{-- Mot de passe --}}
<div class="mb-3">
    <label for="password" class="form-label">Mot de passe</label>
    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
    @if (isset($edit) && $edit)
        <small class="text-muted">Laisser vide si vous ne voulez pas modifier le mot de passe</small>
    @endif
    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
</div>

{{-- Rôle --}}
<div class="mb-3">
    <label for="role" class="form-label">Rôle</label>
    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" onchange="toggleRoleFields()">
        <option value="">-- Sélectionner un rôle --</option>
        <option value="administrateur" {{ old('role', $user->role ?? '') == 'administrateur' ? 'selected' : '' }}>Administrateur</option>
        <option value="stagiaire" {{ old('role', $user->role ?? '') == 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
        <option value="formateur" {{ old('role', $user->role ?? '') == 'formateur' ? 'selected' : '' }}>Formateur</option>
        <option value="commercial" {{ old('role', $user->role ?? '') == 'commercial' ? 'selected' : '' }}>Commercial</option>
        <option value="pole relation client" {{ old('role', $user->role ?? '') == 'pole relation client' ? 'selected' : '' }}>Pôle relation client</option>
    </select>
    @error('role') <div class="text-danger">{{ $message }}</div> @enderror
</div>

{{-- Image --}}
<div class="mb-3">
    <label for="image" class="form-label">Image de profil</label>
    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image">
    @error('image') <div class="text-danger">{{ $message }}</div> @enderror

    @if(isset($user) && $user->image)
        <img src="{{ asset($user->image) }}" alt="Image actuelle" class="mt-2" style="width: 100px; height: auto;">
    @endif
</div>

{{-- Champs spécifiques au stagiaire --}}
<div id="stagiaire-fields" style="{{ old('role', $user->role ?? '') == 'stagiaire' ? '' : 'display:none;' }}">
    <div class="mb-3">
        <label for="civilite" class="form-label">Civilité</label>
        <input type="text" name="civilite" class="form-control" value="{{ old('civilite', $user->stagiaire->civilite ?? '') }}">
    </div>

    <div class="mb-3">
        <label for="prenom" class="form-label">Prénom</label>
        <input type="text" name="prenom" class="form-control" value="{{ old('prenom', $user->stagiaire->prenom ?? '') }}">
    </div>

    <div class="mb-3">
        <label for="telephone" class="form-label">Téléphone</label>
        <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $user->stagiaire->telephone ?? '') }}">
    </div>

    <div class="mb-3">
        <label for="adresse" class="form-label">Adresse</label>
        <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $user->stagiaire->adresse ?? '') }}">
    </div>

    <div class="mb-3">
        <label for="date_naissance" class="form-label">Date de naissance</label>
        <input type="date" name="date_naissance" class="form-control" value="{{ old('date_naissance', $user->stagiaire->date_naissance ?? '') }}">
    </div>

    <div class="mb-3">
        <label for="ville" class="form-label">Ville</label>
        <input type="text" name="ville" class="form-control" value="{{ old('ville', $user->stagiaire->ville ?? '') }}">
    </div>

    <div class="mb-3">
        <label for="code_postal" class="form-label">Code postal</label>
        <input type="text" name="code_postal" class="form-control" value="{{ old('code_postal', $user->stagiaire->code_postal ?? '') }}">
    </div>
</div>

{{-- Prénom pour les autres rôles --}}
<div id="other-role-fields" style="{{ in_array(old('role', $user->role ?? ''), ['formateur', 'commercial', 'pole relation client']) ? '' : 'display:none;' }}">
    <div class="mb-3">
        <label for="prenom" class="form-label">Prénom</label>
        <input type="text" name="prenom" class="form-control" value="{{ old('prenom', $user->formateur->prenom ?? $user->commercial->prenom ?? $user->poleRelationClient->prenom ?? '') }}">
    </div>
</div>
