@extends('admin.auth.layout')

@section('title', 'Reset Password')

@section('form')
    <h1>{{ __('auth.reset_password') }}</h1>
    <div class="dd-auth-sub">Choose a new password for {{ $email }}.</div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form id="resetForm" method="POST" action="{{ route('admin.password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="dd-field">
            <label for="password">{{ __('auth.new_password') }}</label>
            <div class="dd-input">
                <i class="fe fe-lock dd-input-icon"></i>
                <input id="password" type="password" name="password" class="form-control" placeholder="{{ __('auth.new_password') }}" autocomplete="new-password">
                <button type="button" class="dd-eye" aria-label="Show password"><i class="fe fe-eye"></i></button>
            </div>
        </div>

        <div class="dd-field">
            <label for="password_confirmation">{{ __('auth.confirm_new_password') }}</label>
            <div class="dd-input">
                <i class="fe fe-shield dd-input-icon"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="{{ __('auth.confirm_new_password') }}" autocomplete="new-password">
                <button type="button" class="dd-eye" aria-label="Show password"><i class="fe fe-eye"></i></button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary dd-auth-btn">{{ __('auth.reset_password') }}</button>

        <div class="text-center pt-4">
            <a href="{{ route('admin.login') }}" class="fw-semibold"><i class="fe fe-arrow-left me-1"></i>{{ __('auth.back_to_login') }}</a>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    ddValidate('#resetForm',
        {
            password: { required: true, minlength: 8 },
            password_confirmation: { required: true, equalTo: '#password' }
        },
        {
            password: { required: 'Please enter a new password', minlength: 'Password must be at least 8 characters' },
            password_confirmation: { required: 'Please confirm your password', equalTo: 'Passwords do not match' }
        },
        'Resetting...'
    );
</script>
@endsection
