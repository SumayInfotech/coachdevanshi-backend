<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin Panel')</title>

    {{-- THEME CSS --}}
    <link href="{{ asset('assets/sash/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/icons.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body class="app sidebar-mini ltr">

    {{-- GLOBAL LOADER --}}
    <div id="global-loader">
        <img src="{{ asset('assets/sash/images/loader.svg') }}" alt="loader">
    </div>

    <div class="page">

        {{-- HEADER --}}
        @include('admin.layouts.header')

        {{-- SIDEBAR --}}
        @include('admin.layouts.sidebar')

        {{-- MAIN CONTENT --}}
        <div class="main-content app-content">
            <div class="container-fluid">

                @yield('content')

            </div>
        </div>

        {{-- FOOTER --}}
        @include('admin.layouts.footer')

    </div>

    {{-- JS --}}
    @include('admin.layouts.scripts')

    @stack('scripts')
</body>
</html>
