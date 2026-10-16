@php
    $currentController = class_basename(Route::current()->controller);
@endphp
<!--APP-SIDEBAR-->
<div class="sticky">
    <div class="app-sidebar__overlay" data-bs-toggle="sidebar">

    </div>
    <div class="app-sidebar">
        <div class="side-header">
            <!-- <a class="header-brand1" href="index.html">
                <img src="{{ asset('assets/sash/images/brand/logo-white.png') }}" class="header-brand-img desktop-logo" alt="logo">
                <img src="{{ asset('assets/sash/images/brand/icon-white.png') }}" class="header-brand-img toggle-logo"
                    alt="logo">
                <img src="{{ asset('assets/sash/images/brand/icon-dark.png') }}" class="header-brand-img light-logo" alt="logo">
                <img src="{{ asset('assets/sash/images/brand/logo-dark.png') }}" class="header-brand-img light-logo1"
                    alt="logo">
            </a> -->
            <a class="header-brand1" href="{{ route('admin.dashboard') }}">

                <!-- Dark Mode - Full Logo -->
                <img src="{{ setting('sidebar_logo_dark_full')
                            ? getFileUrl(setting('sidebar_logo_dark_full'))
                            : asset('assets/sash/images/brand/logo-white.png') }}"
                    class="header-brand-img desktop-logo"
                    alt="logo">

                <!-- Dark Mode - Toggle Logo -->
                <img src="{{ setting('sidebar_logo_dark_toggle')
                            ? getFileUrl(setting('sidebar_logo_dark_toggle'))
                            : asset('assets/sash/images/brand/icon-white.png') }}"
                    class="header-brand-img toggle-logo"
                    alt="logo">

                <!-- Light Mode - Toggle Logo -->
                <img src="{{ setting('sidebar_logo_light_toggle')
                            ? getFileUrl(setting('sidebar_logo_light_toggle'))
                            : asset('assets/sash/images/brand/icon-dark.png') }}"
                    class="header-brand-img light-logo"
                    alt="logo">

                <!-- Light Mode - Full Logo -->
                <img src="{{ setting('sidebar_logo_light_full')
                            ? getFileUrl(setting('sidebar_logo_light_full'))
                            : asset('assets/sash/images/brand/logo-dark.png') }}"
                    class="header-brand-img light-logo1"
                    alt="logo">

            </a>
            <!-- LOGO -->
        </div>
        <div class="main-sidemenu">
            <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg"
                    fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                </svg>
            </div>
            <ul class="side-menu">
                <li class="sub-category">
                   <h3>{{ __('sidebar.main') }}</h3>
                </li>

                <!-- Dashboard -->
                <li class="slide {{ $currentController === 'DashboardController' ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item has-link {{ $currentController === 'DashboardController' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="side-menu__icon fe fe-home"></i>
                        <span class="side-menu__label">{{ __('sidebar.dashboard') }}</span>
                    </a>
                </li>

                {{-- AUTHENTICATION MENU (Parent) --}}
                @if(hasPermission('view_user') || hasPermission('view_role'))
                    @php
                        $authActive = in_array($currentController, ['UserController','RoleController']);
                    @endphp

                    <li class="slide {{ $authActive ? 'is-expanded active' : '' }}">
                        <a class="side-menu__item {{ $authActive ? 'active' : '' }}" data-bs-toggle="slide" href="javascript:void(0)">
                            <i class="side-menu__icon fe fe-users"></i>
                            <span class="side-menu__label">{{ __('sidebar.authentication') }}</span>
                            <i class="angle fe fe-chevron-right"></i>
                        </a>
                        <ul class="slide-menu">
                            <li class="panel sidetab-menu">
                                <div class="panel-body tabs-menu-body p-0 border-0">
                                    <div class="tab-content">
                                        <div class="tab-pane active">
                                            <ul class="sidemenu-list">

                                                {{-- USER MANAGEMENT --}}
                                                @if(hasPermission('view_user'))
                                                    <li>
                                                        <a href="{{ route('admin.user') }}"
                                                        class="slide-item {{ $currentController === 'UserController' ? 'active' : '' }}">
                                                           {{ __('sidebar.user_management') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                {{-- ROLE MANAGEMENT --}}
                                                @if(hasPermission('view_role'))
                                                    <li>
                                                        <a href="{{ route('admin.roles') }}"
                                                        class="slide-item {{ $currentController === 'RoleController' ? 'active' : '' }}">
                                                            {{ __('sidebar.role_management') }}
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                @endif
                <!-- Setting -->
                @if(hasPermission('view_settings'))
                <li class="slide {{ $currentController === 'SettingsController' ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item has-link {{ $currentController === 'SettingsController' ? 'active' : '' }}" href="{{ route('admin.settings') }}">
                        <i class="side-menu__icon fe fe-settings"></i>
                        <span class="side-menu__label">{{ __('sidebar.settings') }}</span>
                    </a>
                </li>
                @endif

                <!-- Preference -->
                @if(hasPermission('view_preference'))
                <li class="slide {{ $currentController === 'PreferenceController' ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item has-link {{ $currentController === 'PreferenceController' ? 'active' : '' }}" href="{{ route('admin.preferences.index') }}">
                        <i class="side-menu__icon fe fe-package"></i>
                        <span class="side-menu__label">{{ __('sidebar.preference_management') }}</span>
                    </a>
                </li>
                @endif

                <!-- Onboarding Masters -->
                @if(hasPermission('view_onboarding_master'))
                <li class="slide {{ $currentController === 'OnboardingMasterController' ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item has-link {{ $currentController === 'OnboardingMasterController' ? 'active' : '' }}" href="{{ route('admin.onboarding-masters.index') }}">
                        <i class="side-menu__icon fe fe-check-square"></i>
                        <span class="side-menu__label">Onboarding Masters</span>
                    </a>
                </li>
                @endif


                <!-- Ingredients -->
                @if(hasPermission('view_ingredients'))
                <li class="slide {{ $currentController === 'IngredientsController' ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item has-link {{ $currentController === 'IngredientsController' ? 'active' : '' }}" href="{{ route('admin.ingredients.index') }}">
                        <i class="side-menu__icon fe fe-target"></i>
                        <span class="side-menu__label">{{ __('sidebar.ingredients_management') }}</span>
                    </a>
                </li>
                @endif

                <!-- Category -->
                @if(hasPermission('view_category'))
                <li class="slide {{ $currentController === 'CategoryController' ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item has-link {{ $currentController === 'CategoryController' ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="side-menu__icon fe fe-layers"></i>
                        <span class="side-menu__label">{{ __('sidebar.category_management') }}</span>
                    </a>
                </li>
                @endif

                <!-- Preparation Card -->
                @if(hasPermission('view_preparation_card'))
                <li class="slide {{ $currentController === 'PreparationCardController' ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item has-link {{ $currentController === 'PreparationCardController' ? 'active' : '' }}"
                    href="{{ route('admin.preparation-cards.index') }}">
                        <i class="side-menu__icon fe fe-clipboard"></i>
                        <span class="side-menu__label">{{ __('sidebar.preparation_card_management') }}</span>
                    </a>
                </li>
                @endif

                <!-- Recipe -->
                @if(hasPermission('view_recipe'))
                <li class="slide {{ $currentController === 'RecipeController' ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item has-link {{ $currentController === 'RecipeController' ? 'active' : '' }}"
                    href="{{ route('admin.recipes.index') }}">
                        <i class="side-menu__icon fe fe-book-open"></i>
                        <span class="side-menu__label">{{ __('sidebar.recipe_management') }}</span>
                    </a>
                </li>
                @endif

                <!-- Course Management (Parent) -->
                @if(hasPermission('view_course') || hasPermission('view_chapter') || hasPermission('view_lesson'))
                    @php
                        $courseActive = in_array($currentController, [
                            'CourseController',
                            'ChapterController',
                            'LessonController'
                        ]);
                    @endphp

                    <li class="slide {{ $courseActive ? 'is-expanded active' : '' }}">
                        <a class="side-menu__item has-link {{ $courseActive ? 'active' : '' }}"
                        href="{{ route('admin.courses.index') }}">
                            <i class="side-menu__icon fe fe-award"></i>
                            <span class="side-menu__label">{{ __('sidebar.course_management') }}</span>
                        </a>
                    </li>
                @endif


            </ul>
             
            <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                    width="24" height="24" viewBox="0 0 24 24">
                    <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                </svg>
            </div>
        </div>
    </div>
</div>
<!--/APP-SIDEBAR-->