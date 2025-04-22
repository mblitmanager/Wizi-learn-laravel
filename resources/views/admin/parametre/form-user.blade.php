<div class="mb-3">
    <label for="name" class="form-label">Nom</label>
    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" class="form-control @error('name') is-invalid @enderror">
    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" class="form-control @error('email') is-invalid @enderror">
    @error('email') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="password" class="form-label">Mot de passe</label>
    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
    @if (isset($edit) && $edit)
        <small class="text-muted">Laisser vide si vous ne voulez pas modifier le mot de passe</small>
    @endif
    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="role" class="form-label">Rôle</label>
    <select name="role" id="role" class="form-control @error('role') is-invalid @enderror">
        <option value="">-- Sélectionner un rôle --</option>
        <option value="administrateur" {{ old('role', $user->role ?? '') == 'administrateur' ? 'selected' : '' }}>Administrateur</option>
        <option value="stagiaire" {{ old('role', $user->role ?? '') == 'stagiaire' ? 'selected' : '' }}>Stagiaire</option>
        <option value="commercial" {{ old('role', $user->role ?? '') == 'commercial' ? 'selected' : '' }}>Commercial</option>
        <option value="pole relation client" {{ old('role', $user->role ?? '') == 'pole relation client' ? 'selected' : '' }}>Pole relation client</option>
    </select>
    @error('role') <div class="text-danger">{{ $message }}</div> @enderror
</div>
<div class="mb-3">
    <label for="image" class="form-label">Image de profil</label>
    <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image">\
    @error('image') <div class="text-danger">{{ $message }}</div> @enderror

@if(isset($user) && $user->image)
        <img src="{{ asset($user->image) }}" alt="Image actuelle" class="mt-2" style="width: 100px; height: auto;">
    @endif
</div>

