@extends('admin.layouts.master')

@section('title', __('users.change_password'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('users.change_password') }}</h1>

        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active">
                    {{ __('users.change_password') }}
                </li>
            </ol>
        </div>
    </div>

    <!-- FORM -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">{{ __('users.change_password') }}</h3>
                </div>

                <div class="card-body">

                    <form id="passwordForm"
                          method="POST"
                          action="{{ route('admin.update-password') }}"
                          autocomplete="off">
                        @csrf

                        <div class="row">

                            <!-- Current Password -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('users.current_password') }} <span class="text-danger">*</span></label>
                                    <input type="password"
                                           name="current_password"
                                           class="form-control">
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('users.new_password') }} <span class="text-danger">*</span></label>
                                    <input type="password"
                                           name="new_password"
                                           class="form-control">
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>{{ __('users.confirm_password') }} <span class="text-danger">*</span></label>
                                    <input type="password"
                                           name="new_password_confirmation"
                                           class="form-control">
                                </div>
                            </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="card-footer text-end px-0 mt-4">
                            <button type="submit" class="btn btn-success">
                                {{ __('users.update_password') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendor/js/jquery-validation.js') }}"></script>

<script>
$(document).ready(function () {

    $("#passwordForm").validate({
        rules: {
            current_password: { required: true },
            new_password: { required: true, minlength: 6 },
            new_password_confirmation: {
                required: true,
                equalTo: "[name='new_password']"
            }
        },
        messages: {
            current_password: {
                required: jsValidate('required', { field: '{{ __("users.current_password") }}' })
            },
            new_password: {
                required: jsValidate('required', { field: '{{ __("users.new_password") }}' }),
                minlength: jsValidate('min', { field: '{{ __("users.new_password") }}', min: 6 })
            },
            new_password_confirmation: {
                required: jsValidate('required', { field: '{{ __("users.confirm_password") }}' }),
                equalTo: jsValidate('same', { field: '{{ __("users.confirm_password") }}' })
            }
        },
        errorElement: "div",
        errorClass: "text-danger mt-1",
        submitHandler: function (form) {
            $("#global-loader").show();
            form.submit();
        }
    });

});
</script>
@endsection
