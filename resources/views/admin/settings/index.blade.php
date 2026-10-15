@extends('admin.layouts.master')

@section('title', __('settings.page_title'))


@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('settings.page_title') }}</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">{{ __('sidebar.dashboard') }}</a>
                </li>
                <li class="breadcrumb-item1 active" aria-current="page">
                        {{ __('settings.settings') }}
                </li>
            </ol>
        </div>
    </div>
    <!-- PAGE HEADER END -->

    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- CARD HEADER -->
                <div class="card-header">
                    <h3 class="card-title">{{ __('settings.card_title') }}</h3>
                </div>

                <!-- CARD BODY -->
                <div class="card-body">

                    <form id="settingForm"
                          method="POST"
                          action="{{ route('admin.settings.update') }}"
                          enctype="multipart/form-data"
                          autocomplete="off">
                        @csrf

                        <div class="row">

                            <!-- Project Title -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label>
                                         {{ __('settings.project_title') }}<span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           name="project_title"
                                           value="{{ old('project_title', setting('project_title')) }}">
                                </div>
                            </div>

                            <!-- Pagination Length -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label>
                                       {{ __('settings.pagination_length') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="pagination_length"
                                            class="form-control select2">
                                        <option value="">{{ __('settings.select_pagination') }}</option>
                                        @foreach(pagination_length_options() as $length)
                                            <option value="{{ $length }}"
                                                {{ setting('pagination_length') == $length ? 'selected' : '' }}>
                                                {{ $length }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Application Language -->
                            {{-- <div class="col-lg-4 col-md-4">
                                <div class="form-group">
                                    <label>
                                        {{ __('settings.language') }} <span class="text-danger">*</span>
                                    </label>
                                    <select name="language" class="form-control select2" required>
                                        <option value="">Select Language</option>
                                        <option value="en"
                                            {{ setting('language', 'en') === 'en' ? 'selected' : '' }}>
                                          {{ __('settings.lang_en') }}
                                        </option>
                                        <option value="it"
                                            {{ setting('language') === 'it' ? 'selected' : '' }}>
                                            {{ __('settings.lang_it') }}
                                        </option>
                                    </select>
                                </div>
                            </div> --}}

                            <!-- Footer Text -->
                            <div class="col-lg-12 col-md-12">
                                <div class="form-group">
                                      <label>{{ __('settings.footer_text') }}</label>
                                    <input type="text" name="footer_text" class="form-control" value="{{ old('footer_text', setting('footer_text')) }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr style="border:dashed 1.5px #e70707;">
                            </div>

                            <!-- App Icon -->
                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label>{{ __('settings.app_icon') }}</label>
                                    <input type="file"
                                           class="form-control"
                                           name="app_icon"
                                           accept="image/png,image/x-icon">

                                    @if(setting('app_icon'))
                                        <div class="mt-2">
                                            <a href="{{ getFileUrl(setting('app_icon')) }}" target="_blank">
                                                <img src="{{ getFileUrl(setting('app_icon')) }}"
                                                    height="40"
                                                    style="border-radius:6px;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Favicon Icon -->
                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label>{{ __('settings.favicon_icon') }}</label>
                                    <input type="file"
                                           class="form-control"
                                           name="favicon_icon"
                                           accept="image/png,image/x-icon">

                                    @if(setting('favicon_icon'))
                                        <div class="mt-2">
                                            <a href="{{ getFileUrl(setting('favicon_icon')) }}" target="_blank">
                                                <img src="{{ getFileUrl(setting('favicon_icon')) }}"
                                                    height="40"
                                                    style="border-radius:6px;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            

                            <!-- Loader Image -->
                            <div class="col-lg-4 col-md-12">
                                <div class="form-group">
                                    <label>{{ __('settings.loader_image') }}</label>
                                    <input type="file"
                                           class="form-control"
                                           name="loader_image"
                                           accept="image/*">

                                    @if(setting('loader_image'))
                                        <div class="mt-2">
                                            <a href="{{ getFileUrl(setting('loader_image')) }}" target="_blank">
                                                <img src="{{ getFileUrl(setting('loader_image')) }}"
                                                    height="60"
                                                    style="border-radius:6px;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <hr style="border:dashed 1.5px #e70707;">
                            </div>
                            <!-- Sidebar Logo : Dark Mode (Full) -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                     <label>{{ __('settings.sidebar_dark_full') }}</label>
                                    <input type="file"
                                        class="form-control"
                                        name="sidebar_logo_dark_full"
                                        accept="image/*">

                                    @if(setting('sidebar_logo_dark_full'))
                                        <div class="mt-2">
                                            <a href="{{ getFileUrl(setting('sidebar_logo_dark_full')) }}" target="_blank">
                                                <img src="{{ getFileUrl(setting('sidebar_logo_dark_full')) }}"
                                                    height="60"
                                                    style="object-fit:contain;border-radius:6px;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            
                            <!-- Sidebar Logo : Dark Mode (Toggle/Icon) -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                     <label>{{ __('settings.sidebar_dark_toggle') }}</label>
                                    <input type="file"
                                        class="form-control"
                                        name="sidebar_logo_dark_toggle"
                                        accept="image/*">

                                    @if(setting('sidebar_logo_dark_toggle'))
                                        <div class="mt-2">
                                            <a href="{{ getFileUrl(setting('sidebar_logo_dark_toggle')) }}" target="_blank">
                                                <img src="{{ getFileUrl(setting('sidebar_logo_dark_toggle')) }}"
                                                    height="45"
                                                    style="object-fit:contain;border-radius:6px;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr style="border:dashed 1.5px #e70707;">
                            </div>
                            <!-- Sidebar Logo : Light Mode (Full) -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                     <label>{{ __('settings.sidebar_light_full') }}</label>
                                    <input type="file"
                                        class="form-control"
                                        name="sidebar_logo_light_full"
                                        accept="image/*">

                                    @if(setting('sidebar_logo_light_full'))
                                        <div class="mt-2">
                                            <a href="{{ getFileUrl(setting('sidebar_logo_light_full')) }}" target="_blank">
                                                <img src="{{ getFileUrl(setting('sidebar_logo_light_full')) }}"
                                                    height="60"
                                                    style="object-fit:contain;border-radius:6px;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Sidebar Logo : Light Mode (Toggle/Icon) -->
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label>{{ __('settings.sidebar_light_toggle') }}</label>
                                    <input type="file"
                                        class="form-control"
                                        name="sidebar_logo_light_toggle"
                                        accept="image/*">

                                    @if(setting('sidebar_logo_light_toggle'))
                                        <div class="mt-2">
                                            <a href="{{ getFileUrl(setting('sidebar_logo_light_toggle')) }}" target="_blank">
                                                <img src="{{ getFileUrl(setting('sidebar_logo_light_toggle')) }}"
                                                    height="45"
                                                    style="object-fit:contain;border-radius:6px;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <hr style="border:dashed 1.5px #e70707;">
                                <h4 class="mt-3 mb-3">{{ __('settings.smtp_settings') }}</h4>
                            </div>

                            <!-- Mail Mailer -->
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('settings.mail_mailer') }} <span class="text-danger">*</span></label>
                                    <input type="text"
                                        name="mail_mailer"
                                        class="form-control"
                                        value="{{ old('mail_mailer', setting('mail_mailer', 'smtp')) }}">
                                </div>
                            </div>

                            <!-- Mail Host -->
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('settings.mail_host') }} <span class="text-danger">*</span></label>
                                    <input type="text"
                                        name="mail_host"
                                        class="form-control"
                                        value="{{ old('mail_host', setting('mail_host')) }}">
                                </div>
                            </div>

                            <!-- Mail Port -->
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('settings.mail_port') }} <span class="text-danger">*</span></label>
                                    <input type="number"
                                        name="mail_port"
                                        class="form-control"
                                        value="{{ old('mail_port', setting('mail_port', 587)) }}">
                                </div>
                            </div>

                            <!-- Mail Username -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('settings.mail_username') }} <span class="text-danger">*</span></label>
                                    <input type="email"
                                        name="mail_username"
                                        class="form-control"
                                        value="{{ old('mail_username', setting('mail_username')) }}">
                                </div>
                            </div>

                            <!-- Mail Password -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('settings.mail_password') }}</label>
                                    <input type="password"
                                        name="mail_password"
                                        class="form-control"
                                        placeholder="••••••••"
                                        value="{{ old('mail_password', setting('mail_password')) }}">
                                </div>
                            </div>

                            <!-- Mail Encryption -->
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('settings.mail_encryption') }}</label>
                                    <select name="mail_encryption" class="form-control select2">
                                        <option value="">None</option>
                                        <option value="tls" {{ setting('mail_encryption') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ setting('mail_encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Mail From Address -->
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('settings.mail_from_address') }} <span class="text-danger">*</span></label>
                                    <input type="email"
                                        name="mail_from_address"
                                        class="form-control"
                                        value="{{ old('mail_from_address', setting('mail_from_address')) }}">
                                </div>
                            </div>

                            <!-- Mail From Name -->
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group">
                                    <label>{{ __('settings.mail_from_name') }}</label>
                                    <input type="text"
                                        name="mail_from_name"
                                        class="form-control"
                                        value="{{ old('mail_from_name', setting('mail_from_name', config('app.name'))) }}">
                                </div>
                            </div>
                            

                            

                        </div>

                        <!-- CARD FOOTER -->
                        <div class="card-footer text-end px-0 mt-4">
                            <button type="submit" class="btn btn-success my-1">
                             {{ __('settings.save') }}
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

    $("#settingForms").validate({
        ignore: [],
        rules: {
            project_title: {
                required: true,
                minlength: 2
            },
            pagination_length: {
                required: true
            }
        },
         messages: {
            project_title: {
                required: jsValidate('required', { field: 'Project Title' }),
                minlength: jsValidate('min', { field: 'Project Title', min: 2 })
            },
            pagination_length: {
                required: jsValidate('required', { field: 'Pagination Length' })
            }
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

