@extends('admin.layouts.master')

@section('title', __('preferences.module_plural'))

@section('content')
<div class="main-container container-fluid">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">{{ __('preferences.module_plural') }}</h1>
        
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active">
                    <a href="{{ route('admin.dashboard') }}">
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                <li class="breadcrumb-item1 active" aria-current="page">
                    {{ __('preferences.module_plural') }}
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
                        {{ __('preferences.module_plural') }}
                    </h5>

                    <div class="d-flex align-items-center gap-2 export-btn-container">
                        <button class="btn btn-info"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse">
                            <i class="fa fa-sliders me-1"></i>
                            {{ __('preferences.filters') }}
                        </button>

                        @if($addPermission)
                            <a class="btn btn-success"
                               href="{{ route($routePrefix.'.create') }}">
                                <i class="fa fa-plus"></i>
                                <span class="d-sm-inline-block">
                                    {{ __('preferences.create_title') }}
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
                                    {{ __('preferences.status') }}
                                </label>
                                <select name="status"
                                        id="status"
                                        class="form-control">
                                    <option value="">
                                        {{ __('preferences.all_status') }}
                                    </option>
                                    <option value="Active">{{ __('preferences.active') }}</option>
                                    <option value="Inactive">{{ __('preferences.inactive') }}</option>
                                </select>
                            </div>
                            <!-- User Type -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    {{ __('preferences.type') }}
                                </label>
                                <select name="type"
                                        id="type"
                                        class="form-control">
                                    <option value="">
                                        {{ __('preferences.all_types') }}
                                    </option>
                                    <option value="dietary">{{ __('preferences.dietary') }}</option>
                                    <option value="goals">{{ __('preferences.goals') }}</option>
                                    <option value="food_allergies">{{ __('preferences.food_allergies') }}</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-12 d-flex justify-content-end mt-2">
                                <button type="button"
                                        id="searchBtn"
                                        class="btn btn-primary me-2">
                                    <i class="bx bx-search"></i>
                                    {{ __('preferences.apply_filters') }}
                                </button>
                                <button type="button"
                                        id="resetBtn"
                                        class="btn btn-danger">
                                    <i class="bx bx-reset"></i>
                                    {{ __('preferences.reset') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="card-body table-responsive">
                     {{ $dataTable->table(['class' => 'table preference-table table-bordered']) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewPreferenceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preference Details</h5>
                    <button aria-label="Close" class="btn-close" data-bs-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img id="preference-image"
                             src=""
                             class="img-fluid rounded"
                             style="max-height:200px; display:none;">
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="150">Title</th>
                                <td id="preference-title"></td>
                            </tr>

                            <tr>
                                <th>Description</th>
                                <td id="preference-description"></td>
                            </tr>

                            <tr>
                                <th>Type</th>
                                <td id="preference-type"></td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td id="preference-status"></td>
                            </tr>
                        </table>
                    </div>
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
        const table = '.preference-table';

        //Select2 Init
        initSelect2();
        
        //DataTable Filters Init
        initDataTableFilters(table, ['status','type']);

        //Bind Delete Action
        bindDeleteAction(table,"{{ route($routePrefix . '.destroy', '') }}");

        //Bind Status Toggle Action
        bindStatusToggle(table,"{{ route($routePrefix . '.change-status', '') }}");
    });
    </script>
    <script>
        $(document).on('click', '.view-btn', function () {
            let url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    if(response.success) {
                        $('#preference-title').text(response.data.title);
                        $('#preference-description').html(response.data.description ?? '-');
                        $('#preference-type').text(response.data.type);
                        $('#preference-status').html(
                            response.data.status == 'Active'
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-danger">Inactive</span>'
                        );
                        if(response.data.image) {
                            $('#preference-image')
                                .attr('src', response.data.image)
                                .show();
                        } else {
                            $('#preference-image').hide();
                        }
                        $('#viewPreferenceModal').modal('show');
                    }
                }
            });
        });
</script>
@endsection
