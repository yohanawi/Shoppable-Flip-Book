@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Flipbooks</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-500 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Flipbooks</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('flipbooks.create') }}" class="btn btn-sm fw-bold btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>Upload New Flipbook
                    </a>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!--begin::Card-->
                <div class="card">
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" class="form-control form-control-solid w-250px ps-13"
                                    placeholder="Search flipbooks..." id="search_flipbooks">
                            </div>
                        </div>
                    </div>
                    <!--end::Card header-->

                    <!--begin::Card body-->
                    <div class="card-body py-4">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="flipbooks_table">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2">ID</th>
                                        <th class="min-w-125px">Title</th>
                                        <th class="min-w-125px">Template</th>
                                        <th class="min-w-100px">Pages</th>
                                        <th class="min-w-100px">Views</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-100px">Created</th>
                                        <th class="text-end min-w-100px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @forelse($flipbooks as $flipbook)
                                        <tr>
                                            <td>{{ $flipbook->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-50px me-3">
                                                        <img src="{{ $flipbook->getThumbnailUrl() }}"
                                                            alt="{{ $flipbook->title }}" />
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <a href="{{ route('flipbooks.edit', $flipbook) }}"
                                                            class="text-gray-800 text-hover-primary mb-1">
                                                            {{ $flipbook->title }}
                                                        </a>
                                                        <span
                                                            class="text-muted fs-7">{{ Str::limit($flipbook->description, 50) }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($flipbook->template)
                                                    <span
                                                        class="badge badge-light-primary">{{ $flipbook->template->name }}</span>
                                                @else
                                                    <span class="text-muted">Default</span>
                                                @endif
                                            </td>
                                            <td>{{ $flipbook->total_pages }}</td>
                                            <td>{{ number_format($flipbook->views_count) }}</td>
                                            <td>
                                                @if ($flipbook->is_published)
                                                    <span class="badge badge-light-success">Published</span>
                                                @else
                                                    <span class="badge badge-light-warning">Draft</span>
                                                @endif
                                                @if (!$flipbook->is_public)
                                                    <span class="badge badge-light-danger">Private</span>
                                                @endif
                                            </td>
                                            <td>{{ $flipbook->created_at->format('M d, Y') }}</td>
                                            <td class="text-end">
                                                <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    Actions
                                                    <i class="ki-duotone ki-down fs-5 ms-1"></i>
                                                </a>
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
                                                    data-kt-menu="true">
                                                    @if ($flipbook->is_published)
                                                        <div class="menu-item px-3">
                                                            <a href="{{ $flipbook->getPublicUrl() }}" target="_blank"
                                                                class="menu-link px-3">
                                                                <i class="ki-duotone ki-eye fs-5 me-2"></i>View
                                                            </a>
                                                        </div>
                                                    @endif
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('flipbooks.edit', $flipbook) }}"
                                                            class="menu-link px-3">
                                                            <i class="ki-duotone ki-pencil fs-5 me-2"></i>Edit
                                                        </a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('flipbooks.editor', $flipbook) }}"
                                                            class="menu-link px-3">
                                                            <i class="ki-duotone ki-abstract-26 fs-5 me-2"></i>Hotspot
                                                            Editor
                                                        </a>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <form action="{{ route('flipbooks.toggle-publish', $flipbook) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="menu-link px-3 w-100 text-start border-0 bg-transparent">
                                                                <i
                                                                    class="ki-duotone ki-toggle-{{ $flipbook->is_published ? 'off' : 'on' }} fs-5 me-2"></i>
                                                                {{ $flipbook->is_published ? 'Unpublish' : 'Publish' }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <div class="menu-item px-3">
                                                        <form action="{{ route('flipbooks.destroy', $flipbook) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this flipbook?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="menu-link px-3 w-100 text-start border-0 bg-transparent text-danger">
                                                                <i class="ki-duotone ki-trash fs-5 me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-10">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="ki-duotone ki-file-deleted fs-5x text-muted mb-5">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    <h3 class="text-muted">No flipbooks found</h3>
                                                    <p class="text-muted">Start by uploading your first PDF flipbook</p>
                                                    <a href="{{ route('flipbooks.create') }}"
                                                        class="btn btn-primary">Upload Flipbook</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!--begin::Pagination-->
                        <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Showing {{ $flipbooks->firstItem() ?? 0 }} to {{ $flipbooks->lastItem() ?? 0 }} of
                                {{ $flipbooks->total() }} entries
                            </div>
                            {{ $flipbooks->links() }}
                        </div>
                        <!--end::Pagination-->
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Card-->
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
