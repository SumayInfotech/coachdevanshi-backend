@extends('admin.layouts.master')

@section('title', __('users.create_title'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('users.create_title') }}</h1>

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
                    {{ __('users.create') }}
                </li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- ROW OPEN -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- CARD HEADER -->
                <div class="card-header">
                    <h3 class="card-title">
                        {{ __('users.add_title', ['module' => __('users.module_name')]) }}
                    </h3>
                </div>

                <!-- CARD BODY -->
                <div class="card-body">

                    <form id="userForm"
                          method="POST"
                          action="{{ route($routePrefix.'.store') }}"
                          enctype="multipart/form-data"
                          autocomplete="off">
                        @csrf

                        <div class="row">

                            <!-- Name -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('users.name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           name="name"
                                           placeholder="{{ __('users.enter_name') }}"
                                           value="{{ old('name') }}">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('users.email') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           class="form-control"
                                           name="email"
                                           placeholder="{{ __('users.enter_email') }}"
                                           value="{{ old('email') }}">
                                </div>
                            </div>

                            <!-- Mobile -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('users.mobile_no') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           name="mobile_no"
                                           placeholder="{{ __('users.enter_mobile') }}"
                                           value="{{ old('mobile_no') }}">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('users.password') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group" id="Password-toggle">
                                        <input type="password"
                                               class="form-control"
                                               name="password"
                                               placeholder="{{ __('users.enter_password') }}">
                                        <a href="javascript:void(0)"
                                           class="input-group-text bg-white text-muted">
                                            <i class="zmdi zmdi-eye text-muted"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- State -->
                            {{-- <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('users.state') }} <span class="text-danger"></span>
                                    </label>
                                    <select name="state_id" class="form-control select2" id="state_id">
                                        <option value="">{{ __('users.select_state') }}</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}">
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- City -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('users.city') }} <span class="text-danger"></span>
                                    </label>
                                    <select name="city_id" class="form-control select2" id="city_id">
                                        <option value="">{{ __('users.select_city') }}</option>
                                    </select>
                                </div>
                            </div> --}}

                            <!-- Role -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('users.role') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="role_uuid" class="form-control select2">
                                        <option value="">{{ __('users.select_role') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->uuid }}">
                                                {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Profile Image -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>{{ __('users.profile_pic') }}</label>
                                    <input type="file" class="form-control" name="image">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>
                                        {{ __('users.status') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" class="form-control select2">
                                        <option value="">{{ __('users.select_status') }}</option>
                                        <option value="Active" selected>{{ __('users.active') }}</option>
                                        <option value="Inactive">{{ __('users.inactive') }}</option>
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
                                        <option value="en">{{ __('settings.lang_en') }}</option>
                                        <option value="it">{{ __('settings.lang_it') }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- FORM FOOTER -->
                        <div class="card-footer text-end px-0 mt-4">
                            <button type="submit" class="btn btn-success my-1">
                                {{ __('users.save') }}
                            </button>
                            <a href="{{ route($routePrefix) }}" class="btn btn-danger my-1">
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


@section('page-css')

@endsection

@section('scripts')
<script src="{{asset('assets/vendor/js/jquery-validation.js')}}"></script>

<script>
$(document).ready(function () {

    $.validator.addMethod("numericOnly", function (value, element) {
        return this.optional(element) || /^[0-9]+$/.test(value.trim());
      }, jsValidate('numeric', { field: 'Mobile Number' }));



    $("#userForms").validate({
        ignore: [],
        rules: {
            name: { required: true, minlength: 2 },
            email: { required: true, email: true },
            mobile_no: { required: true, numericOnly: true, minlength: 10, maxlength: 10 },
            password: { required: true, minlength: 6 },
            status: { required: true },
            state_id: { required: true },
            city_id: { required: true },
            role_uuid: { required: true }
        },
        messages: {
            name: {
                required: jsValidate('required', { field: 'Name' }),
                minlength: jsValidate('min', { field: 'Name', min: 2 })
            },
             email: {
                required: jsValidate('required', { field: 'Email' }),
                email: jsValidate('email', { field: 'Email' })
            },
            mobile_no: {
                required: jsValidate('required', { field: 'Mobile Number' }),
                digits: jsValidate('digits', { field: 'Mobile Number', digits: 10 })
            },
            password: {
                required: jsValidate('required', { field: 'Password' }),
                minlength: jsValidate('min', { field: 'Password', min: 6 })
            },
            status: {
                required: jsValidate('required', { field: 'Status' })
            },
            state_id: {
                required: jsValidate('required', { field: 'State' })
            },
            city_id: {
                required: jsValidate('required', { field: 'City' })
            },
            role_uuid: {
                required: jsValidate('required', { field: 'Role' })
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
             $(form)[0].submit();
        }
    });

    

    $('#state_id').on('change', function () {
        var stateId = $(this).val();
        $('#city_id').html('<option value="">Select City</option>');

        if (stateId) {
            $.ajax({
                url: "{{ route($routePrefix.'.get.cities') }}",
                type: "POST",
                data: {
                    state_id: stateId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (cities) {
                    $.each(cities, function (index, city) {
                        $('#city_id').append(
                            '<option value="'+city.id+'">'+city.name+'</option>'
                        );
                    });
                }
            });
        }
    });
});
</script>
@endsection
