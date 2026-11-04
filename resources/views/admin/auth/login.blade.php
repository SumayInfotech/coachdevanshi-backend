@extends('admin.auth.layout')

@section('title', 'Admin Login')

@section('form')
    <h1>Welcome back</h1>
    <div class="dd-auth-sub">Sign in to the Devanshi Doshi coach panel.</div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form id="loginForm" method="POST" action="{{ route('admin.login-post') }}" novalidate>
        @csrf
        <div class="dd-field">
            <label for="login">{{ __('auth.email') }}</label>
            <div class="dd-input">
                <i class="fe fe-mail dd-input-icon"></i>
                <input id="login" class="form-control" type="email" name="login" value="{{ old('login') }}" placeholder="you@example.com" autocomplete="username" autofocus>
            </div>
        </div>

        <div class="dd-field">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password">{{ __('auth.password_label') }}</label>
                <a href="{{ route('admin.password.request') }}" class="small fw-semibold mb-1">{{ __('auth.forgot_password') }}</a>
            </div>
            <div class="dd-input">
                <i class="fe fe-lock dd-input-icon"></i>
                <input id="password" class="form-control" type="password" name="password" placeholder="••••••••" autocomplete="current-password">
                <button type="button" class="dd-eye" aria-label="Show password"><i class="fe fe-eye"></i></button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary dd-auth-btn">{{ __('auth.login') }}</button>
    </form>
@endsection

@section('scripts')
<script>
    ddValidate('#loginForm',
        { login: { required: true, email: true }, password: { required: true } },
        {
            login: { required: 'Please enter your email', email: 'Please enter a valid email address' },
            password: { required: 'Please enter your password' }
        },
        'Signing in...'
    );
</script>
@endsection
