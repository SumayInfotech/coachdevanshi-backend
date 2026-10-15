@extends('admin.layouts.master')

@section('title', __('roles.create_title'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('roles.module_plural') }}</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">  {{ __('roles.dashboard') }}</a>
                </li>
                <li class="breadcrumb-item1 active">
                    <a href="{{ route($routePrefix) }}">{{ __('roles.module_name') }}</a>
                </li>
                <li class="breadcrumb-item1" aria-current="page">
                   {{ __('roles.create') }}
                </li>
            </ol>
        </div>
    </div>
    <!-- PAGE-HEADER END -->

    <!-- ROW -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- CARD HEADER -->
                <div class="card-header">
                    <h3 class="card-title">Add   {{ __('roles.add_title', ['module' => __('roles.module_name')]) }}</h3>
                    
                </div>

                <!-- CARD BODY -->
                <div class="card-body">

                    <form id="roleForm"
                          method="POST"
                          action="{{ route($routePrefix.'.store') }}"
                          autocomplete="off">
                        @csrf

                        <div class="row">

                            <!-- Role Name -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                   <label>{{ __('roles.name') }}<span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           placeholder="{{ __('roles.enter_name') }}"
                                           value="{{ old('name') }}">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>{{ __('roles.status') }}<span class="text-danger">*</span></label>
                                    <select name="status" class="form-control select2">
                                        <option value="">{{ __('roles.select_status') }}</option>
                                        <option value="Active" selected>{{ __('roles.active') }}</option>
                                        <option value="Inactive">{{ __('roles.inactive') }}</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- PERMISSIONS -->
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('roles.assign_permissions') }}</h5>

                        <!-- Global Select All -->
                        <div class="form-check mb-3">
                            <input type="checkbox" id="globalSelectAll" class="form-check-input">
                            <label class="form-check-label fw-bold" for="globalSelectAll">
                                {{ __('roles.select_all_permissions') }}
                            </label>
                        </div>

                        <!-- Permission Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('roles.permission_group') }}</th>
                                        <th class="text-center">{{ __('roles.allow_all') }}</th>
                                        <th class="text-center">{{ __('roles.create') }}</th>
                                        <th class="text-center">{{ __('roles.edit') }}</th>
                                        <th class="text-center">{{ __('roles.view') }}</th>
                                        <th class="text-center">{{ __('roles.delete') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($permissions as $group => $groupPermissions)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_', ' ', $group)) }}</td>

                                        <!-- Group Select All -->
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   class="form-check-input group-select-all"
                                                   data-group="{{ $group }}">
                                        </td>

                                        @foreach($groupPermissions as $permission)
                                            <td class="text-center">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $permission->name }}"
                                                       class="form-check-input permission-checkbox"
                                                       data-group="{{ $group }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Permission Error -->
                        <div class="text-danger mt-2" id="permissionError" style="display:none;">
                            {{ __('roles.permission_required') }}
                        </div>

                        <!-- FORM FOOTER -->
                        <div class="card-footer text-end px-0 mt-4">
                            <button type="submit" id="submitBtn" class="btn btn-success my-1">
                                 {{ __('roles.save') }}
                            </button>
                            <a href="{{ route($routePrefix) }}" class="btn btn-danger my-1">
                                {{ __('roles.cancel') }}
                            </a>
                        </div>

                    </form>

                </div>
                <!-- CARD BODY END -->

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendor/js/jquery-validation.js') }}"></script>

<script>
$(document).ready(function () {

    /* ===============================
       VALIDATION
    ================================ */
    let validator = $("#roleForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            status: {
                required: true
            },
            'permissions[]': {
                required: function () {
                    return $('.permission-checkbox:checked').length === 0;
                }
            }
        },
        messages: {
            name: {
                required: jsValidate('required', { field: "{{ __('roles.name') }}" }),
                minlength: jsValidate('min', { field: "{{ __('roles.name') }}", min: 2 })
            },
            status: {
                required: jsValidate('required', { field: "{{ __('roles.status') }}" })
            },
            'permissions[]': {
                required: "{{ __('roles.permission_required') }}"
            }
        },
        errorElement: "div",
        errorPlacement: function (error, element) {
            error.addClass("text-danger mt-1");

            if (element.attr("name") === "permissions[]") {
                $("#permissionError").html(error).show();
            } else if (element.hasClass("select2-hidden-accessible")) {
                error.insertAfter(element.next('.select2'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            $("#global-loader").show();
            form.submit();
        }
    });

    /* ===============================
       PERMISSION TOGGLES
    ================================ */

    // Global Select All
    $('#globalSelectAll').on('change', function () {
        let checked = $(this).prop('checked');
        $('.permission-checkbox, .group-select-all').prop('checked', checked);
        validatePermissions();
    });

    // Group Select All
    $('.group-select-all').on('change', function () {
        let group = $(this).data('group');
        let checked = $(this).prop('checked');
        $('.permission-checkbox[data-group="' + group + '"]').prop('checked', checked);
        updateGlobalSelectAll();
        validatePermissions();
    });

    // Individual Permission
    $('.permission-checkbox').on('change', function () {
        let group = $(this).data('group');

        let allChecked =
            $('.permission-checkbox[data-group="' + group + '"]').length ===
            $('.permission-checkbox[data-group="' + group + '"]:checked').length;

        $('.group-select-all[data-group="' + group + '"]').prop('checked', allChecked);

        updateGlobalSelectAll();
        validatePermissions();
    });

    function updateGlobalSelectAll() {
        let allChecked =
            $('.permission-checkbox').length === $('.permission-checkbox:checked').length;

        $('#globalSelectAll').prop('checked', allChecked);
    }

    function validatePermissions() {
        if ($('.permission-checkbox:checked').length > 0) {
            $("#permissionError").hide();
            validator.element('input[name="permissions[]"]');
        } else {
            $("#permissionError").show();
        }
    }
});
</script>
@endsection
