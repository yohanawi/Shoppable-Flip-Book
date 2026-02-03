@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Create New FlipBook
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.catalog.index') }}"
                                class="text-muted text-hover-primary">Catalog</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Create</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('customer.catalog.store') }}" method="POST" enctype="multipart/form-data"
                            id="createFlipbookForm">
                            @csrf

                            <div class="mb-10">
                                <h2 class="fw-bold text-dark">Step 1: Upload PDF & Basic Details</h2>
                                <div class="text-gray-400 fw-semibold fs-6">Provide basic information about your flipbook
                                </div>
                            </div>

                            <div class="mb-10">
                                <label class="form-label required">PDF File</label>
                                <input type="file" name="pdf" class="form-control @error('pdf') is-invalid @enderror"
                                    accept=".pdf" required>
                                @error('pdf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maximum file size: 50MB</div>
                            </div>

                            <div class="mb-10">
                                <label class="form-label required">Title</label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                    required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-10">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-10">
                                <label class="form-label required">Template Type</label>
                                <div class="row g-5">
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="template_type" value="page_management"
                                            id="template_page"
                                            {{ old('template_type') === 'page_management' ? 'checked' : '' }}>
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center h-100"
                                            for="template_page">
                                            <i class="ki-duotone ki-document fs-3x me-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="d-block fw-semibold text-start">
                                                <span class="text-dark fw-bold d-block fs-3">Page Management</span>
                                                <span class="text-muted fw-semibold fs-6">Reorder, rename, and organize
                                                    pages</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="template_type"
                                            value="page_flip_physics" id="template_physics"
                                            {{ old('template_type') === 'page_flip_physics' ? 'checked' : '' }}>
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center h-100"
                                            for="template_physics">
                                            <i class="ki-duotone ki-abstract-26 fs-3x me-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="d-block fw-semibold text-start">
                                                <span class="text-dark fw-bold d-block fs-3">Flip Physics</span>
                                                <span class="text-muted fw-semibold fs-6">Configure animation and flip
                                                    behavior</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="template_type" value="slicer"
                                            id="template_slicer"
                                            {{ old('template_type', 'slicer') === 'slicer' ? 'checked' : '' }} required>
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center h-100"
                                            for="template_slicer">
                                            <i class="ki-duotone ki-shop fs-3x me-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                            <span class="d-block fw-semibold text-start">
                                                <span class="text-dark fw-bold d-block fs-3">Slicer (Shoppable)</span>
                                                <span class="text-muted fw-semibold fs-6">Add interactive hotspots and
                                                    products</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                @error('template_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-10">
                                <label class="form-label required">Visibility</label>
                                <select name="visibility" class="form-select @error('visibility') is-invalid @enderror"
                                    required>
                                    <option value="private"
                                        {{ old('visibility', 'private') === 'private' ? 'selected' : '' }}>Private - Only I
                                        can see</option>
                                    <option value="unlisted" {{ old('visibility') === 'unlisted' ? 'selected' : '' }}>
                                        Unlisted - Anyone with link can see</option>
                                    <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Public -
                                        Everyone can see</option>
                                </select>
                                @error('visibility')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ki-duotone ki-check fs-3"></i>Create & Continue to Template
                                </button>
                                <a href="{{ route('customer.catalog.index') }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
