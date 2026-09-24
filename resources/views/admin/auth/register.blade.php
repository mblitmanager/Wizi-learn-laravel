@extends('admin.auth.layout')

@section('title', 'Inscription')
@section('subtitle', 'Créez votre compte d\'administration')

@section('content')
    <form action="{{ route('register.post') }}" method="POST" novalidate>
        @csrf

        <div class="login-form-group">
            <label for="name" class="login-form-label">Nom complet</label>
            <div class="login-input-group">
                <i class="bi bi-person input-icon"></i>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="login-input @error('name') is-invalid @enderror" placeholder="Votre nom"
                    autocomplete="name" autofocus required>
            </div>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-form-group">
            <label for="email" class="login-form-label">Adresse e-mail</label>
            <div class="login-input-group">
                <i class="bi bi-envelope input-icon"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="login-input @error('email') is-invalid @enderror" placeholder="nom@entreprise.fr"
                    autocomplete="email" required>
            </div>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-form-group">
            <label for="password" class="login-form-label">Mot de passe</label>
            <div class="login-input-group">
                <i class="bi bi-shield-lock input-icon"></i>
                <input type="password" id="password" name="password"
                    class="login-input login-input-password @error('password') is-invalid @enderror"
                    placeholder="••••••••" autocomplete="new-password" required>
                <button type="button" class="password-toggle-btn" aria-label="Afficher le mot de passe">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-form-group">
            <label for="password_confirmation" class="login-form-label">Confirmer le mot de passe</label>
            <div class="login-input-group">
                <i class="bi bi-shield-check input-icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="login-input login-input-password" placeholder="••••••••" autocomplete="new-password"
                    required>
                <button type="button" class="password-toggle-btn" aria-label="Afficher le mot de passe">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <span>Créer mon compte</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>

    <p class="login-footer-text">
        Vous avez déjà un compte ? <a href="{{ route('login') }}">Connectez-vous</a>
    </p>
@endsection
