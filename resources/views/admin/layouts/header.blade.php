@php
    use App\Models\Role;
    $data = Auth::user();
    $headerRole = null;

    if (Auth::check() && Auth::user()->role_uuid) {
        $headerRole = Role::withoutGlobalScope('exclude_super_admin')
            ->where('uuid', Auth::user()->role_uuid)
            ->value('name');
    }
@endphp
<div class="app-header header sticky">
                <div class="container-fluid main-container">
                    <div class="d-flex">
                        <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar" href="javascript:void(0)"></a>
                        <!-- sidebar-toggle-->
                        <a class="logo-horizontal " href="index.html">
                            <img src="{{ asset('assets/sash/images/brand/logo-white.png') }}" class="header-brand-img desktop-logo" alt="logo">
                            <img src="{{ asset('assets/sash/images/brand/logo-dark.png') }}" class="header-brand-img light-logo1" alt="logo">
                        </a>
                        <!-- LOGO -->
                        <!-- <div class="main-header-center ms-3 d-none d-lg-block">
                            <input type="text" class="form-control" id="typehead" placeholder="Search for results...">
                            <button class="btn px-0 pt-2"><i class="fe fe-search" aria-hidden="true"></i></button>
                        </div> -->
                        <div class="d-flex order-lg-2 ms-auto header-right-icons">
                            <!-- SEARCH -->
                            <button class="navbar-toggler navresponsive-toggler d-lg-none ms-auto" type="button"
                                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4"
                                aria-controls="navbarSupportedContent-4" aria-expanded="false"
                                aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon fe fe-more-vertical"></span>
                            </button>
                            <div class="navbar navbar-collapse responsive-navbar p-0">
                                <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                                    <div class="d-flex order-lg-2">
                                        <div class="dropdown d-lg-none d-flex">
                                            <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="dropdown">
                                                <i class="fe fe-search"></i>
                                            </a>
                                            <div class="dropdown-menu header-search dropdown-menu-start">
                                                <div class="input-group w-100 p-2">
                                                    <input type="text" class="form-control" placeholder="Search....">
                                                    <div class="input-group-text btn btn-primary">
                                                        <i class="fa fa-search" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <a class="nav-link icon theme-layout nav-link-bg layout-setting">
                                                <span class="dark-layout"><i class="fe fe-moon"></i></span>
                                                <span class="light-layout"><i class="fe fe-sun"></i></span>
                                            </a>
                                        </div>
                                        <div class="d-flex country dropdown">
                                            <!-- <a class="nav-link icon text-center dropdown-toggle"
                                            href="javascript:void(0)"
                                            data-bs-toggle="dropdown">
                                                <i class="fe fe-globe"></i>
                                                <span class="fs-16 ms-2 d-none d-xl-block">
                                                    {{ app()->getLocale() === 'it' ? 'Italiano' : 'English' }}
                                                </span>
                                            </a> -->

                                            <!-- <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item language-switch" data-lang="en" href="javascript:void(0)">
                                                    🇬🇧 English
                                                </a>
                                                <a class="dropdown-item language-switch" data-lang="it" href="javascript:void(0)">
                                                    🇮🇹 Italiano
                                                </a>
                                            </div> -->
                                        </div>
                                        
                                        <!-- CART -->
                                        <div class="dropdown d-flex">
                                            <a class="nav-link icon full-screen-link nav-link-bg">
                                                <i class="fe fe-minimize fullscreen-button"></i>
                                            </a>
                                        </div>
                                        <!-- FULL-SCREEN -->
                                        
                                        <!-- SIDE-MENU -->
                                        <div class="dropdown d-flex profile-1">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="nav-link leading-none d-flex">
                                                <!-- <img src="{{ asset('assets/sash/images/users/21.jpg') }}" alt="profile-user" class="avatar  profile-user brround cover-image"> -->
                                                <img src="{{ $data->profile_image
                                                            ? getFileUrl($data->profile_image)
                                                            : asset('assets/sash/images/users/21.jpg') }}" alt="profile-user" class="avatar  profile-user brround cover-image">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading">
                                                    <div class="text-center">
                                                        <h5 class="text-dark mb-0 fs-14 fw-semibold">{{ Auth::user()->name }}</h5>                                                        
                                                        <small class="text-muted">{{ str_replace('_', ' ', ucfirst($headerRole ?? 'Unknown Role')) }}</small>
                                                        
                                                        @if(!empty(Auth::user()->mobile))
                                                            <span class="fw-medium d-block small mt-2">
                                                                <i class="ri-phone-line me-1"></i> {{ Auth::user()->mobile }}
                                                            </span>
                                                        @endif

                                                        @if(!empty(Auth::user()->email))
                                                            <span class="fw-medium d-block small mt-2">
                                                                <i class="ri-mail-line me-1"></i> {{ Auth::user()->email }}
                                                            </span>
                                                        @endif
                                                        
                                                    </div>
                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <a class="dropdown-item" href="{{ route('admin.user.view', $data->uuid)}}">
                                                    <i class="dropdown-icon fe fe-user"></i> {{ __('header.profile') }}
                                                </a>
                                                <a class="dropdown-item" href="{{ route('admin.change-password')}}">
                                                    <i class="dropdown-icon fa fa-cogs"></i> {{ __('header.change_password') }}
                                                </a>
                                                <!-- <a class="dropdown-item" href="email-inbox.html">
                                                    <i class="dropdown-icon fe fe-mail"></i> Inbox
                                                    <span class="badge bg-danger rounded-pill float-end">5</span>
                                                </a>
                                                <a class="dropdown-item" href="lockscreen.html">
                                                    <i class="dropdown-icon fe fe-lock"></i> Lockscreen
                                                </a> -->
                                                <!-- <a class="dropdown-item" href="login.html">
                                                    <i class="dropdown-icon fe fe-alert-circle"></i> Sign out
                                                </a> -->
                                               
                                                <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <!-- <small class="align-middle">Logout</small> -->
                                                   <i class="dropdown-icon fa fa-sign-out"></i> {{ __('header.logout') }}
                                                </a>
                                                <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" class="d-none"></form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>