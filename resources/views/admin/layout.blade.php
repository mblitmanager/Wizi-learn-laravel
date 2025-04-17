<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('assets/images/logowizi.png') }}" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <!-- loader-->
    <!-- <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/pace.min.js') }}"></script> -->
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/header-colors.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 CSS -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


    <title>Wize Learn</title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <div id="globalLoader" class="hourglassOverlay d-none">
            <div class="hourglassBackground">
                <div class="hourglassContainer">
                    <div class="hourglassCurves"></div>
                    <div class="hourglassCapTop"></div>
                    <div class="hourglassGlassTop"></div>
                    <div class="hourglassSand"></div>
                    <div class="hourglassSandStream"></div>
                    <div class="hourglassCapBottom"></div>
                    <div class="hourglassGlass"></div>
                </div>
            </div>
        </div>

        {{-- <div id="globalLoader" class="hourglassOverlay d-block">
            <div class="hourglassBackground">
                <div class="hourglassContainer">
                    <div class="hourglassCurves"></div>
                    <div class="hourglassCapTop"></div>
                    <div class="hourglassGlassTop"></div>
                    <div class="hourglassSand"></div>
                    <div class="hourglassSandStream"></div>
                    <div class="hourglassCapBottom"></div>
                    <div class="hourglassGlass"></div>
                </div>
            </div>
        </div> --}}


        <!--sidebar wrapper -->
        <div class="sidebar-wrapper" data-simplebar="true">
            @include('admin.partials.sidebar')
        </div>
        <!--end sidebar wrapper -->
        <!--start header -->
        @include('admin.partials.header')
        <!--end header -->
        <!--start page wrapper -->
        <div class="page-wrapper">
            <div class="page-content">
                @yield('content')
            </div>
        </div>
        <!--end page wrapper -->
        <!--start overlay-->
        <div class="overlay toggle-icon"></div>
        <!--end overlay-->
        <!--Start Back To Top Button-->
        <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        <footer class="page-footer">
            <p class="mb-0">Copyright © 2025. Tous droits réservés.</p>
        </footer>
    </div>
    <!--end wrapper-->
    <!-- Bootstrap JS -->
    @yield('scripts')
    @include('admin.partials.scripts')
</body>

</html>
