@extends('admin.auth.layout')

@section('title', 'Forgot Password')

@section('form')
    <h1>{{ __('auth.forgot_password') }}</h1>
    <div class="dd-auth-sub">{{ __('auth.forgot_password_title') }}</div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @error('email')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <form id="forgotForm" method="POST" action="{{ route('admin.password.email') }}" novalidate>
        @csrf
        <div class="dd-field">
            <label for="email">{{ __('auth.email') }}</label>
            <div class="dd-input">
                <i class="fe fe-mail dd-input-icon"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="you@example.com" autofocus>
            </div>
        </div>

        <button type="submit" class="btn btn-primary dd-auth-btn">{{ __('auth.send_reset_link') }}</button>

        <div class="text-center pt-4">
            <a href="{{ route('admin.login') }}" class="fw-semibold"><i class="fe fe-arrow-left me-1"></i>{{ __('auth.back_to_login') }}</a>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    ddValidate('#forgotForm',
        { email: { required: true, email: true } },
        { email: { required: 'Please enter your email address', email: 'Please enter a valid email address' } },
        'Sending...'
    );
</script>
@endsection
