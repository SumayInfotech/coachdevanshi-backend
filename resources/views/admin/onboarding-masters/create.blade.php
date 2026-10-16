@extends('admin.layouts.master')

@section('title', 'Create Onboarding Master')

@section('content')
<div class="main-container container-fluid">

    <div class="page-header">
        <h1 class="page-title">Create Onboarding Master</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item1 active">
                    <a href="{{ route($routePrefix . '.index') }}">Onboarding Masters</a>
                </li>
                <li class="breadcrumb-item1" aria-current="page">
                    Create
                </li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Master Option Information</h5>
        </div>
        <div class="card-body">

            <form id="onboardingMasterForm"
                  method="POST"
                  action="{{ route($routePrefix . '.store') }}"
                  enctype="multipart/form-data"
                  autocomplete="off">

                @csrf

                <div class="row g-3">

                    <!-- Category -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">
                                Category / Question <span class="text-danger">*</span>
                            </label>
                            <select name="category" id="category" class="form-control select2" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Title / Label -->
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="form-label">
                                Label / Title <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   class="form-control"
                                   value="{{ old('title') }}"
                                   placeholder="e.g. Vegetarian, Bloating, Type 1: Separate hard lumps"
                                   required>
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Sort Order -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label">
                                Sort Order
                            </label>
                            <input type="number"
                                   name="sort_order"
                                   id="sort_order"
                                   class="form-control"
                                   value="{{ old('sort_order', 0) }}"
                                   min="0"
                                   placeholder="0">
                            <small class="text-muted">Lower numbers appear first in the mobile app list.</small>
                            @error('sort_order')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-control select2" required>
                                <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Image / Icon -->
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label">
                                Icon / Image
                            </label>
                            <input type="file"
                                   name="image"
                                   id="image"
                                   class="form-control"
                                   accept="image/*">
                            <small class="text-muted">Optional: Upload an icon or reference image (e.g. Stool Chart type).</small>
                            @error('image')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description / Subtitle -->
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label">
                                Description / Subtitle
                            </label>
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      class="form-control"
                                      placeholder="Optional subtitle or helpful description for this option">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="card-footer text-end px-0 mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fa fa-check me-1"></i> Save Master
                    </button>
                    <a href="{{ route($routePrefix . '.index') }}" class="btn btn-danger">
                        <i class="fa fa-times me-1"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendor/js/jquery-validation.js') }}"></script>

<script>
$(document).ready(function () {
    if (typeof initSelect2 === 'function') {
        initSelect2();
    } else {
        $('.select2').select2({ width: '100%' });
    }

    $("#onboardingMasterForm").validate({
        ignore: [],
        rules: {
            category: { required: true },
            title:    { required: true, minlength: 2 },
            status:   { required: true },
        },
        messages: {
            category: { required: "Please select a category." },
            title: {
                required: "Please enter the label or title.",
                minlength: "Title must be at least 2 characters."
            },
            status: { required: "Please select a status." }
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
            form.submit();
        }
    });
});
</script>
@endsection
