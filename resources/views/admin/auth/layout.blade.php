<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Connexion') — Wizi Learn</title>
    <link rel="icon" href="{{ asset('assets/images/logowizi.png') }}" type="image/png" />
    <link rel="stylesheet" href="{{ asset('assets/spark/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/spark/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/spark/css/spark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/wizi-theme.css') }}">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-bg-shape login-bg-shape-1"></div>
        <div class="login-bg-shape login-bg-shape-2"></div>

        <div class="login-card">
            <a href="{{ route('login') }}" class="login-brand text-decoration-none">
                <img src="{{ asset('assets/images/logowizi.png') }}" alt="Wizi Learn">
            </a>

            @hasSection('subtitle')
                <p class="login-subtitle">@yield('subtitle')</p>
            @endif

            @if (session('status'))
                <div class="alert alert-success" role="alert">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="{{ asset('assets/spark/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Afficher / masquer les mots de passe
        document.querySelectorAll('.password-toggle-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = button.parentElement.querySelector('input');
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                button.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
                button.setAttribute('aria-label', visible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
            });
        });
    </script>
</body>

</html>
