@extends('admin.layouts.master')

@section('title', 'Create Pathway')

@section('content')
<div class="main-container container-fluid">
    <div class="page-header">
        <h1 class="page-title">Create Pathway</h1>
        <div class="breadcrumb-style3">
            <ol class="breadcrumb1">
                <li class="breadcrumb-item1 active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item1 active"><a href="{{ route($routePrefix . '.index') }}">Pathways</a></li>
                <li class="breadcrumb-item1" aria-current="page">Create</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom"><h5 class="card-title mb-0">Pathway Information</h5></div>
        <div class="card-body">
            <form id="pathwayForm" method="POST" action="{{ route($routePrefix . '.store') }}" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @include('admin.pathways._form')
                <div class="card-footer text-end px-0 mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-success me-2"><i class="fa fa-check me-1"></i> Save Pathway</button>
                    <a href="{{ route($routePrefix . '.index') }}" class="btn btn-danger"><i class="fa fa-times me-1"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('.select2').select2({ width: '100%' });
    $('#pathwayForm').on('submit', function () { $('#global-loader').show(); });
});
</script>
@endsection
