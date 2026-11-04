@php
    use App\Models\Role;
    $data = Auth::user();
    $headerRole = null;

    if (Auth::check() && Auth::user()->role_uuid) {
        $headerRole = Role::withoutGlobalScope('exclude_super_admin')
            ->where('uuid', Auth::user()->role_uuid)
            ->value('name');
    }
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $headerName = trim(($data->name ?? '') . ' ' . ($data->last_name ?? '')) ?: 'Admin';
    $initials = collect(preg_split('/\s+/', $headerName))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp
<div class="app-header header sticky">
    <div class="container-fluid main-container">
        <div class="d-flex align-items-center w-100">
            <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar" href="javascript:void(0)"></a>

            <a class="logo-horizontal" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('assets/brand/logo-light.png') }}" class="header-brand-img desktop-logo" alt="logo">
                <img src="{{ asset('assets/brand/logo.png') }}" class="header-brand-img light-logo1" alt="logo">
            </a>

            <div class="dd-header-title">
                <span class="dd-hello">{{ now()->format('l, d F Y') }}</span>
                <span class="dd-page">{{ $greeting }}, {{ $data->name ?? 'Admin' }}</span>
            </div>

            <div class="d-flex align-items-center ms-auto header-right-icons">
                <a class="nav-link icon theme-layout nav-link-bg layout-setting" href="javascript:void(0)" title="Toggle dark mode">
                    <span class="dark-layout"><i class="fe fe-moon"></i></span>
                    <span class="light-layout"><i class="fe fe-sun"></i></span>
                </a>

                <a class="nav-link icon full-screen-link nav-link-bg d-none d-md-inline-flex" href="javascript:void(0)" title="Full screen">
                    <i class="fe fe-maximize fullscreen-button"></i>
                </a>

                <div class="dropdown d-flex profile-1">
                    <a href="javascript:void(0)" data-bs-toggle="dropdown" class="nav-link leading-none dd-profile-btn">
                        @if($data->profile_image)
                            <img src="{{ getFileUrl($data->profile_image) }}" alt="profile" class="avatar profile-user cover-image">
                        @else
                            <span class="dd-avatar-initials">{{ $initials }}</span>
                        @endif
                        <span class="d-none d-sm-flex flex-column text-start">
                            <span class="dd-profile-name">{{ $headerName }}</span>
                            <span class="dd-profile-role">{{ str_replace('_', ' ', $headerRole ?? 'Admin') }}</span>
                        </span>
                        <i class="fe fe-chevron-down text-muted d-none d-sm-inline"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="drop-heading">
                            <div class="fw-semibold" style="color: var(--dd-ink)">{{ $headerName }}</div>
                            @if(!empty($data->email))
                                <div class="small text-muted">{{ $data->email }}</div>
                            @endif
                            @if(!empty($data->mobile))
                                <div class="small text-muted">{{ $data->mobile }}</div>
                            @endif
                        </div>
                        <div class="dropdown-divider my-1"></div>
                        <a class="dropdown-item" href="{{ route('admin.user.view', $data->uuid) }}">
                            <i class="dropdown-icon fe fe-user"></i> {{ __('header.profile') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.change-password') }}">
                            <i class="dropdown-icon fe fe-lock"></i> {{ __('header.change_password') }}
                        </a>
                        <div class="dropdown-divider my-1"></div>
                        <a class="dropdown-item" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="dropdown-icon fe fe-log-out"></i> {{ __('header.logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="GET" class="d-none"></form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
