<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    @php    
    $projectTitle = setting('project_title', 'Admin Panel');
    @endphp
    <title> {{ $projectTitle }} |  Forgot Password</title>

    <link href="{{ asset('assets/sash/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/icons.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Gabarito', sans-serif !important;
        }
    </style>
</head>

<body class="app sidebar-mini ltr login-img">

<div class="page">
    <div class="text-center mb-4">
        <img src="{{ setting('app_icon') ? getFileUrl(setting('app_icon')) : asset('assets/sash/images/brand/logo-white.png') }}"
                class="header-brand-img" width="150" height="150" 
                alt="Logo">
    </div>
    <div class="container-login100">
        <div class="wrap-login100 p-6">

            <!-- <div class="text-center mb-4">
                <img src="{{ setting('app_icon') ? getFileUrl(setting('app_icon')) : asset('assets/sash/images/brand/logo-white.png') }}"
                     class="header-brand-img" width="150" height="150" 
                     alt="Logo">
            </div> -->

            <form method="POST" action="{{ route('admin.password.email') }}">
                @csrf

                <span class="login100-form-title pb-3">
                    {{ __('auth.forgot_password') }}
                </span>

                <p class="text-muted text-center mb-4">
                    {{ __('auth.forgot_password_title') }}
                </p>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                <div class="wrap-input100 validate-input input-group mb-4">
                    <span class="input-group-text bg-white text-muted">
                        <i class="zmdi zmdi-email"></i>
                    </span>
                    <input type="email"
                           name="email"
                           class="input100 border-start-0 form-control ms-0"
                           placeholder="{{ __('auth.email') }}"
                           required>
                </div>

                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn btn-primary">
                        {{ __('auth.send_reset_link') }}
                    </button>
                </div>

                <div class="text-center pt-3">
                    <a href="{{ route('admin.login') }}" class="text-primary">
                        {{ __('auth.back_to_login') }}
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/sash/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/sash/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/vendor/js/jquery-validation.js') }}"></script>

<script>
$(document).ready(function () {

    $("form").validate({
        rules: {
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            email: {
                required: "Please enter your email address",
                email: "Please enter a valid email address"
            }
        },
        errorElement: "div",
        errorClass: "text-danger mt-1",

        errorPlacement: function (error, element) {
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
                   .text("Sending...");
            form.submit();
        }
    });

});
</script>
</body>
</html>
