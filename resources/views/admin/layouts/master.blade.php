<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @php    
    $projectTitle = setting('project_title', 'Admin Panel');
    @endphp
    <title>
        {{ $projectTitle }}
        @hasSection('title')
            | @yield('title')
        @endif
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ setting('favicon_icon') ? getFileUrl(setting('favicon_icon')) : asset('assets/sash/images/brand/favicon.ico') }}">

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="{{ asset('assets/sash/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- STYLE CSS -->
    <link href="{{ asset('assets/sash/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/icons.css') }}" rel="stylesheet">
    <!-- <link href="{{ asset('assets/sash/css/color-changes.css') }}" rel="stylesheet"> -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700&display=swap" rel="stylesheet">

    
    <style>
        tfoot{
            display: none;
        }
        div.dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table.dataTable {
            width: 100% !important;
        }
        .dataTables_scrollHead thead tr:first-child th {
            border-top: 1px solid #dee2e6 !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
        body {
            font-family: 'Gabarito', sans-serif !important;
        }
    </style>
    @yield('page-css')
</head>

<body class="app sidebar-mini ltr light-mode">

<!-- GLOBAL LOADER -->
<div id="global-loader">
    <!-- <img src="{{ asset('assets/sash/images/loader.svg') }}" class="loader-img" alt="Loader"> -->
    <img src="{{ setting('loader_image') ? getFileUrl(setting('loader_image')) : asset('assets/sash/images/loader.svg') }}" class="loader-img" alt="loader">
</div>

<!-- PAGE -->
<div class="page">
    <div class="page-main">

        {{-- HEADER --}}
        @include('admin.layouts.header')

        {{-- SIDEBAR --}}
        @include('admin.layouts.sidebar')

        <!-- APP CONTENT -->
        <div class="main-content app-content mt-0">
            <div class="side-app">
                @yield('content')
            </div>
        </div>

    </div>

    {{-- FOOTER --}}
    @include('admin.layouts.footer')
</div>
<a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>
<!-- JS -->
    <script src="{{ asset('assets/sash/js/jquery.min.js') }}"></script>

    <!-- BOOTSTRAP JS -->
    <script src="{{ asset('assets/sash/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- INPUT MASK JS-->
    <script src="{{ asset('assets/sash/plugins/input-mask/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('assets/sash/js/show-password.min.js') }}"></script>


	<!-- TypeHead js -->
     <script src="{{ asset('assets/sash/plugins/bootstrap5-typehead/autocomplete.js') }}"></script>
    <script src="{{ asset('assets/sash/js/typehead.js') }}"></script>
    
    <!-- Select2 JS-->
    <script src="{{ asset('assets/sash/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/sash/js/select2.js') }}"></script>
    
    <!-- Perfect SCROLLBAR JS-->
    <script src="{{ asset('assets/sash/plugins/p-scroll/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/p-scroll/pscroll.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/p-scroll/pscroll-1.js') }}"></script>
    <!-- SIDE-MENU JS -->
    <script src="{{ asset('assets/sash/plugins/sidemenu/sidemenu.js') }}"></script>
    
    <!-- SIDEBAR JS -->
    <script src="{{ asset('assets/sash/plugins/sidebar/sidebar.js') }}"></script>
    
    <!-- Color Theme js -->
    <script src="{{ asset('assets/sash/js/themeColors.js') }}"></script>
    
    <!-- Sticky js -->
    <script src="{{ asset('assets/sash/js/sticky.js') }}"></script>
    
    <!-- CUSTOM JS -->

    <script src="{{ asset('assets/sash/js/custom.js') }}"></script>

    <!-- Custom-switcher -->
    <script src="{{ asset('assets/sash/js/custom-swicher.js') }}"></script>

    <!-- Switcher js -->
    <script src="{{ asset('assets/sash/switcher/js/switcher.js') }}"></script>
    <script src="{{ asset('assets/sash/switcher/js/switcher.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/sash/js/sweet-alert.js') }}"></script>
    <script src="{{ asset('assets/sash/js/toast.js') }}"></script>
    <script src="{{ asset('assets/sash/js/master.js') }}"></script>
    
    <!-- JS VALIDATION MESSAGES -->
    <script>
        window.jsValidationMessages = {
            required: @json(__('js-validation.required')),
            email:    @json(__('js-validation.email')),
            numeric:  @json(__('js-validation.numeric')),
            digits:   @json(__('js-validation.digits')),
            min:      @json(__('js-validation.min')),
            max:      @json(__('js-validation.max')),
            url:      @json(__('js-validation.url')),
        };
        </script>
        <script src="{{ asset('assets/sash/js/js-validation-helper.js') }}"></script>

    <script>
    //      let loaderStartTime = null;
    //     const MIN_LOADER_TIME = 500;

    //     function showGlobalLoader() {
    //         if (!loaderStartTime) {
    //             loaderStartTime = Date.now();
    //             $("#global-loader").show();
    //         }
    //     }

    //     function hideGlobalLoader() {
    //         if (!loaderStartTime) return;

    //         const elapsed = Date.now() - loaderStartTime;
    //         const remaining = Math.max(MIN_LOADER_TIME - elapsed, 0);

    //         setTimeout(() => {
    //             $("#global-loader").hide();
    //             loaderStartTime = null;
    //         }, remaining);
    //     }
    //     $(document).ajaxStart(function () {
    //         showGlobalLoader();
    //     });

    //     $(document).ajaxStop(function () {
    //         hideGlobalLoader();
    //     });
    //    // ✅ Page LOAD loader (not unload)
    //     $(window).on("load", function () {
    //         hideGlobalLoader();
    //     });

    //     //✅ Show loader immediately on first paint
    //     $(document).ready(function () {
    //         showGlobalLoader();
    //     });
    </script>
    <script>
        window.showToast = function (type, message) {
            return Swal.fire({
                toast: true,
                position: "top-end",
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        };
    </script>
    @if (session('success'))
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            showToast('success', @json(session('success')));
        });
        </script>
    @endif

    @if (session()->has('language_changed_success'))
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            showToast('success', @json(session('language_changed_success')));
        });
        
        </script>
        @php
            session()->forget('language_changed_success');
        @endphp
    @endif
    
    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', async function () {

            const errors = @json($errors->all());

            for (const error of errors) {
                await showToast('error', error);
            }

        });
        </script>
    @endif

    <script>
        $(document).on('click', '.language-switch', function () {
            const lang = $(this).data('lang');
            $("#global-loader").show();
            $.ajax({
                url: "{{ route('admin.user.update-language') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    language: lang
                },
                success: function (res) {
                    location.reload();
                },
                error: function () {
                    $("#global-loader").hide();
                    showToast('error', 'Failed to change language');
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
