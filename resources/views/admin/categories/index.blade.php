@extends('admin.layouts.master')

@section('title', __('categories.module_plural'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('categories.module_plural') }}</h1>

        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('categories.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active" aria-current="page">
                    {{ __('categories.module_plural') }}
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
                        {{ __('categories.module_plural') }}
                    </h5>

                    <div class="d-flex align-items-center gap-2 export-btn-container">
                        <button class="btn btn-info"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse">
                            <i class="fa fa-sliders me-1"></i>
                            {{ __('categories.filters') }}
                        </button>

                        @if($addPermission)
                            <a class="btn btn-success"
                               href="{{ route($routePrefix.'.create') }}">
                                <i class="fa fa-plus"></i>
                                <span class="d-sm-inline-block">
                                    {{ __('categories.create_title') }}
                                </span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- FILTER PANEL -->
                <div class="collapse" id="filterCollapse">
                    <div class="card-body border-bottom">
                        <form id="filters" class="row g-3">
                            <!-- Status -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ __('categories.status') }}
                                </label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">{{ __('categories.all_status') }}</option>
                                    <option value="Active">{{ __('categories.active') }}</option>
                                    <option value="Inactive">{{ __('categories.inactive') }}</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 d-flex justify-content-end mt-2">
                                <button type="button" id="searchBtn" class="btn btn-primary me-2">
                                    <i class="bx bx-search"></i>
                                    {{ __('categories.apply_filters') }}
                                </button>
                                <button type="button" id="resetBtn" class="btn btn-danger">
                                    <i class="bx bx-reset"></i>
                                    {{ __('categories.reset') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card-body table-responsive">
                    {{ $dataTable->table(['class' => 'table category-table table-bordered']) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- VIEW MODAL -->
<div class="modal fade" id="viewCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('categories.module_name') }} Details</h5>
                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="150">{{ __('categories.name') }}</th>
                        <td id="category-name"></td>
                    </tr>
                    <tr>
                        <th>{{ __('categories.status') }}</th>
                        <td id="category-status"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-css')
@endsection

@section('scripts')
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
    <script src="{{ asset('assets/sash/js/table-data.js') }}"></script>
    {!! $dataTable->scripts() !!}

    <script>
    $(document).ready(function () {
        const table = '.category-table';

        initSelect2();

        initDataTableFilters(table, ['status']);

        bindDeleteAction(table, "{{ route($routePrefix . '.destroy', '') }}");

        bindStatusToggle(table, "{{ route($routePrefix . '.change-status', '') }}");
    });
    </script>

    <script>
    $(document).on('click', '.view-btn', function () {
        let url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    $('#category-name').text(response.data.name);
                    $('#category-status').html(
                        response.data.status === 'Active'
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>'
                    );
                    $('#viewCategoryModal').modal('show');
                }
            }
        });
    });
    </script>
@endsection