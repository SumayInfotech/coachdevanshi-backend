@extends('admin.layouts.master')

@section('title', __('roles.module_plural'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE-HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('roles.module_plural') }}</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">{{ __('roles.dashboard') }}</a>
                </li>
                <li class="breadcrumb-item1 active" aria-current="page">
                    {{ __('roles.module_name') }}
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
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                    <h5 class="card-title mb-0"> {{ __('roles.module_plural') }}</h5>

                    <div class="d-flex align-items-center gap-2 export-btn-container">
                        @if($addPermission)
                            <a class="btn btn-success waves-effect waves-light"
                               href="{{ route($routePrefix . '.create') }}">
                                <i class="fa fa-plus"></i>
                                <span class="d-sm-inline-block">
                                    Add  {{ __('roles.add_title', ['module' => __('roles.module_name')]) }}
                                </span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card-body table-responsive">
                    {!! $dataTable->table(['class' => 'table roles-table table-bordered'], true) !!}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- DATATABLE JS -->
    <script src="{{ asset('assets/sash/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function () {

            // DataTable selector
            const table = '.roles-table';

            // Bind delete action
            bindDeleteAction(table, "{{ route($routePrefix . '.delete', '') }}");

            // Bind status toggle
            bindStatusToggle(table, "{{ route($routePrefix . '.change-status', '') }}");

        });
    </script>
@endsection
