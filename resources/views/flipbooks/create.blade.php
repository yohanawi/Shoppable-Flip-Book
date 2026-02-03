@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">Upload
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
                        <li class="breadcrumb-item text-muted">Upload</li>
                    </ul>
                </div>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!--begin::Card-->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Upload PDF Flipbook</h3>
                    </div>

                    <form action="{{ route('flipbooks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="card-body">
                            <!--begin::Form group-->
                            <div class="mb-10">
                                <label class="form-label required">Flipbook Title</label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Enter flipbook title" value="{{ old('title') }}" required />
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Form group-->

                            <!--begin::Form group-->
                            <div class="mb-10">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                                    placeholder="Enter flipbook description (optional)">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Form group-->

                            <!--begin::Form group-->
                            <div class="mb-10">
                                <label class="form-label required">PDF File</label>
                                <input type="file" name="pdf_file"
                                    class="form-control @error('pdf_file') is-invalid @enderror" accept="application/pdf"
                                    required />
                                <div class="form-text">Maximum file size: 50MB. Accepted format: PDF</div>
                                @error('pdf_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Form group-->

                            <!--begin::Form group-->
                            <div class="mb-10">
                                <label class="form-label">Template</label>
                                <select name="template_id" class="form-select @error('template_id') is-invalid @enderror">
                                    <option value="">Default Template</option>
                                    @foreach ($templates as $template)
                                        <option value="{{ $template->id }}"
                                            {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Choose a template style for your flipbook</div>
                                @error('template_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!--end::Form group-->

                            <!--begin::Form group-->
                            <div class="mb-10">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_public" id="is_public"
                                        value="1" {{ old('is_public', true) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="is_public">
                                        Make this flipbook public
                                    </label>
                                </div>
                                <div class="form-text">Public flipbooks can be viewed by anyone with the link</div>
                            </div>
                            <!--end::Form group-->

                            <!--begin::Alert-->
                            <div class="alert alert-info d-flex align-items-center p-5">
                                <i class="ki-duotone ki-information-5 fs-2hx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-dark">Processing Information</h4>
                                    <span>After uploading, your PDF will be converted to images. This may take a few minutes
                                        depending on the file size and number of pages.</span>
                                </div>
                            </div>
                            <!--end::Alert-->
                        </div>

                        <div class="card-footer d-flex justify-content-end py-6">
                            <a href="{{ route('flipbooks.index') }}" class="btn btn-light me-3">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-duotone ki-file-up fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Upload Flipbook
                            </button>
                        </div>
                    </form>
                </div>
                <!--end::Card-->
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection
