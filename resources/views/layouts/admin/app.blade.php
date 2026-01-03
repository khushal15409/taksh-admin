<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <!-- Title -->
    <title>@yield('title', 'Admin Dashboard')</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <!-- Font -->
    <link href="{{asset('assets/admin/css/fonts.css')}}" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/custom.css')}}">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('assets/admin/css/owl.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/bootstrap-tour-standalone.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/emogi-area.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/intltelinput/css/intlTelInput.css')}}">
    <style>
        /* Custom Sidebar Gradient */
        .js-navbar-vertical-aside,
        .navbar-vertical-container,
        .navbar-vertical-content.bg--005555,
        .navbar-vertical-content {
            background: linear-gradient(180deg, #0177cd 0%, #0e4da3 100%) !important;
        }
        /* Ensure sidebar wrapper also has gradient */
        #sidebarMain aside {
            background: linear-gradient(180deg, #0177cd 0%, #0e4da3 100%) !important;
        }
        /* Dashboard Background Gradient */
        body.footer-offset {
            background: linear-gradient(135deg, #e0edff 0%, #d4e6ff 25%, #e8f2ff 50%, #d4e6ff 75%, #e0edff 100%) !important;
            background-attachment: fixed;
            min-height: 100vh;
        }
        /* Main content area with light gradient */
        main#content.main {
            background: linear-gradient(135deg, rgba(224, 237, 255, 0.6) 0%, rgba(212, 230, 255, 0.6) 50%, rgba(232, 242, 255, 0.6) 100%);
            min-height: calc(100vh - 60px);
        }
        /* Dashboard container with subtle gradient overlay */
        .content.container-fluid {
            background: transparent;
            position: relative;
        }
        /* Transparent Glassmorphism Cards */
        .card {
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 8px 32px rgba(1, 119, 205, 0.15), 0 2px 8px rgba(1, 119, 205, 0.1) !important;
            border-radius: 16px !important;
            transition: all 0.3s ease;
        }
        .card:hover {
            background: rgba(255, 255, 255, 0.35) !important;
            box-shadow: 0 12px 40px rgba(1, 119, 205, 0.2), 0 4px 12px rgba(1, 119, 205, 0.15) !important;
            transform: translateY(-2px);
        }
        /* Dashboard stat cards - transparent */
        .__dashboard-card-2 {
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(8px) saturate(150%);
            -webkit-backdrop-filter: blur(8px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 16px rgba(1, 119, 205, 0.12) !important;
            transition: all 0.3s ease;
        }
        .__dashboard-card-2:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 6px 20px rgba(1, 119, 205, 0.18) !important;
            transform: translateY(-3px);
        }
        /* Order cards - transparent */
        .order--card {
            background: rgba(255, 255, 255, 0.2) !important;
            backdrop-filter: blur(8px) saturate(150%);
            -webkit-backdrop-filter: blur(8px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 16px rgba(1, 119, 205, 0.12) !important;
            transition: all 0.3s ease;
        }
        .order--card:hover {
            background: rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 6px 20px rgba(1, 119, 205, 0.18) !important;
            transform: translateY(-3px);
        }
        /* Remove card body background */
        .card-body {
            background: transparent !important;
        }
        /* Page header styling */
        .page-header {
            background: rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 12px !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
        }
        /* Chart and content cards - transparent */
        .card.h-100 {
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            box-shadow: 0 8px 32px rgba(1, 119, 205, 0.15) !important;
            border-radius: 16px !important;
        }
        .card.h-100:hover {
            background: rgba(255, 255, 255, 0.35) !important;
            box-shadow: 0 12px 40px rgba(1, 119, 205, 0.2) !important;
        }
        /* Card headers - transparent */
        .card-header {
            background: rgba(255, 255, 255, 0.1) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 16px 16px 0 0 !important;
        }
        /* Table styling inside cards */
        .table {
            background: transparent !important;
        }
        .table thead th {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        .table tbody td {
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
        /* Ensure text remains readable */
        .card-title, .card-subtitle, h1, h2, h3, h4, h5, h6 {
            color: #1e293b !important;
        }
        .page-header-title, .page-header-text {
            color: #1e293b !important;
        }
    </style>
    @stack('css_or_js')
    <script src="{{asset('assets/admin/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js')}}"></script>
    <link rel="stylesheet" href="{{asset('assets/admin/css/toastr.css')}}">
</head>

<body class="footer-offset">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" class="initial-hidden">
                <div class="loader--inner">
                    <img width="200" src="{{asset('assets/admin/img/loader.gif')}}" alt="image">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Builder -->
@include('layouts.admin.partials._front-settings')
<!-- End Builder -->

<!-- JS Preview mode only -->
@include('layouts.admin.partials._header')

@include('layouts.admin.partials._sidebar')

<!-- END ONLY DEV -->

<main id="content" role="main" class="main pointer-event">
    <!-- Content -->
@yield('content')
<!-- End Content -->

    <!-- Footer -->
@include('layouts.admin.partials._footer')
<!-- End Footer -->

    <div class="modal fade" id="popup-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <h2 class="update_notification_text">
                                    <i class="tio-shopping-cart-outlined"></i> You have new order, Check Please.
                                </h2>
                                <hr>
                                <button class="btn btn-primary check-order">Ok, let me check</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="toggle-modal">
        <div class="modal-dialog status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear"></span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <img id="toggle-image" alt="" class="mb-20">
                                <h5 class="modal-title" id="toggle-title"></h5>
                            </div>
                            <div class="text-center" id="toggle-message">
                            </div>
                        </div>
                        <div class="btn--container justify-content-center">
                            <button type="button" id="toggle-ok-button" class="btn btn--primary min-w-120 confirm-Toggle" data-dismiss="modal" >Ok</button>
                            <button id="reset_btn" type="reset" class="btn btn--cancel min-w-120" data-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="toggle-status-modal">
        <div class="modal-dialog status-warning-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true" class="tio-clear"></span>
                    </button>
                </div>
                <div class="modal-body pb-5 pt-0">
                    <div class="max-349 mx-auto mb-20">
                        <div>
                            <div class="text-center">
                                <img id="toggle-status-image" alt="" class="mb-20">
                                <h5 class="modal-title" id="toggle-status-title"></h5>
                            </div>
                            <div class="text-center" id="toggle-status-message">
                            </div>
                        </div>
                        <div class="btn--container justify-content-center">
                            <button type="button" id="toggle-status-ok-button" class="btn btn--primary min-w-120 confirm-Status-Toggle" data-dismiss="modal" >Ok</button>
                            <button id="reset_btn" type="reset" class="btn btn--cancel min-w-120" data-dismiss="modal">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- ========== END MAIN CONTENT ========== -->

<!-- ========== END SECONDARY CONTENTS ========== -->
<script src="{{asset('assets/admin')}}/js/custom.js"></script>
<script src="{{asset('assets/admin')}}/js/firebase.min.js"></script>
<!-- JS Implementing Plugins -->

@stack('script')
<!-- JS Front -->

<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('assets/admin')}}/js/bootstrap-tour-standalone.min.js"></script>
<script src="{{asset('assets/admin/js/owl.min.js')}}"></script>
<script src="{{asset('assets/admin')}}/js/emogi-area.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('assets/admin/js/app-blade/admin.js')}}"></script>

<script>
    // Configure Toastr to show notifications in top-right position
    if (typeof toastr !== 'undefined') {
        toastr.options.positionClass = 'toast-top-right';
    }
</script>

@stack('script_2')
<script src="{{asset('assets/admin/js/view-pages/common.js')}}"></script>
<script src="{{asset('assets/admin/js/keyword-highlighted.js')}}"></script>

<script>
    let baseUrl = '{{ url('/') }}';
    
    // Ensure sidebar and menu items stay visible on page navigation
    $(document).ready(function() {
        // Function to maintain sidebar visibility
        function maintainSidebarVisibility() {
            // Ensure sidebar is visible on desktop
            if ($(window).width() >= 1200) {
                $('.js-navbar-vertical-aside').removeClass('d-none');
                $('#sidebarMain').removeClass('d-none');
            }
        }
        
        // Run on page load
        maintainSidebarVisibility();
        
        // Run after a short delay to ensure DOM is ready
        setTimeout(maintainSidebarVisibility, 100);
        
        // Run on window resize
        $(window).on('resize', function() {
            maintainSidebarVisibility();
        });
        
        // Prevent sidebar from being hidden when clicking menu items
        $(document).on('click', '.js-navbar-vertical-aside-menu-link', function(e) {
            if ($(window).width() >= 1200) {
                // Don't hide sidebar on desktop when clicking menu items
                setTimeout(maintainSidebarVisibility, 50);
            }
        });
    });
</script>

<script src="{{asset('assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>
</body>
</html>

