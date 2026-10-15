@extends('admin.layouts.master')

@section('title', __('users.edit_title'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('users.edit_title') }}</h1>

        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active">
                    <a href="{{ route($routePrefix) }}">
                        {{ __('users.module_plural') }}
                    </a>
                </li>
                <li class="breadcrumb-item1" aria-current="page">
                    {{ __('users.edit') }}
                </li>
            </ol>
        </div>
    </div>

    <!-- FORM -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        {{ __('users.edit_title') }}
                    </h3>
                </div>

                <div class="card-body">

                    <form id="userForm"
                          method="POST"
                          action="{{ route($routePrefix.'.update', $user->uuid) }}"
                          enctype="multipart/form-data"
                          autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="row">

                            <!-- Name -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                           name="name"
                                           value="{{ old('name', $user->name) }}">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.email') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control"
                                           name="email"
                                           value="{{ old('email', $user->email) }}">
                                </div>
                            </div>

                            <!-- Mobile -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.mobile_no') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control"
                                           name="mobile_no"
                                           value="{{ old('mobile_no', $user->mobile) }}">
                                </div>
                            </div>

                            <!-- Password (Optional) -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.password') }}</label>
                                    <div class="input-group" id="Password-toggle">
                                        <input type="password" class="form-control"
                                               name="password"
                                               placeholder="{{ __('users.password_optional') }}">
                                        <span class="input-group-text bg-white">
                                            <i class="zmdi zmdi-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- State -->
                            {{-- <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.state') }} <span class="text-danger">*</span></label>
                                    <select name="state_id" id="state_id" class="form-control select2">
                                        <option value="">{{ __('users.select_state') }}</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}"
                                                {{ $user->state_id == $state->id ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.city') }} <span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control select2">
                                        <option value="">{{ __('users.select_city') }}</option>
                                    </select>
                                </div>
                            </div> --}}

                            <!-- Role -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.role') }} <span class="text-danger">*</span></label>
                                    <select name="role_uuid" class="form-control select2">
                                        <option value="">{{ __('users.select_role') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->uuid }}"
                                                {{ $user->role_uuid == $role->uuid ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('_',' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Profile Image -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.profile_pic') }}</label>
                                    <input type="file" class="form-control" name="image">

                                    @if($user->profile_image)
                                        <img src="{{ getFileUrl($user->profile_image) }}"
                                             class="mt-2 rounded"
                                             width="80">
                                    @endif
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('users.status') }} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control select2">
                                        <option value="">{{ __('users.select_status') }}</option>
                                        <option value="Active" {{ $user->status == 'Active' ? 'selected' : '' }}>
                                            {{ __('users.active') }}
                                        </option>
                                        <option value="Inactive" {{ $user->status == 'Inactive' ? 'selected' : '' }}>
                                            {{ __('users.inactive') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Application Language -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {{ __('settings.language') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="language" class="form-control select2" required>
                                        <option value="">Select Language</option>
                                        <option value="en"
                                            {{ $user->language === 'en' ? 'selected' : '' }}>
                                          {{ __('settings.lang_en') }}
                                        </option>
                                        <option value="it"
                                            {{ $user->language === 'it' ? 'selected' : '' }}>
                                            {{ __('settings.lang_it') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="card-footer text-end px-0 mt-4">
                            <button type="submit" class="btn btn-success">
                                {{ __('users.update') }}
                            </button>
                            <a href="{{ route($routePrefix) }}" class="btn btn-danger">
                                {{ __('users.cancel') }}
                            </a>
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
    const existingCityId = "{{ $user->city_id }}";
    bindStateCity('#state_id','#city_id',"{{ route($routePrefix . '.get.cities') }}",existingCityId);

    $('#state_id').trigger('change');
});
</script>
<script>
$(document).ready(function () {

    $.validator.addMethod("numericOnly", function (value) {
        return this.optional(element) || /^[0-9]+$/.test(value.trim());
    }, jsValidate('numeric', { field: '{{ __("users.mobile_no") }}' }));

    $("#userForm").validate({
        rules: {
            name: { required: true, minlength: 2 },
            email: { required: true, email: true },
            mobile_no: { required: true, digits: 10 },
            password: { minlength: 6 }, // optional
            status: { required: true },
            role_uuid: { required: true }
        },
        messages: {
            name: {
                required: jsValidate('required', { field: '{{ __("users.name") }}' }),
                minlength: jsValidate('min', { field: '{{ __("users.name") }}', min: 2 })
            },
            email: {
                required: jsValidate('required', { field: '{{ __("users.email") }}' }),
                email: jsValidate('email', { field: '{{ __("users.email") }}' })
            },
            mobile_no: {
                required: jsValidate('required', { field: '{{ __("users.mobile_no") }}' }),
                digits: jsValidate('digits', { field: '{{ __("users.mobile_no") }}', digits: 10 })
            },
            password: {
                minlength: jsValidate('min', { field: '{{ __("users.password") }}', min: 6 })
            }
        },
        errorElement: "div",
        errorPlacement: function (error, element) {
            error.addClass("text-danger mt-1");

            if (element.attr("name") === "password") {
                error.insertAfter(element.closest(".input-group"));
            }
            else if (element.hasClass("select2-hidden-accessible")) {
                error.insertAfter(element.next('.select2'));
            }
            else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            $("#global-loader").show();
            form.submit();
        }
    });
});
</script>
@endsection
