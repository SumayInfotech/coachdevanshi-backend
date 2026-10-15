@php
    $editPermission   = hasPermission('edit_user');
    $deletePermission = hasPermission('delete_user');
@endphp

@extends('admin.layouts.master')

@section('title', __('users.module_name').' - '.__('users.view'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('users.module_plural') }}</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}"> {{ __('sidebar.dashboard') }}</a>
                </li>
                <li class="breadcrumb-item1 active">
                    <a href="{{ route($routePrefix) }}"> {{ __('users.module_plural') }}</a>
                </li>
                <li class="breadcrumb-item1" aria-current="page">
                     {{ __('users.view') }}
                </li>
            </ol>
        </div>
    </div>
    <!-- PAGE HEADER END -->

    <div class="row" id="user-profile">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="wideget-user mb-2">
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="row">
                                    <div class="panel profile-cover">
                                        <div class="profile-cover__action bg-img">
                                            <!-- <img src="{{ asset('assets/sash/images/media/about-hero.jpg') }}" alt="img"> -->
                                        </div>
                                        <div class="profile-cover__img">
                                            <div class="profile-img-1">
                                                <!-- <img src="{{ getFileUrl($data->profile_image) ? getFileUrl($data->profile_image) : asset('assets/sash/images/users/21.jpg') }}" alt="img"> -->
                                                <img src="{{ $data->profile_image
                                                            ? getFileUrl($data->profile_image)
                                                            : asset('assets/sash/images/users/21.jpg') }}"
                                                             alt="img">
                                            </div>
                                            <div class="profile-img-content text-dark text-start">
                                                <div class="text-dark">
                                                    <h3 class="h3 mb-2">{{ $data->name}}</h3>
                                                    <h5 class="text-muted">{{ formatName($data->role?->name) ?? __('users.na')}}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="btn-profile">
                                            <button class="btn btn-primary mt-1 mb-1"> <i class="fa fa-rss"></i> <span>Follow</span></button>
                                            <button class="btn btn-secondary mt-1 mb-1"> <i class="fa fa-envelope"></i> <span>Message</span></button>
                                        </div> -->
                                    </div>
                                </div>
                                <!-- <div class="row">
                                    <div class="px-0 px-sm-4">
                                        <div class="social social-profile-buttons mt-5 float-end">
                                            <div class="mt-3">
                                                <a class="social-icon text-primary" href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook"></i></a>
                                                <a class="social-icon text-primary" href="https://twitter.com/" target="_blank"><i class="fa fa-twitter"></i></a>
                                                <a class="social-icon text-primary" href="https://www.youtube.com/" target="_blank"><i class="fa fa-youtube"></i></a>
                                                <a class="social-icon text-primary" href="javascript:void(0)"><i class="fa fa-rss"></i></a>
                                                <a class="social-icon text-primary" href="https://www.linkedin.com/" target="_blank"><i class="fa fa-linkedin"></i></a>
                                                <a class="social-icon text-primary" href="https://myaccount.google.com/" target="_blank"><i class="fa fa-google-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-end">
                                        <div class="d-flex align-items-center gap-3">

                                            {{-- STATUS --}}
                                            @if(in_array($data->status, ['Active', 'Inactive']))
                                                <button type="button"
                                                        class="status-change-btn btn btn-{{ $data->status === 'Active' ? 'success' : 'danger' }}
                                                            text-nowrap d-inline-flex position-relative"
                                                        data-uuid="{{ $data->uuid }}"
                                                        data-status="{{ $data->status }}"
                                                        style="cursor:pointer;">
                                                     {{ __('users.'.strtolower($data->status)) }}
                                                    <span class="position-absolute top-0 start-100 translate-middle badge badge-dot border border-2 p-2 bg-{{ $data->status === 'Active' ? 'success' : 'danger' }}">
                                                        
                                                    </span>
                                                </button>
                                            @endif

                                            {{-- EDIT --}}
                                            @if($editPermission)
                                                <a href="{{ route($routePrefix.'.edit', $data->uuid) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="{{ __('users.edit') }}">
                                                    <i class="ri-pencil-fill"></i>
                                                </a>
                                            @endif

                                            {{-- DELETE --}}
                                            @if($deletePermission)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-uuid="{{ $data->uuid }}"
                                                        title="{{ __('users.delete') }}">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-header">
                             {{ __('users.module_name') }} {{ __('users.details') }}
                        </div>

                        <div class="card-body">
                            <ul class="list-group list-group-flush">

                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold">{{ __('users.name') }}</span>
                                    <span>{{ $data->name }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold">{{ __('users.email') }}</span>
                                    <span>{{ $data->email }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold">{{ __('users.mobile_no') }}</span>
                                    <span>{{ $data->mobile }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold">{{ __('users.city') }}</span>
                                    <span>{{ $data->city->name ?? '-' }}</span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="fw-semibold">{{ __('users.state') }}</span>
                                    <span>{{ $data->state->name ?? '-' }}</span>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="card">
                <div class="card-body table-responsive">
                    {!! $dataTable->table(['class' => 'table users-table table-bordered'], true) !!}
                </div>
            </div> --}}
        </div>
    </div>

</div>
                   
@endsection
@section('scripts')
<script>
$(document).ready(function () {

    // reuse global handlers
    bindDeleteAction(null, "{{ route($routePrefix . '.delete', '') }}");
    bindViewStatusChange("{{ route($routePrefix . '.change-status', '') }}");

});
</script>
@endsection


