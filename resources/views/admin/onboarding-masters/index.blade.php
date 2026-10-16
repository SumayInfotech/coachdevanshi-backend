@extends('admin.layouts.master')

@section('title', 'Onboarding Masters')

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">Onboarding Masters</h1>

        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item1 active" aria-current="page">
                    Onboarding Masters
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
                    <h5 class="card-title mb-0">Onboarding Master List</h5>

                    <div class="d-flex align-items-center gap-2 export-btn-container">
                        <button class="btn btn-info"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse">
                            <i class="fa fa-sliders me-1"></i>
                            Filters
                        </button>

                        @if($addPermission)
                            <a class="btn btn-success"
                               href="{{ route($routePrefix . '.create') }}">
                                <i class="fa fa-plus me-1"></i>
                                <span class="d-sm-inline-block">Add Master Option</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- FILTER PANEL -->
                <div class="collapse" id="filterCollapse">
                    <div class="card-body border-bottom bg-light">
                        <form id="filters" class="row g-3">
                            <!-- Category Filter -->
                            <div class="col-md-5">
                                <label class="form-label font-weight-semibold">Category / Question</label>
                                <select name="category" id="category" class="form-control select2">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <label class="form-label font-weight-semibold">Status</label>
                                <select name="status" id="status" class="form-control select2">
                                    <option value="">All Statuses</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="button" id="searchBtn" class="btn btn-primary flex-fill">
                                    <i class="fa fa-filter me-1"></i> Apply Filters
                                </button>
                                <button type="button" id="resetBtn" class="btn btn-danger flex-fill">
                                    <i class="fa fa-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card-body table-responsive">
                    {{ $dataTable->table(['class' => 'table onboarding-master-table table-bordered align-middle']) }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- VIEW MASTER MODAL -->
<div class="modal fade" id="viewMasterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Onboarding Master Details</h5>
                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row align-items-start">
                    <div class="col-md-4 text-center mb-3">
                        <img id="master-image"
                             src=""
                             alt="Master Icon/Image"
                             class="img-fluid rounded border p-1"
                             style="max-height: 200px; max-width: 100%; display: none;">
                        <div id="master-no-image" class="p-4 bg-light rounded text-muted border" style="display:none;">
                            <i class="fa fa-image fa-3x mb-2 text-muted"></i>
                            <p class="mb-0 small">No image uploaded</p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th width="160" class="bg-light">Category</th>
                                <td id="master-category" class="font-weight-semibold"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Title / Label</th>
                                <td id="master-title" class="font-weight-bold text-primary"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Description</th>
                                <td id="master-description"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Sort Order</th>
                                <td id="master-sort-order"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Status</th>
                                <td id="master-status"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">Created At</th>
                                <td id="master-created-at"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- DATA TABLE JS-->
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
        const table = '.onboarding-master-table';

        // Select2 Init
        initSelect2();

        // DataTable Filters Init
        initDataTableFilters(table, ['status', 'category']);

        // Bind Delete Action
        bindDeleteAction(table, "{{ route($routePrefix . '.destroy', '') }}");

        // Bind Status Toggle Action
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
                        $('#master-category').text(response.data.category);
                        $('#master-title').text(response.data.title);
                        $('#master-description').html(response.data.description || '-');
                        $('#master-sort-order').text(response.data.sort_order ?? 0);
                        $('#master-created-at').text(response.data.created_at || '-');
                        $('#master-status').html(
                            response.data.status === 'Active'
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-danger">Inactive</span>'
                        );

                        if (response.data.image) {
                            $('#master-image').attr('src', response.data.image).show();
                            $('#master-no-image').hide();
                        } else {
                            $('#master-image').hide();
                            $('#master-no-image').show();
                        }

                        $('#viewMasterModal').modal('show');
                    }
                },
                error: function () {
                    toastr.error('Failed to fetch details.');
                }
            });
        });
    </script>
@endsection

