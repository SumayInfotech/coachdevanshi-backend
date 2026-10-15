<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    @php    
        $projectTitle = setting('project_title', 'Admin Panel');
    @endphp
    <title> {{ $projectTitle }} |  Reset Password</title>

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
    <div class="">
        <div class="col col-login mx-auto mt-7">
            <div class="text-center">
                <a href="javascript:void(0)">
                    <img src="{{ setting('app_icon') ? getFileUrl(setting('app_icon')) : asset('assets/sash/images/brand/logo.png') }}"
                        class="header-brand-img"
                        alt="" width="150" height="150">
                </a>
            </div>
        </div>
        <div class="container-login100">
            <div class="wrap-login100 p-6">
                <form method="POST" action="{{ route('customer.password.update') }}">
                    <input type="hidden" name="token" value="{{ $token }}">

                    @csrf
                    <span class="login100-form-title pb-3">
                        {{ __('auth.reset_password') }}
                    </span>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif


                    <div class="wrap-input100 validate-input input-group mb-4">
                        <span class="input-group-text bg-white text-muted">
                            <i class="zmdi zmdi-lock"></i>
                        </span>
                        <input type="password"
                            name="password"
                            class="input100 border-start-0 form-control ms-0"
                            placeholder="{{ __('auth.new_password') }}"
                            required>
                    </div>

                    <div class="wrap-input100 validate-input input-group mb-4">
                        <span class="input-group-text bg-white text-muted">
                            <i class="zmdi zmdi-lock-outline"></i>
                        </span>
                        <input type="password"
                            name="password_confirmation"
                            class="input100 border-start-0 form-control ms-0"
                            placeholder="{{ __('auth.confirm_new_password') }}"
                            required>
                    </div>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn btn-success">
                            {{ __('auth.reset_password') }}
                        </button>
                    </div>

                    <!-- <div class="text-center pt-3">
                        <a href="{{ route('admin.login') }}" class="text-primary">
                            Back to Login
                        </a>
                    </div> -->

                </form>
            </div>
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
            password: {
                required: true,
                minlength: 8
            },
            password_confirmation: {
                required: true,
                equalTo: "[name='password']"
            }
        },
        messages: {
            password: {
                required: "Please enter a new password",
                minlength: "Password must be at least 8 characters"
            },
            password_confirmation: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
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
                   .text("Resetting...");
            form.submit();
        }
    });

});
</script>
</body>
</html>
