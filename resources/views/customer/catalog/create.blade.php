<x-default-layout>

    @section('title')
        Create New FlipBook
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('customer.catalog.create') }}
    @endsection

    <div id="kt_app_content_container">

        {{-- Progress Stepper Info --}}
        <div class="card mb-5 mb-xl-10">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex overflow-auto h-55px">
                    <ul
                        class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold flex-nowrap">
                        <li class="nav-item">
                            <a class="nav-link text-active-primary me-6 active" href="#">
                                <i class="ki-duotone ki-file-up fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Upload & Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted me-6" href="#">
                                <i class="ki-duotone ki-setting-2 fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Configure Template
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted me-6" href="#">
                                <i class="ki-duotone ki-abstract-26 fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Add Hotspots
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-muted" href="#">
                                <i class="ki-duotone ki-check-circle fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Publish
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row g-6 g-xl-9">
            {{-- Main Form --}}
            <div class="col-lg-8">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-7">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">FlipBook Information</span>
                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Upload your PDF and provide basic
                                details</span>
                        </h3>
                    </div>

                    <div class="card-body pt-5">
                        <form action="{{ route('customer.catalog.store') }}" method="POST"
                            enctype="multipart/form-data" id="createFlipbookForm">
                            @csrf

                            {{-- PDF Upload Section --}}
                            <div class="mb-10">
                                <label class="fs-6 fw-semibold form-label required mb-5">
                                    <span>PDF Document</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Upload your PDF file to convert into a flipbook"></i>
                                </label>

                                <div class="fv-row">
                                    <div class="dropzone" id="kt_dropzone_pdf">
                                        <div class="dz-message needsclick">
                                            <i class="ki-duotone ki-file-up fs-3x text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div class="ms-4">
                                                <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop PDF file here
                                                    or click to upload.</h3>
                                                <span class="fs-7 fw-semibold text-gray-400">Maximum file size:
                                                    50MB</span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="file" name="pdf"
                                        class="form-control mt-3 @error('pdf') is-invalid @enderror" accept=".pdf"
                                        required id="pdf_input">
                                    @error('pdf')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="separator separator-dashed my-10"></div>

                            {{-- Title --}}
                            <div class="mb-10 fv-row">
                                <label class="fs-6 fw-semibold form-label required">
                                    <span>FlipBook Title</span>
                                </label>
                                <input type="text" name="title"
                                    class="form-control form-control-solid @error('title') is-invalid @enderror"
                                    placeholder="Enter a catchy title for your flipbook" value="{{ old('title') }}"
                                    required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-10 fv-row">
                                <label class="fs-6 fw-semibold form-label">
                                    <span>Description</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Provide a brief description of your flipbook content"></i>
                                </label>
                                <textarea name="description" class="form-control form-control-solid @error('description') is-invalid @enderror"
                                    rows="4" placeholder="Describe what this flipbook is about...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional: Help viewers understand what your flipbook
                                    contains</div>
                            </div>

                            <div class="separator separator-dashed my-10"></div>

                            {{-- Template Type Selection --}}
                            <div class="mb-10 fv-row">
                                <label class="fs-6 fw-semibold form-label required mb-5">
                                    <span>Template Type</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Choose the template that best fits your needs"></i>
                                </label>

                                <div class="row g-5">
                                    {{-- Page Management --}}
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="template_type"
                                            value="page_management" id="template_page"
                                            {{ old('template_type') === 'page_management' ? 'checked' : '' }}>
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex flex-column align-items-center h-100"
                                            for="template_page">
                                            <i class="ki-duotone ki-document fs-3x text-primary mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="d-block fw-semibold text-center">
                                                <span class="text-dark fw-bold d-block fs-5 mb-2">Page
                                                    Management</span>
                                                <span class="text-gray-400 fw-semibold fs-7">Reorder, rename,
                                                    and organize pages efficiently</span>
                                            </span>
                                        </label>
                                    </div>

                                    {{-- Flip Physics --}}
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="template_type"
                                            value="page_flip_physics" id="template_physics"
                                            {{ old('template_type') === 'page_flip_physics' ? 'checked' : '' }}>
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex flex-column align-items-center h-100"
                                            for="template_physics">
                                            <i class="ki-duotone ki-abstract-26 fs-3x text-success mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <span class="d-block fw-semibold text-center">
                                                <span class="text-dark fw-bold d-block fs-5 mb-2">Flip
                                                    Physics</span>
                                                <span class="text-gray-400 fw-semibold fs-7">Configure
                                                    realistic page flip animations</span>
                                            </span>
                                        </label>
                                    </div>

                                    {{-- Slicer (Shoppable) --}}
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="template_type" value="slicer"
                                            id="template_slicer"
                                            {{ old('template_type', 'slicer') === 'slicer' ? 'checked' : '' }} required>
                                        <label
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex flex-column align-items-center h-100"
                                            for="template_slicer">
                                            <i class="ki-duotone ki-shop fs-3x text-warning mb-5">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                            <span class="d-block fw-semibold text-center">
                                                <span class="text-dark fw-bold d-block fs-5 mb-2">Slicer
                                                    (Shoppable)</span>
                                                <span class="text-gray-400 fw-semibold fs-7">Add interactive
                                                    hotspots and product links</span>
                                            </span>
                                            <span
                                                class="badge badge-light-warning position-absolute top-0 end-0 m-3">Popular</span>
                                        </label>
                                    </div>
                                </div>
                                @error('template_type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="separator separator-dashed my-10"></div>

                            {{-- Visibility --}}
                            <div class="mb-10 fv-row">
                                <label class="fs-6 fw-semibold form-label required mb-3">
                                    <span>Visibility Settings</span>
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                        title="Control who can access your flipbook"></i>
                                </label>
                                <select name="visibility"
                                    class="form-select form-select-solid @error('visibility') is-invalid @enderror"
                                    data-control="select2" data-hide-search="true" required>
                                    <option value="private"
                                        {{ old('visibility', 'private') === 'private' ? 'selected' : '' }}>
                                        🔒 Private - Only I can see
                                    </option>
                                    <option value="unlisted" {{ old('visibility') === 'unlisted' ? 'selected' : '' }}>
                                        🔗 Unlisted - Anyone with link can see
                                    </option>
                                    <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>
                                        🌐 Public - Everyone can see
                                    </option>
                                </select>
                                @error('visibility')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Action Buttons --}}
                            <div class="separator separator-dashed my-10"></div>

                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('customer.catalog.index') }}" class="btn btn-light">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="submit_btn">
                                    <i class="ki-duotone ki-check fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Create & Continue
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar - Help & Info --}}
            <div class="col-lg-4">
                {{-- Getting Started Card --}}
                <div class="card card-flush mb-6">
                    <div class="card-header pt-7">
                        <h3 class="card-title">
                            <span class="card-label fw-bold text-gray-800">Getting Started</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="mb-7">
                            <div class="d-flex align-items-start mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-file-up fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="text-gray-900 fw-bold fs-6 mb-1">1. Upload PDF</h4>
                                    <span class="text-gray-400 fw-semibold fs-7">Choose your PDF document (max
                                        50MB)</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-notepad-edit fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="text-gray-900 fw-bold fs-6 mb-1">2. Add Details</h4>
                                    <span class="text-gray-400 fw-semibold fs-7">Provide title and
                                        description</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-5">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-setting-2 fs-2 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="text-gray-900 fw-bold fs-6 mb-1">3. Choose Template</h4>
                                    <span class="text-gray-400 fw-semibold fs-7">Select the template type that
                                        fits your needs</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-eye fs-2 text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h4 class="text-gray-900 fw-bold fs-6 mb-1">4. Set Visibility</h4>
                                    <span class="text-gray-400 fw-semibold fs-7">Control who can access your
                                        flipbook</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tips Card --}}
                <div class="card card-flush mb-6">
                    <div class="card-header pt-7">
                        <h3 class="card-title">
                            <span class="card-label fw-bold text-gray-800">Pro Tips</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div
                            class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-5">
                            <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Best Quality</h4>
                                    <div class="fs-7 text-gray-700">Use high-resolution PDFs for better viewing
                                        experience</div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="notice d-flex bg-light-success rounded border-success border border-dashed p-6 mb-5">
                            <i class="ki-duotone ki-shield-tick fs-2tx text-success me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">File Size</h4>
                                    <div class="fs-7 text-gray-700">Optimize your PDF to keep it under 50MB for
                                        faster loading</div>
                                </div>
                            </div>
                        </div>

                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                            <i class="ki-duotone ki-abstract-26 fs-2tx text-warning me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Hotspots</h4>
                                    <div class="fs-7 text-gray-700">Add interactive hotspots in the next step
                                        to make it shoppable</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Template Comparison --}}
                <div class="card card-flush">
                    <div class="card-header pt-7">
                        <h3 class="card-title">
                            <span class="card-label fw-bold text-gray-800">Template Features</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-7 gy-3">
                                <tbody>
                                    <tr>
                                        <td class="text-gray-800 fw-bold">Page Organization</td>
                                        <td class="text-end">
                                            <i class="ki-duotone ki-check-circle fs-2 text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-800 fw-bold">Flip Animations</td>
                                        <td class="text-end">
                                            <i class="ki-duotone ki-check-circle fs-2 text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-800 fw-bold">Interactive Hotspots</td>
                                        <td class="text-end">
                                            <span class="badge badge-light-warning">Slicer Only</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-gray-800 fw-bold">Product Links</td>
                                        <td class="text-end">
                                            <span class="badge badge-light-warning">Slicer Only</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Form submission handling
            document.getElementById('createFlipbookForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submit_btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="indicator-label">Processing...</span>';
            });

            // File input preview
            document.getElementById('pdf_input').addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    const fileName = e.target.files[0].name;
                    const fileSize = (e.target.files[0].size / (1024 * 1024)).toFixed(2);

                    // Update dropzone message
                    const dzMessage = document.querySelector('.dz-message');
                    if (dzMessage) {
                        dzMessage.innerHTML = `
                        <i class="ki-duotone ki-check-circle fs-3x text-success">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="ms-4">
                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">${fileName}</h3>
                            <span class="fs-7 fw-semibold text-gray-400">File size: ${fileSize} MB</span>
                        </div>
                    `;
                    }
                }
            });
        </script>
    @endpush

</x-default-layout>
