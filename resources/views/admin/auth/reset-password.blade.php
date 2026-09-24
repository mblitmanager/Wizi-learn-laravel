@extends('admin.auth.layout')

@section('title', 'Réinitialiser le mot de passe')
@section('subtitle', 'Choisissez un nouveau mot de passe.')

@section('content')
    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="login-form-group">
            <label for="email" class="login-form-label">Adresse e-mail</label>
            <div class="login-input-group">
                <i class="bi bi-envelope input-icon"></i>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}"
                    class="login-input @error('email') is-invalid @enderror" autocomplete="email" required>
            </div>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-form-group">
            <label for="password" class="login-form-label">Nouveau mot de passe</label>
            <div class="login-input-group">
                <i class="bi bi-shield-lock input-icon"></i>
                <input id="password" type="password" name="password"
                    class="login-input login-input-password @error('password') is-invalid @enderror"
                    autocomplete="new-password" autofocus required>
                <button type="button" class="password-toggle-btn" aria-label="Afficher le mot de passe">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="login-form-group">
            <label for="password-confirm" class="login-form-label">Confirmer le mot de passe</label>
            <div class="login-input-group">
                <i class="bi bi-shield-check input-icon"></i>
                <input id="password-confirm" type="password" name="password_confirmation"
                    class="login-input login-input-password" autocomplete="new-password" required>
                <button type="button" class="password-toggle-btn" aria-label="Afficher le mot de passe">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <span>Réinitialiser le mot de passe</span>
            <i class="bi bi-arrow-right"></i>
        </button>
    </form>
@endsection
