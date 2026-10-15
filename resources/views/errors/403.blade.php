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
            <!-- PAGE-CONTENT OPEN -->
            <div class="page-content error-page error2 text-white">
                <div class="container text-center">
                    <div class="error-template">
                        <h1 class="display-1 mb-2">4<span class="custom-emoji"><svg xmlns="http://www.w3.org/2000/svg" height="140" width="140" data-name="Layer 1" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#a2a1ff"/><path fill="#6563ff" d="M14.99951 17.0918a.99473.99473 0 0 1-.64209-.23438 3.766 3.766 0 0 0-4.71484 0 .99955.99955 0 1 1-1.28516-1.53125 5.81162 5.81162 0 0 1 7.28516 0 .99974.99974 0 0 1-.64307 1.76563zM8.25 12a1 1 0 0 1-.707-1.707l.293-.293-.293-.293A.99989.99989 0 0 1 8.957 8.293l1 1a.99962.99962 0 0 1 0 1.41406l-1 1A.99676.99676 0 0 1 8.25 12z"/><path fill="#6563ff" d="M10.25 12a.99676.99676 0 0 1-.707-.293l-1-1a.99962.99962 0 0 1 0-1.41406l1-1A.99989.99989 0 0 1 10.957 9.707l-.293.293.293.293A1 1 0 0 1 10.25 12zM14.25 12a1 1 0 0 1-.707-1.707l.293-.293-.293-.293A.99989.99989 0 0 1 14.957 8.293l1 1a.99962.99962 0 0 1 0 1.41406l-1 1A.99676.99676 0 0 1 14.25 12z"/><path fill="#6563ff" d="M16.25,12a.99676.99676,0,0,1-.707-.293l-1-1a.99962.99962,0,0,1,0-1.41406l1-1A.99989.99989,0,0,1,16.957,9.707l-.293.293.293.293A1,1,0,0,1,16.25,12Z"/></svg></span>3</h1>
                        <h5 class="error-details">
                            Access Denied! You are not authorized to perform this action.
                        </h5>
                    </div>
                </div>
            </div>
            <!-- PAGE-CONTENT OPEN CLOSED -->
        </div>
        <!-- End PAGE -->
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


</body>
</html>
