<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" base_url="{!! url('/') !!}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset(siteSettings()['favicon'] ?? ecommerceIcon()) }}">
    <link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/js/jquery_ui/jquery-ui.css') }}" />
    {{-- Datetimepicker --}}
    <link rel="stylesheet" href="{{ asset('assets/common/datetimepicker/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/common/datetimepicker/css/tempusdominus-bootstrap-4.min.css') }}"
        crossorigin="anonymous" />

    {{-- Slim select     --}}
    <link href="{{ asset('assets/admin/partial/css/slimselect.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/admin/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/css/custom.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    @stack('css')
</head>

<body data-sidebar="light" data-layout-mode="light" data-topbar="light">
    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>

    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <div class="navbar-brand-box">
                        <a href="{{ route('home') }}" target="_blank" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{ asset(siteSettings()['favicon'] ?? ecommerceIcon()) }}" alt=""
                                    height="50">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset(siteSettings()['logo'] ?? devLogo()) }}" alt=""
                                    height="50">
                            </span>
                        </a>

                        <a href="{{ route('home') }}" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="{{ asset(siteSettings()['favicon'] ?? ecommerceIcon()) }}" alt=""
                                    height="50">
                            </span>
                            <span class="logo-lg">
                                <img src="{{ asset(siteSettings()['logo'] ?? devLogo()) }}" alt=""
                                    height="50">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect"
                        id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>

                </div>

                <div class="d-flex">

                    <div class="dropdown d-none d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect"
                            data-bs-toggle="fullscreen">
                            <i class="bx bx-fullscreen"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user"
                                src="{{ asset(authUser()->image ?? userAvatar()) }}" alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1">{{ authUser()->name ?? 'Admin' }}</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="bx bx-user font-size-16 align-middle me-1"></i>
                                <span>Profile</span>
                            </a>

                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="{{ route('admin.logout') }}">
                                <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                @include('admin.layouts.sidebar')
            </div>
        </div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <x-dismissible-alert></x-dismissible-alert>
                    @yield('content')
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                {{ siteSettings()['company_name'] ?? 'Company Name' }}
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- Some common modal --}}
    {{-- Image preview modal --}}
    <div class="modal fade" id="viewImageModal" tabindex="-1" aria-labelledby="viewImageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewImageModalLabel">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Image Preview" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Data view modal --}}
    <div class="modal fade" id="data-view-modal" tabindex="-1" aria-labelledby="dataViewModal" aria-hidden="true">
        <div class="modal-dialog" id="data-view-modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title data-view-modal-header" id="dataViewModal"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="data-view-modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    {{-- Scripts --}}
    <script src="{{ asset('assets/admin/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/ck-editor/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/admin/js/axios.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery_ui/jquery-ui.min.js') }}"></script>
    {{-- Datetimepicker --}}
    <script src="{{ asset('assets/common/datetimepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/common/datetimepicker/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/common/datetimepicker/js/moment-timezone-with-data.min.js') }}"></script>
    <script src="{{ asset('assets/common/datetimepicker/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <script src="{{ asset('assets/common/datetimepicker/js/custom_picker.js') }}"></script>
    <script src="{{ asset('assets/admin/partial/js/form_validation.js') }}"></script>
    {{-- Slim Select  --}}
    <script src="{{ asset('assets/admin/partial/js/slimselect.min.js') }}"></script>
    <script src="{{ asset('assets/admin/partial/js/slimselect.js') }}"></script>


    {{-- Custom created js --}}
    <script src="{{ asset('assets/admin/js/helpers.js') }}"></script>
    {{-- Page wise js --}}
    <script src="{{ asset('assets/admin/js/common.js') }}"></script>
    <script src="{{ asset('assets/admin/js/app.js') }}"></script>

    @stack('js')
</body>

</html>
