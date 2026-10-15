@extends('admin.layouts.master')

@section('title', __('preferences.edit_title'))

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <h1 class="page-title">{{ __('preferences.edit_title') }}</h1>

        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active">
                    <a href="{{ route($routePrefix.'.index') }}">
                        {{ __('preferences.module_plural') }}
                    </a>
                </li>
                <li class="breadcrumb-item1" aria-current="page">
                    {{ __('preferences.edit') }}
                </li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="preferenceForm"
                            method="POST"
                            action="{{ route($routePrefix.'.update', $preference->uuid) }}"
                            enctype="multipart/form-data"
                            autocomplete="off">

                        @csrf
                        @method('PUT')

                        <div class="row">

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {{ __('preferences.title') }}
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                        name="title"
                                        class="form-control"
                                        value="{{ old('title', $preference->title) }}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('preferences.type') }}</label>

                                    <select name="type" class="form-control select2">

                                        <option value="">
                                            {{ __('preferences.select_type') }}
                                        </option>

                                        <option value="dietary"
                                            {{ $preference->type == 'dietary' ? 'selected' : '' }}>
                                            {{ __('preferences.dietary') }}
                                        </option>

                                        <option value="goals"
                                            {{ $preference->type == 'goals' ? 'selected' : '' }}>
                                            {{ __('preferences.goals') }}
                                        </option>

                                        <option value="food_allergies"
                                            {{ $preference->type == 'food_allergies' ? 'selected' : '' }}>
                                            {{ __('preferences.food_allergies') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>{{ __('preferences.image') }}</label>

                                    <input type="file"
                                        name="image"
                                        class="form-control">

                                    @if($preference->image)
                                        <img src="{{ getFileUrl($preference->image) }}"
                                            width="80"
                                            class="mt-2 rounded">
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>
                                        {{ __('preferences.status') }}
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select name="status" class="form-control select2">

                                        <option value="Active"
                                            {{ $preference->status == 'Active' ? 'selected' : '' }}>
                                            {{ __('preferences.active') }}
                                        </option>

                                        <option value="Inactive"
                                            {{ $preference->status == 'Inactive' ? 'selected' : '' }}>
                                            {{ __('preferences.inactive') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>{{ __('preferences.description') }}</label>

                                    <textarea name="description"
                                            rows="4"
                                            class="form-control">{{ old('description', $preference->description) }}</textarea>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer text-end px-0 mt-4">

                            <button type="submit"
                                    class="btn btn-success">
                                {{ __('preferences.update') }}
                            </button>

                            <a href="{{ route($routePrefix.'.index') }}"
                            class="btn btn-danger">
                                {{ __('preferences.cancel') }}
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

    $.validator.addMethod("numericOnly", function (value) {
        return this.optional(element) || /^[0-9]+$/.test(value.trim());
    }, jsValidate('numeric', { field: '{{ __("users.mobile_no") }}' }));

    $("#preferenceForm").validate({
        ignore: [],
        rules: {
            title: { required: true, minlength: 2 },
            type: { required: true },
            status: { required: true },
        },
        messages: {
            title: {
                required: jsValidate('required', { field: 'Name' }),
                minlength: jsValidate('min', { field: 'Name', min: 2 })
            },
            type: {
                required: jsValidate('required', { field: 'Type' })
            },
            status: {
                required: jsValidate('required', { field: 'Status' })
            },
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
});
</script>
@endsection
