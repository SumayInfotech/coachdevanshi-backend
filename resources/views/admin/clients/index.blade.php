@extends('admin.layouts.master')

@section('title', 'Clients')

@section('content')
<div class="main-container container-fluid">

    <div class="page-header">
        <h1 class="page-title">Clients</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item1 active" aria-current="page">Clients</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                    <h5 class="card-title mb-0">App Clients</h5>
                    <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fa fa-sliders me-1"></i> Filters
                    </button>
                </div>

                <div class="collapse" id="filterCollapse">
                    <div class="card-body border-bottom bg-light">
                        <form id="filters" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label font-weight-semibold">Pathway</label>
                                <select name="pathway_uuid" id="pathway_uuid" class="form-control select2">
                                    <option value="">All Pathways</option>
                                    @foreach($pathways as $uuid => $title)
                                        <option value="{{ $uuid }}">{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-weight-semibold">Onboarding</label>
                                <select name="onboarding" id="onboarding" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-weight-semibold">Needs Attention</label>
                                <select name="attention" id="attention" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="renewal">Renewal requested</option>
                                    <option value="unread">Unread messages</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label font-weight-semibold">Status</label>
                                <select name="status" id="status" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="button" id="searchBtn" class="btn btn-primary flex-fill"><i class="fa fa-filter me-1"></i> Apply</button>
                                <button type="button" id="resetBtn" class="btn btn-danger flex-fill"><i class="fa fa-refresh me-1"></i> Reset</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body table-responsive">
                    {{ $dataTable->table(['class' => 'table clients-table table-bordered align-middle']) }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/sash/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/js/dataTables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/sash/plugins/datatable/responsive.bootstrap5.min.js') }}"></script>

    {!! $dataTable->scripts() !!}

    <script>
    $(document).ready(function () {
        const table = '.clients-table';
        initSelect2();
        initDataTableFilters(table, ['pathway_uuid', 'onboarding', 'attention', 'status']);
        bindStatusToggle(table, "{{ route('admin.user.change-status', '') }}");
    });
    </script>
@endsection
