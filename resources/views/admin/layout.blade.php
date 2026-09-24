<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Wizi Learn')</title>
    <link rel="icon" href="{{ asset('assets/images/logowizi.png') }}" type="image/png" />

    {{-- Thème Spark Admin (Bootstrap 5.3) aux couleurs Wizi --}}
    <link rel="stylesheet" href="{{ asset('assets/spark/libs/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/spark/libs/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/spark/css/spark.css') }}">

    {{-- Icônes utilisées par les vues (boxicons, lineicons, Font Awesome) --}}
    <link rel="stylesheet" href="{{ asset('assets/css/icons.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Plugins --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>

    @stack('styles')
    {{-- Couleurs de marque + compatibilité des vues : toujours en dernier --}}
    <link rel="stylesheet" href="{{ asset('assets/css/wizi-theme.css') }}">
</head>

<body>
    @include('admin.partials.sidebar')

    <div class="main-wrapper">
        @include('admin.partials.header')

        <main class="page-content">
            @yield('content')
        </main>

        <footer class="footer-custom">
            <div class="footer-left">
                <span class="footer-logo">
                    <img src="{{ asset('assets/images/logowizi.png') }}" alt="Wizi Learn">
                </span>
                <span class="footer-separator">|</span>
                <span class="footer-copy">&copy; {{ date('Y') }} Wizi Learn. Tous droits réservés.</span>
            </div>
        </footer>
    </div>

    <a href="#" class="back-to-top" aria-label="Retour en haut"><i class="bi bi-arrow-up"></i></a>

    @yield('manual-scripts')
    @yield('scripts')
    @include('admin.partials.scripts')
    @stack('scripts')
</body>

</html>
