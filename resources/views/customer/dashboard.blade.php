@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Customer Dashboard
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Dashboard</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <!-- Stats Cards -->
                <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                    <div class="col-md-4">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10"
                            style="background-color: #F1416C;">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $totalFlipbooks }}</span>
                                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total FlipBooks</span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <a href="{{ route('customer.catalog.index') }}"
                                        class="btn btn-sm btn-color-white btn-active-color-primary fw-bold">View Catalog</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10"
                            style="background-color: #50CD89;">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $publishedFlipbooks }}</span>
                                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Published FlipBooks</span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <span class="text-white opacity-75 fw-semibold fs-7">Live & Available</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-md-50 mb-5 mb-xl-10"
                            style="background-color: #7239EA;">
                            <div class="card-header pt-5">
                                <div class="card-title d-flex flex-column">
                                    <span class="fs-2hx fw-bold text-white me-2 lh-1 ls-n2">{{ $draftFlipbooks }}</span>
                                    <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Draft FlipBooks</span>
                                </div>
                            </div>
                            <div class="card-body d-flex align-items-end pt-0">
                                <div class="d-flex align-items-center flex-column mt-3 w-100">
                                    <a href="{{ route('customer.catalog.create') }}"
                                        class="btn btn-sm btn-color-white btn-active-color-primary fw-bold">Create New</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent FlipBooks -->
                <div class="card">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Recent FlipBooks</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Your latest projects</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="{{ route('customer.catalog.index') }}" class="btn btn-sm btn-light-primary">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="card-body py-3">
                        @if ($recentFlipbooks->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted">
                                            <th class="min-w-200px">Title</th>
                                            <th class="min-w-100px">Template</th>
                                            <th class="min-w-100px">Status</th>
                                            <th class="min-w-100px">Visibility</th>
                                            <th class="min-w-100px">Created</th>
                                            <th class="min-w-100px text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentFlipbooks as $flipbook)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <a href="{{ route('customer.catalog.show', $flipbook) }}"
                                                                class="text-dark fw-bold text-hover-primary fs-6">
                                                                {{ $flipbook->title }}
                                                            </a>
                                                            <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                                {{ Str::limit($flipbook->description, 50) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-light-info">{{ str_replace('_', ' ', ucwords($flipbook->template_type ?? 'slicer')) }}</span>
                                                </td>
                                                <td>
                                                    @if ($flipbook->status === 'live')
                                                        <span class="badge badge-light-success">Live</span>
                                                    @elseif($flipbook->status === 'draft')
                                                        <span class="badge badge-light-warning">Draft</span>
                                                    @else
                                                        <span class="badge badge-light-secondary">Archived</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-light-primary">{{ ucfirst($flipbook->visibility ?? 'private') }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="text-muted fw-semibold d-block fs-7">{{ $flipbook->created_at->diffForHumans() }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('customer.catalog.edit', $flipbook) }}"
                                                        class="btn btn-sm btn-icon btn-light btn-active-light-primary">
                                                        <i class="ki-duotone ki-pencil fs-5"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-10">
                                <i class="ki-duotone ki-file-deleted fs-5x text-muted mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <p class="text-muted fs-5">No FlipBooks yet. Create your first one!</p>
                                <a href="{{ route('customer.catalog.create') }}" class="btn btn-primary">Create
                                    FlipBook</a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
