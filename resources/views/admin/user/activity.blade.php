@extends('admin.layouts.master')

@section('title', __('activity.page_title'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">
            {{ __('activity.page_heading') }} – {{ $user->name }}
        </h1>

        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.user') }}">
                        {{ __('users.module_plural') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active" aria-current="page">
                    {{ __('activity.page_title') }}
                </li>
            </ol>
        </div>
    </div>

    <!-- ROW -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- CARD HEADER -->
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                    <h5 class="card-title mb-0">
                        {{ __('activity.card_title') }}
                    </h5>

                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-info"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse">
                            <i class="fa fa-sliders me-1"></i>
                            {{ __('activity.filters') }}
                        </button>
                    </div>
                </div>

                <!-- FILTER PANEL -->
                <div class="collapse" id="filterCollapse">
                    <div class="card-body border-bottom">
                        <form id="filters" class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">{{ __('activity.module') }}</label>
                                <select name="log_name" id="log_name" class="form-control">
                                    <option value="">{{ __('activity.all') }}</option>
                                    <option value="User">{{ __('activity.modules.user') }}</option>
                                    <option value="Role">{{ __('activity.modules.role') }}</option>
                                    <option value="Settings">{{ __('activity.modules.settings') }}</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ __('activity.action') }}</label>
                                <select name="event" id="event" class="form-control">
                                    <option value="">{{ __('activity.all') }}</option>
                                    <option value="created">{{ __('activity.actions.created') }}</option>
                                    <option value="updated">{{ __('activity.actions.updated') }}</option>
                                    <option value="viewed">{{ __('activity.actions.viewed') }}</option>
                                    <option value="status_changed">{{ __('activity.actions.status_changed') }}</option>
                                    <option value="deleted">{{ __('activity.actions.deleted') }}</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">{{ __('activity.date') }}</label>
                                <input type="date" name="date" id="date" class="form-control">
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-2">
                                <button type="button" id="searchBtn" class="btn btn-primary me-2">
                                    <i class="bx bx-search"></i> {{ __('activity.search') }}
                                </button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">
                                    <i class="bx bx-reset"></i> {{ __('activity.reset') }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card-body table-responsive">
                    {!! $dataTable->table(['class' => 'table user-activity-table table-bordered'], true) !!}
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ACTIVITY CHANGES MODAL -->
<div class="modal fade" id="activityChangesModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">{{ __('activity.changes_title') }}</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <div class="col-md-6">
                        <h6 class="text-danger">{{ __('activity.old') }}</h6>
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('activity.field') }}</th>
                                    <th>{{ __('activity.value') }}</th>
                                </tr>
                            </thead>
                            <tbody id="oldChangesTable"></tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-success">{{ __('activity.new') }}</h6>
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('activity.field') }}</th>
                                    <th>{{ __('activity.value') }}</th>
                                </tr>
                            </thead>
                            <tbody id="newChangesTable"></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('page-css')
<style>
    #oldChangesTable td:last-child {
        color: #dc3545; /* red */
    }
    #newChangesTable td:last-child {
        color: #198754; /* green */
    }
</style>

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
        const table = '.user-activity-table';
        //DataTable Filters Init
        initDataTableFilters(table, ['log_name','event','date']);

       
    });
    </script>
   
    <script>
        $(document).on('click', '.view-changes', function () {

            const activityId = $(this).data('id');

            $('#oldChangesTable, #newChangesTable').html(`
                <tr>
                    <td colspan="2" class="text-center">Loading...</td>
                </tr>
            `);

            $.get(`{{ route('admin.user.activity.changes', ':id') }}`.replace(':id', activityId), function (res) {

                renderDiffTables(res.old || {}, res.new || {});
                $('#activityChangesModal').modal('show');
            });
        });

        function renderDiffTables(oldData, newData) {

            let oldHtml = '';
            let newHtml = '';

            const keys = new Set([
                ...Object.keys(oldData),
                ...Object.keys(newData)
            ]);

            let hasDiff = false;

            keys.forEach(function (key) {

                const oldVal = oldData[key] ?? null;
                const newVal = newData[key] ?? null;

                //DIFF LOGIC
                if (JSON.stringify(oldVal) === JSON.stringify(newVal)) {
                    return; // skip unchanged
                }

                hasDiff = true;

                oldHtml += `
                    <tr>
                        <td>${formatKey(key)}</td>
                        <td>${formatValue(oldVal)}</td>
                    </tr>
                `;

                newHtml += `
                    <tr>
                        <td>${formatKey(key)}</td>
                        <td>${formatValue(newVal)}</td>
                    </tr>
                `;
            });

            if (!hasDiff) {
                oldHtml = newHtml = `
                    <tr>
                        <td colspan="2" class="text-center">No changes</td>
                    </tr>
                `;
            }

            $('#oldChangesTable').html(oldHtml);
            $('#newChangesTable').html(newHtml);
        }

        // JS version of formatName()
        function formatKey(key) {
            if (!key) return '-';

            return key
                .replace(/_/g, ' ')
                .replace(/\b\w/g, c => c.toUpperCase());
        }

        function formatValue(value) {
            if (value === null || value === undefined || value === '') {
                return '-';
            }

            if (typeof value === 'object') {
                return `<pre class="mb-0">${JSON.stringify(value, null, 2)}</pre>`;
            }

            return value;
        }
    </script>

@endsection
