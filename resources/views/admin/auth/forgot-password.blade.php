@extends('admin.auth.layout')

@section('title', 'Mot de passe oublié')
@section('subtitle', 'Saisissez votre adresse e-mail : nous vous enverrons un lien de réinitialisation.')

@section('content')
    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="login-form-group">
            <label for="email" class="login-form-label">Adresse e-mail</label>
            <div class="login-input-group">
                <i class="bi bi-envelope input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="login-input @error('email') is-invalid @enderror" placeholder="nom@entreprise.fr"
                    autocomplete="email" autofocus required>
            </div>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-login">
            <span>Envoyer le lien de réinitialisation</span>
            <i class="bi bi-send"></i>
        </button>
    </form>

    <p class="login-footer-text">
        <a href="{{ route('login') }}">Retour à la connexion</a>
    </p>
@endsection
