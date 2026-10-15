@extends('admin.layouts.master')

@section('title', __('users.module_plural'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('users.module_plural') }}</h1>

        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active" aria-current="page">
                    {{ __('users.module_plural') }}
                </li>
            </ol>
        </div>
    </div>
    <!-- PAGE HEADER END -->

    <!-- ROW -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- CARD HEADER -->
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                    <h5 class="card-title mb-0">
                        {{ __('users.module_plural') }}
                    </h5>

                    <div class="d-flex align-items-center gap-2 export-btn-container">
                        <button class="btn btn-info"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse">
                            <i class="fa fa-sliders me-1"></i>
                            {{ __('users.filters') }}
                        </button>

                        @if($addPermission)
                            <a class="btn btn-success"
                               href="{{ route('admin.user.create') }}">
                                <i class="fa fa-plus"></i>
                                <span class="d-sm-inline-block">
                                    {{ __('users.add_title', ['module' => __('users.module_name')]) }}
                                </span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- FILTER PANEL -->
                <div class="collapse" id="filterCollapse">
                    <div class="card-body border-bottom">
                        <form id="filters" class="row g-3">

                            <!-- State -->
                            {{-- <div class="col-md-4">
                                <label class="form-label">
                                    {{ __('users.state') }}
                                </label>
                                <select name="state_id"
                                        id="state_id"
                                        class="form-control select2-searchable">
                                    <option value="">
                                        {{ __('users.all_states') }}
                                    </option>
                                    @foreach($states ?? [] as $state)
                                        <option value="{{ $state->id }}">
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <!-- City -->
                            {{-- <div class="col-md-4">
                                <label class="form-label">
                                    {{ __('users.city') }}
                                </label>
                                <select name="city_id"
                                        id="city_id"
                                        class="form-control select2-searchable">
                                    <option value="">
                                        {{ __('users.all_cities') }}
                                    </option>
                                    @foreach($cities ?? [] as $city)
                                        <option value="{{ $city->id }}">
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}

                            <!-- Status -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ __('users.status') }}
                                </label>
                                <select name="status"
                                        id="status"
                                        class="form-control">
                                    <option value="">
                                        {{ __('users.all_status') }}
                                    </option>
                                    <option value="Active">{{ __('users.active') }}</option>
                                    <option value="Inactive">{{ __('users.inactive') }}</option>
                                </select>
                            </div>
                            <!-- User Type -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ __('users.user_type') }}
                                </label>
                                <select name="user_type"
                                        id="user_type"
                                        class="form-control">
                                    <option value="">
                                        {{ __('users.all_user_types') }}
                                    </option>
                                    <option value="admin" selected>{{ __('users.admin') }}</option>
                                    <option value="customer">{{ __('users.customer') }}</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 d-flex justify-content-end mt-2">
                                <button type="button"
                                        id="searchBtn"
                                        class="btn btn-primary me-2">
                                    <i class="bx bx-search"></i>
                                    {{ __('users.search') }}
                                </button>
                                <button type="button"
                                        id="resetBtn"
                                        class="btn btn-secondary">
                                    <i class="bx bx-reset"></i>
                                    {{ __('users.reset') }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card-body table-responsive">
                    {!! $dataTable->table(['class' => 'table users-table table-bordered'], true) !!}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('page-css')
  
@endsection
@section('scripts')
    <!-- Page JS -->
    <!-- DATA TABLE JS-->
    <script src="{{ asset('assets/sash/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>
    <script src=" {{ asset('assets/sash/js/table-data.js') }}"></script>
    {!! $dataTable->scripts() !!}
    <script>
    $(document).ready(function () {
        //DataTable Name Define
        const table = '.users-table';

        //Select2 Init
        initSelect2();
        
        //DataTable Filters Init
        initDataTableFilters(table, ['state_id','city_id','status','user_type']);

        //Bind Delete Action
        bindDeleteAction(table,"{{ route($routePrefix . '.delete', '') }}");

        //Bind Status Toggle Action
        bindStatusToggle(table,"{{ route($routePrefix . '.change-status', '') }}");

        //Bind State-City Dropdown
        bindStateCity('#state_id','#city_id',"{{ route($routePrefix . '.get.cities') }}");
    });
    </script>
@endsection
