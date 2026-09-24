@extends('admin.auth.layout')

@section('title', 'Connexion')
@section('subtitle', 'Connectez-vous pour accéder à votre espace d\'administration')

@section('content')
    <form action="{{ route('login.post') }}" method="POST" novalidate>
        @csrf

        <div class="login-form-group">
            <label for="email" class="login-form-label">Adresse e-mail</label>
            <div class="login-input-group">
                <i class="bi bi-envelope input-icon"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    class="login-input @error('email') is-invalid @enderror" placeholder="nom@entreprise.fr"
                    autocomplete="email" autofocus required>
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
                    placeholder="••••••••" autocomplete="current-password" required>
                <button type="button" class="password-toggle-btn" aria-label="Afficher le mot de passe">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-options">
            <span></span>
            <a href="{{ route('password.request') }}" class="forgot-password-link">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="btn-login">
            <span>Se connecter</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>
@endsection
