@extends('admin.layouts.master')

@section('title', __('categories.edit_title'))

@section('content')
<div class="main-container container-fluid">

    <div class="page-header">
        <h1 class="page-title">{{ __('categories.edit_title') }}</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active">
                    <a href="{{ route($routePrefix.'.index') }}">
                        {{ __('categories.module_plural') }}
                    </a>
                </li>
                <li class="breadcrumb-item1" aria-current="page">
                    {{ __('categories.edit_title') }}
                </li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <form id="categoryForm"
                          method="POST"
                          action="{{ route($routePrefix.'.update', $category->uuid) }}"
                          autocomplete="off">

                        @csrf
                        @method('PUT')

                        <div class="row">

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {{ __('categories.name') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           value="{{ old('name', $category->name) }}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {{ __('categories.status') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" class="form-control select2">
                                        <option value="Active"
                                            {{ $category->status == 'Active' ? 'selected' : '' }}>
                                            {{ __('categories.active') }}
                                        </option>
                                        <option value="Inactive"
                                            {{ $category->status == 'Inactive' ? 'selected' : '' }}>
                                            {{ __('categories.inactive') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer text-end px-0 mt-4">
                            <button type="submit" class="btn btn-success">
                                {{ __('categories.update') }}
                            </button>
                            <a href="{{ route($routePrefix.'.index') }}" class="btn btn-danger">
                                {{ __('categories.cancel') }}
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

    $("#categoryForm").validate({
        ignore: [],
        rules: {
            name:   { required: true, minlength: 2 },
            status: { required: true },
        },
        messages: {
            name: {
                required: jsValidate('required', { field: 'Name' }),
                minlength: jsValidate('min', { field: 'Name', min: 2 })
            },
            status: {
                required: jsValidate('required', { field: 'Status' })
            },
        },
        errorElement: "div",
        errorPlacement: function (error, element) {
            error.addClass("text-danger mt-1");
            if (element.hasClass("select2-hidden-accessible")) {
                error.insertAfter(element.next('.select2'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function (form) {
            $("#global-loader").show();
            $(form)[0].submit();
        }
    });
});
</script>
@endsection