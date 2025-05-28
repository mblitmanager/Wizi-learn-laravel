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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sono:wght@200..800&display=swap"
        rel="stylesheet">
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
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
    <!-- Include Bootstrap and Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.20.0/js/mdb.min.js" --}}
    {{-- integrity="sha512-XFd1m0eHgU1F05yOmuzEklFHtiacLVbtdBufAyZwFR0zfcq7vc6iJuxerGPyVFOXlPGgM8Uhem9gwzMI8SJ5uw==" --}}
    {{-- crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}

    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.20.0/css/mdb.min.css" --}}
    {{-- integrity="sha512-hj9rznBPdFg9A4fACbJcp4ttzdinMDtPrtZ3gBD11DiY3O1xJfn0r1U5so/J0zwfGOzq9teIaH5rFmjFAFw8SA==" --}}
    {{-- crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->

    <title>Wizi Learn</title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        {{-- <div id="globalLoader" class="hourglassOverlay d-none">
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
        {{-- <div class="switcher-wrapper">
                <div class="switcher-btn"> <i class='bx bx-cog bx-spin'></i>
                </div>
                <div class="switcher-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-uppercase">Theme Customizer</h5>
                        <button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
                    </div>
                    <hr/>
                    <h6 class="mb-0">Theme Styles</h6>
                    <hr/>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode" checked>
                            <label class="form-check-label" for="lightmode">Light</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
                            <label class="form-check-label" for="darkmode">Dark</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
                            <label class="form-check-label" for="semidark">Semi Dark</label>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="minimaltheme" name="flexRadioDefault">
                        <label class="form-check-label" for="minimaltheme">Minimal Theme</label>
                    </div>
                    <hr/>
                    <h6 class="mb-0">Header Colors</h6>
                    <hr/>
                    <div class="header-colors-indigators">
                        <div class="row row-cols-auto g-3">
                            <div class="col">
                                <div class="indigator headercolor1" id="headercolor1"></div>
                            </div>
                            <div class="col">
                                <div class="indigator headercolor2" id="headercolor2"></div>
                            </div>
                            <div class="col">
                                <div class="indigator headercolor3" id="headercolor3"></div>
                            </div>
                            <div class="col">
                                <div class="indigator headercolor4" id="headercolor4"></div>
                            </div>
                            <div class="col">
                                <div class="indigator headercolor5" id="headercolor5"></div>
                            </div>
                            <div class="col">
                                <div class="indigator headercolor6" id="headercolor6"></div>
                            </div>
                            <div class="col">
                                <div class="indigator headercolor7" id="headercolor7"></div>
                            </div>
                            <div class="col">
                                <div class="indigator headercolor8" id="headercolor8"></div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <h6 class="mb-0">Sidebar Colors</h6>
                    <hr/>
                    <div class="header-colors-indigators">
                        <div class="row row-cols-auto g-3">
                            <div class="col">
                                <div class="indigator sidebarcolor1" id="sidebarcolor1"></div>
                            </div>
                            <div class="col">
                                <div class="indigator sidebarcolor2" id="sidebarcolor2"></div>
                            </div>
                            <div class="col">
                                <div class="indigator sidebarcolor3" id="sidebarcolor3"></div>
                            </div>
                            <div class="col">
                                <div class="indigator sidebarcolor4" id="sidebarcolor4"></div>
                            </div>
                            <div class="col">
                                <div class="indigator sidebarcolor5" id="sidebarcolor5"></div>
                            </div>
                            <div class="col">
                                <div class="indigator sidebarcolor6" id="sidebarcolor6"></div>
                            </div>
                            <div class="col">
                                <div class="indigator sidebarcolor7" id="sidebarcolor7"></div>
                            </div>
                            <div class="col">
                                <div class="indigator sidebarcolor8" id="sidebarcolor8"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        <!--end switcher-->
    </div>
    <!--end wrapper-->
    <!-- Bootstrap JS -->
    @yield('scripts')
    @include('admin.partials.scripts')
</body>

</html>