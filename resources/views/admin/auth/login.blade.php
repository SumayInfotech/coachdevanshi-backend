<!doctype html>
<html lang="en" dir="ltr">

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sash – Bootstrap 5  Admin & Dashboard Template">
    <meta name="author" content="Spruko Technologies Private Limited">
    <meta name="keywords" content="admin,admin dashboard,admin panel,admin template,bootstrap,clean,dashboard,flat,jquery,modern,responsive,premium admin templates,responsive admin,ui,ui kit.">

    <!-- FAVICON -->
    <!-- <link rel="shortcut icon" type="image/x-icon"
          href="{{ asset('assets/sash/images/brand/favicon.ico') }}"> -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ setting('favicon_icon') ? getFileUrl(setting('favicon_icon')) : asset('assets/sash/images/brand/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- TITLE -->
    @php    
    $projectTitle = setting('project_title', 'Admin Panel');
    @endphp
    <title> {{ $projectTitle }} | Admin Login</title>

    <!-- BOOTSTRAP CSS -->
    <link id="style"
          href="{{ asset('assets/sash/plugins/bootstrap/css/bootstrap.min.css') }}"
          rel="stylesheet">

    <!-- STYLE CSS -->
    <link href="{{ asset('assets/sash/css/style.css') }}" rel="stylesheet">

    <!-- Plugins CSS -->
    <link href="{{ asset('assets/sash/css/plugins.css') }}" rel="stylesheet">

    <!-- FONT ICONS CSS (REQUIRED FOR GOOGLE / FB / TWITTER) -->
    <link href="{{ asset('assets/sash/css/icons.css') }}" rel="stylesheet">

    <!-- FONT AWESOME (MANDATORY FOR fa fa-google etc.) -->
    <!-- <link href="{{ asset('assets/sash/plugins/fontawesome/css/all.min.css') }}" rel="stylesheet"> -->

    <!-- INTERNAL Switcher css -->
    <link href="{{ asset('assets/sash/switcher/css/switcher.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/switcher/demo.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Gabarito', sans-serif !important;
        }
    </style>

</head>

<body class="app sidebar-mini ltr login-img">

<!-- BACKGROUND -->
<div class="">

    <!-- GLOBAL LOADER -->
    <div id="global-loader">
        <img src="{{ asset('assets/sash/images/loader.svg') }}"
             class="loader-img"
             alt="Loader">
    </div>

    <!-- PAGE -->
    <div class="page">
        <div class="">

            <!-- LOGO -->
            <div class="col col-login mx-auto mt-7">
                <div class="text-center">
                    <a href="javascript:void(0)">
                        <img src="{{ setting('app_icon') ? getFileUrl(setting('app_icon')) : asset('assets/sash/images/brand/logo-white.png') }}"
                             class="header-brand-img"
                             alt="" width="150" height="150">
                    </a>
                </div>
            </div>

            <div class="container-login100">
                <div class="wrap-login100 p-6">
                     

                    <form class="login100-form validate-form"
                          id="loginForm"
                          method="POST"
                          action="{{ route('admin.login-post') }}">
                        @csrf

                        <span class="login100-form-title pb-5">
                            {{ __('auth.login') }}
                        </span>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel panel-primary">
                            <div class="tab-menu-heading">
                                <div class="tabs-menu1">
                                    <ul class="nav panel-tabs">
                                        <li class="mx-0">
                                            <a href="#tab5" class="active" data-bs-toggle="tab">{{ __('auth.email') }}</a>
                                        </li>
                                        <!-- <li class="mx-0">
                                            <a href="#tab6" data-bs-toggle="tab">Mobile</a>
                                        </li> -->
                                    </ul>
                                </div>
                            </div>

                            <div class="panel-body tabs-menu-body p-0 pt-5">
                                <div class="tab-content">

                                    <!-- EMAIL TAB -->
                                    <div class="tab-pane active" id="tab5">

                                        <div class="wrap-input100 validate-input input-group">
                                            <a href="javascript:void(0)"
                                               class="input-group-text bg-white text-muted">
                                                <i class="zmdi zmdi-email text-muted"></i>
                                            </a>
                                            <input class="input100 border-start-0 form-control ms-0"
                                                   type="email"
                                                   name="login"
                                                   placeholder="{{ __('auth.email') }}">
                                        </div>

                                        <div class="wrap-input100 validate-input input-group"
                                             id="Password-toggle">
                                            <a href="javascript:void(0)"
                                               class="input-group-text bg-white text-muted">
                                                <i class="zmdi zmdi-eye text-muted"></i>
                                            </a>
                                            <input class="input100 border-start-0 form-control ms-0"
                                                   type="password"
                                                   name="password"
                                                   placeholder="{{ __('auth.password_label') }}">
                                        </div>

                                        <div class="text-end pt-4">
                                            <p class="mb-0">
                                                <a href="{{ route('admin.password.request') }}"
                                                   class="text-primary ms-1">
                                                    {{ __('auth.forgot_password') }}
                                                </a>
                                            </p>
                                        </div>

                                        <div class="container-login100-form-btn">
                                            <button type="submit"
                                                    class="login100-form-btn btn-primary">
                                                {{ __('auth.login') }}
                                            </button>
                                        </div>

                                        <!-- <div class="text-center pt-3">
                                            <p class="text-dark mb-0">
                                                Not a member?
                                                <a href="javascript:void(0)"
                                                   class="text-primary ms-1">
                                                    Sign UP
                                                </a>
                                            </p>
                                        </div> -->

                                        <!-- SOCIAL LOGIN -->
                                        <!-- <label class="login-social-icon">
                                            <span>Login with Social</span>
                                        </label> -->

                                        <!-- <div class="d-flex justify-content-center">
                                            <a href="javascript:void(0)">
                                                <div class="social-login me-4 text-center">
                                                    <i class="fa fa-google"></i>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0)">
                                                <div class="social-login me-4 text-center">
                                                    <i class="fa fa-facebook"></i>
                                                </div>
                                            </a>
                                            <a href="javascript:void(0)">
                                                <div class="social-login text-center">
                                                    <i class="fa fa-twitter"></i>
                                                </div>
                                            </a>
                                        </div> -->

                                    </div>

                                    <!-- MOBILE TAB -->
                                    <div class="tab-pane" id="tab6">

                                        <div id="mobile-num"
                                             class="wrap-input100 validate-input input-group mb-4">
                                            <a href="javascript:void(0)"
                                               class="input-group-text bg-white text-muted">
                                                <span>+91</span>
                                            </a>
                                            <input class="input100 border-start-0 form-control ms-0">
                                        </div>

                                        <div id="login-otp"
                                             class="justify-content-around mb-5">
                                            <input class="form-control text-center w-15" maxlength="1">
                                            <input class="form-control text-center w-15" maxlength="1">
                                            <input class="form-control text-center w-15" maxlength="1">
                                            <input class="form-control text-center w-15" maxlength="1">
                                        </div>

                                        <span>
                                            {{ __('auth.note') }}
                                        </span>

                                        <div class="container-login100-form-btn">
                                            <a href="javascript:void(0)"
                                               class="login100-form-btn btn-primary"
                                               id="generate-otp">
                                                Proceed
                                            </a>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- JQUERY -->
<script src="{{ asset('assets/sash/js/jquery.min.js') }}"></script>

<!-- BOOTSTRAP -->
<script src="{{ asset('assets/sash/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/sash/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!-- SHOW PASSWORD -->
<script src="{{ asset('assets/sash/js/show-password.min.js') }}"></script>

<!-- GENERATE OTP -->
<script src="{{ asset('assets/sash/js/generate-otp.js') }}"></script>

<!-- PERFECT SCROLLBAR -->
<script src="{{ asset('assets/sash/plugins/p-scroll/perfect-scrollbar.js') }}"></script>

<!-- THEME COLORS -->
<script src="{{ asset('assets/sash/js/themeColors.js') }}"></script>

<!-- CUSTOM JS -->
<script src="{{ asset('assets/sash/js/custom.js') }}"></script>

<!-- SWITCHER -->
<script src="{{ asset('assets/sash/js/custom-swicher.js') }}"></script>
<script src="{{ asset('assets/sash/switcher/js/switcher.js') }}"></script>
<script src="{{asset('assets/vendor/js/jquery-validation.js')}}"></script>

<script>
$(document).ready(function () {

    $("#loginForm").validate({
        rules: {
            login: {
                required: true,
                email: true
            },
            password: {
                required: true
            }
        },
        messages: {
            login: {
                required: "Please enter your email",
                email: "Please enter a valid email address"
            },
            password: {
                required: "Please enter your password"
            }
        },
        errorElement: "div",
        errorClass: "text-danger mt-1",

        errorPlacement: function (error, element) {
            // place error AFTER input-group, not after input
            if (element.closest('.input-group').length) {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
        },

        highlight: function (element) {
            $(element).addClass("is-invalid");
        },

        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },

        submitHandler: function (form) {
            $(form).find("button[type='submit']")
                   .prop("disabled", true)
                   .text("Signing in...");
            form.submit();
        }
    });

});
</script>

</body>
</html>
