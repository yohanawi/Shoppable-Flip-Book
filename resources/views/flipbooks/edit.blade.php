@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Edit
                        Flipbook</h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('flipbooks.index') }}" class="text-muted text-hover-primary">Flipbooks</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">{{ $flipbook->title }}</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('flipbooks.editor', $flipbook) }}" class="btn btn-sm btn-light-primary">
                        <i class="ki-duotone ki-abstract-26 fs-2"></i>Hotspot Editor
                    </a>
                    @if ($flipbook->is_published)
                        <a href="{{ $flipbook->getPublicUrl() }}" target="_blank" class="btn btn-sm btn-light-success">
                            <i class="ki-duotone ki-eye fs-2"></i>View Live
                        </a>
                    @endif
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

                @if ($flipbook->pages->isEmpty())
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-information-5 fs-2x me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="flex-grow-1">
                                <h4 class="mb-1">No Pages Generated</h4>
                                <div class="fw-semibold">
                                    @if (!extension_loaded('imagick'))
                                        The Imagick extension is not installed, so PDF pages couldn't be converted to
                                        images.
                                        <br>
                                        <strong>Don't worry!</strong> Your PDF will be rendered directly in the browser
                                        using PDF.js.
                                        <br>
                                        <a href="{{ route('flipbook.viewer', $flipbook->slug) }}" target="_blank"
                                            class="btn btn-sm btn-light-primary mt-2">
                                            <i class="ki-duotone ki-eye fs-2"></i>Test Viewer
                                        </a>
                                    @else
                                        PDF conversion failed. Please try re-uploading your PDF or contact support.
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!--begin::Card-->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Flipbook Details</h3>
                    </div>

                    <form action="{{ route('flipbooks.update', $flipbook) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <!--begin::Form group-->
                                    <div class="mb-10">
                                        <label class="form-label required">Flipbook Title</label>
                                        <input type="text" name="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            value="{{ old('title', $flipbook->title) }}" required />
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!--end::Form group-->

                                    <!--begin::Form group-->
                                    <div class="mb-10">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $flipbook->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!--end::Form group-->

                                    <!--begin::Form group-->
                                    <div class="mb-10">
                                        <label class="form-label">Template</label>
                                        <select name="template_id"
                                            class="form-select @error('template_id') is-invalid @enderror">
                                            <option value="">Default Template</option>
                                            @foreach ($templates as $template)
                                                <option value="{{ $template->id }}"
                                                    {{ old('template_id', $flipbook->template_id) == $template->id ? 'selected' : '' }}>
                                                    {{ $template->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('template_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!--end::Form group-->

                                    <!--begin::Form group-->
                                    <div class="mb-10">
                                        <div class="form-check form-switch form-check-custom form-check-solid mb-3">
                                            <input class="form-check-input" type="checkbox" name="is_published"
                                                id="is_published" value="1"
                                                {{ old('is_published', $flipbook->is_published) ? 'checked' : '' }} />
                                            <label class="form-check-label" for="is_published">
                                                Published
                                            </label>
                                        </div>
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" name="is_public" id="is_public"
                                                value="1"
                                                {{ old('is_public', $flipbook->is_public) ? 'checked' : '' }} />
                                            <label class="form-check-label" for="is_public">
                                                Public
                                            </label>
                                        </div>
                                    </div>
                                    <!--end::Form group-->
                                </div>

                                <div class="col-md-4">
                                    <!--begin::Info-->
                                    <div class="card card-flush bg-light">
                                        <div class="card-header">
                                            <h3 class="card-title">Flipbook Info</h3>
                                        </div>
                                        <div class="card-body pt-0">
                                            <div class="d-flex flex-center mb-5">
                                                <img src="{{ $flipbook->getThumbnailUrl() }}" class="img-fluid rounded"
                                                    alt="{{ $flipbook->title }}" />
                                            </div>
                                            <div class="separator separator-dashed my-5"></div>
                                            <div class="mb-5">
                                                <div class="fw-bold text-gray-600 mb-2">Total Pages</div>
                                                <div class="fs-3 fw-bold text-gray-800">{{ $flipbook->total_pages }}</div>
                                            </div>
                                            <div class="mb-5">
                                                <div class="fw-bold text-gray-600 mb-2">Total Views</div>
                                                <div class="fs-3 fw-bold text-gray-800">
                                                    {{ number_format($flipbook->views_count) }}</div>
                                            </div>
                                            <div class="mb-5">
                                                <div class="fw-bold text-gray-600 mb-2">Created</div>
                                                <div class="text-gray-800">{{ $flipbook->created_at->format('M d, Y') }}
                                                </div>
                                            </div>
                                            @if ($flipbook->published_at)
                                                <div class="mb-5">
                                                    <div class="fw-bold text-gray-600 mb-2">Published</div>
                                                    <div class="text-gray-800">
                                                        {{ $flipbook->published_at->format('M d, Y') }}</div>
                                                </div>
                                            @endif
                                            <div class="separator separator-dashed my-5"></div>
                                            <a href="{{ $flipbook->getPdfUrl() }}" target="_blank"
                                                class="btn btn-light-primary w-100">
                                                <i class="ki-duotone ki-file-down fs-2">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>
                                    <!--end::Info-->
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-between py-6">
                            <a href="{{ route('flipbooks.index') }}" class="btn btn-light">Back to List</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-duotone ki-check fs-2"></i>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
                <!--end::Card-->

                <!--begin::Pages Card-->
                <div class="card mt-5">
                    <div class="card-header">
                        <h3 class="card-title">Pages ({{ $flipbook->pages->count() }})</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-5">
                            @forelse($flipbook->pages as $page)
                                <div class="col-md-2 col-sm-3 col-6">
                                    <div class="card card-flush h-100 position-relative">
                                        <div class="card-body p-3">
                                            <img src="{{ $page->getThumbnailUrl() }}" class="img-fluid rounded mb-2"
                                                alt="Page {{ $page->page_number }}" />
                                            <div class="text-center">
                                                <span class="badge badge-light-primary">Page
                                                    {{ $page->page_number }}</span>
                                                @if ($page->hotspots->count() > 0)
                                                    <span class="badge badge-light-success">{{ $page->hotspots->count() }}
                                                        hotspots</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-10">
                                    <p class="text-muted">Pages are being processed...</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <!--end::Pages Card-->
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
