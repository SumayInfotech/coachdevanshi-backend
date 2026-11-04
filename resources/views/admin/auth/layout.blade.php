<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('project_title', 'Devanshi Doshi') }} | @yield('title')</title>
    <link rel="shortcut icon" href="{{ setting('favicon_icon') ? getFileUrl(setting('favicon_icon')) : asset('assets/brand/favicon.png') }}">

    <link href="{{ asset('assets/sash/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/sash/css/icons.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/brand/admin-theme.css') }}?v={{ filemtime(public_path('assets/brand/admin-theme.css')) }}" rel="stylesheet">
</head>
<body class="dd-auth">
<div class="dd-auth-shell">
    <aside class="dd-auth-brand">
        <div class="dd-auth-logo-card">
            <img src="{{ asset('assets/brand/logo.png') }}?v={{ filemtime(public_path('assets/brand/logo.png')) }}" class="dd-auth-logo" alt="Devanshi Doshi – Holistic inch-loss">
        </div>
        <div class="dd-auth-quote">
            <div class="dd-eyebrow">Coach workspace</div>
            <h2>Small, grounded rituals build sustainable nourishment.</h2>
            <p>Follow every client's onboarding, pathway, daily check-ins and progress, and keep the conversation going, from one calm place.</p>
        </div>
        <div class="dd-auth-points">
            <span class="dd-chip"><span class="dd-dot"></span>Client onboarding</span>
            <span class="dd-chip"><span class="dd-dot" style="background:var(--dd-sage)"></span>Daily &amp; weekly tracking</span>
            <span class="dd-chip"><span class="dd-dot" style="background:var(--dd-sand)"></span>Plans &amp; renewals</span>
            <span class="dd-chip"><span class="dd-dot" style="background:var(--dd-blush)"></span>Coach chat</span>
        </div>
    </aside>

    <main class="dd-auth-form-side">
        <div class="dd-auth-card">
            <img src="{{ asset('assets/brand/logo.png') }}" class="dd-auth-mobile-logo" alt="Devanshi Doshi">
            @yield('form')
            <div class="dd-auth-foot">{{ setting('footer_text') ?: '© ' . date('Y') . ' Devanshi Doshi · Holistic inch-loss coaching' }}</div>
        </div>
    </main>
</div>

<script src="{{ asset('assets/sash/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/sash/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/vendor/js/jquery-validation.js') }}"></script>
<script>
    $(document).on('click', '.dd-eye', function () {
        const input = $(this).siblings('input');
        const show = input.attr('type') === 'password';
        input.attr('type', show ? 'text' : 'password');
        $(this).find('i').toggleClass('fe-eye fe-eye-off');
    });
    window.ddValidate = function (selector, rules, messages, busyText) {
        $(selector).validate({
            rules: rules,
            messages: messages,
            errorElement: 'div',
            errorClass: 'text-danger small mt-1',
            errorPlacement: function (error, element) { error.insertAfter(element.closest('.dd-input')); },
            highlight: function (el) { $(el).addClass('is-invalid'); },
            unhighlight: function (el) { $(el).removeClass('is-invalid'); },
            submitHandler: function (form) {
                $(form).find("button[type='submit']").prop('disabled', true).text(busyText);
                form.submit();
            }
        });
    };
</script>
@yield('scripts')
</body>
</html>
