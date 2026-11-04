@extends('admin.layouts.master')

@section('title', 'Pathways')

@section('content')
<div class="main-container container-fluid">

    <div class="page-header">
        <h1 class="page-title">Pathways</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item1 active" aria-current="page">Pathways</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                    <h5 class="card-title mb-0">Coaching Pathways</h5>
                    <div class="d-flex align-items-center gap-2 export-btn-container">
                        <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="fa fa-sliders me-1"></i> Filters
                        </button>
                        @if($addPermission)
                            <a class="btn btn-success" href="{{ route($routePrefix . '.create') }}">
                                <i class="fa fa-plus me-1"></i> <span class="d-sm-inline-block">Add Pathway</span>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="collapse" id="filterCollapse">
                    <div class="card-body border-bottom bg-light">
                        <form id="filters" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label font-weight-semibold">Group</label>
                                <select name="group" id="group" class="form-control select2">
                                    <option value="">All Groups</option>
                                    @foreach($groups as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label font-weight-semibold">Status</label>
                                <select name="status" id="status" class="form-control select2">
                                    <option value="">All Statuses</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="button" id="searchBtn" class="btn btn-primary flex-fill"><i class="fa fa-filter me-1"></i> Apply Filters</button>
                                <button type="button" id="resetBtn" class="btn btn-danger flex-fill"><i class="fa fa-refresh me-1"></i> Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body table-responsive">
                    {{ $dataTable->table(['class' => 'table pathways-table table-bordered align-middle']) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewPathwayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Pathway Details</h5>
                <button aria-label="Close" class="btn-close" data-bs-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="row align-items-start">
                    <div class="col-md-4 mb-3">
                        <div class="dd-media">
                            <img id="pathway-image" src="" alt="Pathway image" style="display:none;">
                            <div id="pathway-no-image" class="dd-media-empty" style="display:none;">
                                <i class="fe fe-image"></i>
                                <span>No image uploaded</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table dd-detail-table mb-0">
                            <tr><th width="150">Title</th><td id="pathway-title" class="font-weight-bold text-primary"></td></tr>
                            <tr><th>Group</th><td id="pathway-group"></td></tr>
                            <tr><th>Description</th><td id="pathway-description"></td></tr>
                            <tr><th>Tags</th><td id="pathway-tags"></td></tr>
                            <tr><th>Protocol Form</th><td id="pathway-protocol"></td></tr>
                            <tr><th>Clients</th><td id="pathway-clients"></td></tr>
                            <tr><th>Status</th><td id="pathway-status"></td></tr>
                            <tr><th>Created At</th><td id="pathway-created-at"></td></tr>
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
    <script src="{{ asset('assets/sash/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

    {!! $dataTable->scripts() !!}

    <script>
    $(document).ready(function () {
        const table = '.pathways-table';
        initSelect2();
        initDataTableFilters(table, ['status', 'group']);
        bindDeleteAction(table, "{{ route($routePrefix . '.destroy', '') }}");
        bindStatusToggle(table, "{{ route($routePrefix . '.change-status', '') }}");
    });

    $(document).on('click', '.view-btn', function () {
        $.get($(this).data('url'), function (response) {
            if (!response.success) return;
            const d = response.data;
            $('#pathway-title').text(d.title);
            $('#pathway-group').text(d.group);
            $('#pathway-description').text(d.description || '-');
            $('#pathway-tags').text(d.tags || '-');
            $('#pathway-protocol').text(d.protocol);
            $('#pathway-clients').text(d.clients);
            $('#pathway-created-at').text(d.created_at || '-');
            $('#pathway-status').html(d.status === 'Active'
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>');
            if (d.image) {
                $('#pathway-image').attr('src', d.image).show();
                $('#pathway-no-image').hide();
            } else {
                $('#pathway-image').hide();
                $('#pathway-no-image').show();
            }
            $('#viewPathwayModal').modal('show');
        }).fail(function () {
            showToast('error', 'Failed to fetch details.');
        });
    });
    </script>
@endsection
