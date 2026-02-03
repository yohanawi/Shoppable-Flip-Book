<x-default-layout>

    @section('title')
        Edit FlipBook - {{ $flipbook->title }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('customer.catalog.edit', $flipbook) }}
    @endsection
    {{-- Quick Actions --}}
    <div class="d-flex align-items-center gap-2 gap-lg-3 justify-content-end m-5">
        @if ($flipbook->status === 'live')
            <a href="{{ route('flipbook.viewer', $flipbook->slug) }}" target="_blank" class="btn btn-sm btn-light-primary">
                <i class="ki-duotone ki-eye fs-3">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                Preview
            </a>
        @endif
        <a href="{{ route('customer.catalog.index') }}" class="btn btn-sm btn-light">
            <i class="ki-duotone ki-arrow-left fs-3">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            Back to Catalog
        </a>
    </div>

    {{-- Content --}}
    <div id="kt_app_content_container">

        <div class="row g-7">
            {{-- Main Form --}}
            <div class="col-lg-8">
                {{-- FlipBook Info Card --}}
                <div class="card shadow-sm mb-7">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">FlipBook Details</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Update your flipbook information</span>
                        </h3>
                        <div class="card-toolbar">
                            <span
                                class="badge badge-light-{{ $flipbook->status === 'live' ? 'success' : 'warning' }} fw-bold px-4 py-3">
                                {{ ucfirst($flipbook->status ?? 'draft') }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <form action="{{ route('customer.catalog.update', $flipbook) }}" method="POST"
                            id="editFlipbookForm">
                            @csrf
                            @method('PUT')

                            {{-- Title --}}
                            <div class="mb-8">
                                <label class="form-label required fs-6 fw-semibold text-gray-800">Title</label>
                                <input type="text" name="title"
                                    class="form-control form-control-solid @error('title') is-invalid @enderror"
                                    placeholder="Enter flipbook title" value="{{ old('title', $flipbook->title) }}"
                                    required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-8">
                                <label class="form-label fs-6 fw-semibold text-gray-800">Description</label>
                                <textarea name="description" class="form-control form-control-solid @error('description') is-invalid @enderror"
                                    rows="4" placeholder="Describe your flipbook...">{{ old('description', $flipbook->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">Optional: Provide a brief description</div>
                            </div>

                            {{-- Visibility --}}
                            <div class="mb-10">
                                <label class="form-label required fs-6 fw-semibold text-gray-800">Visibility</label>
                                <select name="visibility"
                                    class="form-select form-select-solid @error('visibility') is-invalid @enderror"
                                    data-control="select2" data-hide-search="true" required>
                                    <option value="private"
                                        {{ old('visibility', $flipbook->visibility ?? 'private') === 'private' ? 'selected' : '' }}>
                                        🔒 Private - Only I can see
                                    </option>
                                    <option value="unlisted"
                                        {{ old('visibility', $flipbook->visibility) === 'unlisted' ? 'selected' : '' }}>
                                        🔗 Unlisted - Anyone with link can see
                                    </option>
                                    <option value="public"
                                        {{ old('visibility', $flipbook->visibility) === 'public' ? 'selected' : '' }}>
                                        🌐 Public - Everyone can see
                                    </option>
                                </select>
                                @error('visibility')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Save Button --}}
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary" id="save_btn">
                                    <i class="ki-duotone ki-check fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Template Selection Card --}}
                <div class="card shadow-sm">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Template Type</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Choose the template that fits your
                                needs</span>
                        </h3>
                    </div>

                    <div class="card-body pt-0">
                        <form action="{{ route('customer.catalog.update', $flipbook) }}" method="POST"
                            id="templateForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="title" value="{{ $flipbook->title }}">
                            <input type="hidden" name="description" value="{{ $flipbook->description }}">
                            <input type="hidden" name="visibility" value="{{ $flipbook->visibility }}">

                            <div class="row g-6">
                                {{-- Page Management --}}
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="template_type" value="page_management"
                                        id="template_page"
                                        {{ old('template_type', $flipbook->template_type) === 'page_management' ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <label
                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary p-6 d-flex flex-column align-items-start h-100"
                                        for="template_page">
                                        <div class="d-flex align-items-center mb-5">
                                            <div class="symbol symbol-50px me-3">
                                                <span class="symbol-label bg-light-primary">
                                                    <i class="ki-duotone ki-document fs-2x text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                            </div>
                                        </div>
                                        <span class="d-block fw-semibold text-start">
                                            <span class="text-gray-900 fw-bold d-block fs-5 mb-2">Page
                                                Management</span>
                                            <span class="text-gray-400 fw-semibold fs-7">Reorder, rename, and
                                                organize pages</span>
                                        </span>
                                    </label>
                                </div>

                                {{-- Flip Physics --}}
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="template_type"
                                        value="page_flip_physics" id="template_physics"
                                        {{ old('template_type', $flipbook->template_type) === 'page_flip_physics' ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <label
                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary p-6 d-flex flex-column align-items-start h-100"
                                        for="template_physics">
                                        <div class="d-flex align-items-center mb-5">
                                            <div class="symbol symbol-50px me-3">
                                                <span class="symbol-label bg-light-success">
                                                    <i class="ki-duotone ki-abstract-26 fs-2x text-success">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                            </div>
                                        </div>
                                        <span class="d-block fw-semibold text-start">
                                            <span class="text-gray-900 fw-bold d-block fs-5 mb-2">Flip
                                                Physics</span>
                                            <span class="text-gray-400 fw-semibold fs-7">Configure realistic flip
                                                animations</span>
                                        </span>
                                    </label>
                                </div>

                                {{-- Slicer (Shoppable) --}}
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="template_type" value="slicer"
                                        id="template_slicer"
                                        {{ old('template_type', $flipbook->template_type ?? 'slicer') === 'slicer' ? 'checked' : '' }}
                                        onchange="this.form.submit()" required>
                                    <label
                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary p-6 d-flex flex-column align-items-start h-100 position-relative"
                                        for="template_slicer">
                                        <span
                                            class="badge badge-light-warning position-absolute top-0 end-0 m-3">Popular</span>
                                        <div class="d-flex align-items-center mb-5">
                                            <div class="symbol symbol-50px me-3">
                                                <span class="symbol-label bg-light-warning">
                                                    <i class="ki-duotone ki-shop fs-2x text-warning">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                    </i>
                                                </span>
                                            </div>
                                        </div>
                                        <span class="d-block fw-semibold text-start">
                                            <span class="text-gray-900 fw-bold d-block fs-5 mb-2">Slicer
                                                (Shoppable)</span>
                                            <span class="text-gray-400 fw-semibold fs-7">Add interactive
                                                hotspots</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            @error('template_type')
                                <div class="invalid-feedback d-block mt-3">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Quick Actions Card --}}
                <div class="card shadow-sm mb-7">
                    <div class="card-body">
                        <div class="mb-7">
                            <h3 class="fw-bold text-gray-900 mb-5">Quick Actions</h3>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <a href="{{ route('customer.template.show', ['flipbook' => $flipbook->id, 'type' => $flipbook->template_type ?? 'slicer']) }}"
                                class="btn btn-light-primary w-100 d-flex justify-content-start">
                                <i class="ki-duotone ki-setting-2 fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="text-start">
                                    <div class="fw-bold">Configure Template</div>
                                    <div class="fs-7 text-muted">Customize template settings</div>
                                </div>
                            </a>

                            @if ($flipbook->status === 'live')
                                <a href="{{ route('flipbook.viewer', $flipbook->slug) }}" target="_blank"
                                    class="btn btn-light-info w-100 d-flex justify-content-start">
                                    <i class="ki-duotone ki-eye fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="text-start">
                                        <div class="fw-bold">View FlipBook</div>
                                        <div class="fs-7 text-muted">Preview your flipbook</div>
                                    </div>
                                </a>

                                <button class="btn btn-light-success w-100 d-flex justify-content-start"
                                    onclick="copyShareLink()">
                                    <i class="ki-duotone ki-copy fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                    <div class="text-start">
                                        <div class="fw-bold">Copy Share Link</div>
                                        <div class="fs-7 text-muted">Share with others</div>
                                    </div>
                                </button>
                            @else
                                <button class="btn btn-light-warning w-100 d-flex justify-content-start"
                                    onclick="publishFlipbook()">
                                    <i class="ki-duotone ki-check-circle fs-2 me-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="text-start">
                                        <div class="fw-bold">Publish FlipBook</div>
                                        <div class="fs-7 text-muted">Make it live</div>
                                    </div>
                                </button>
                            @endif

                            <button class="btn btn-light-danger w-100 d-flex justify-content-start"
                                onclick="deleteFlipbook()">
                                <i class="ki-duotone ki-trash fs-2 me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                <div class="text-start">
                                    <div class="fw-bold">Delete FlipBook</div>
                                    <div class="fs-7 text-muted">Remove permanently</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Stats Card --}}
                <div class="card shadow-sm mb-7">
                    <div class="card-body">
                        <h3 class="fw-bold text-gray-900 mb-7">Statistics</h3>

                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-50px me-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-abstract-26 fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-2">{{ $flipbook->hotspots()->count() }}</span>
                                <span class="text-muted fw-semibold">Total Hotspots</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-7">
                            <div class="symbol symbol-50px me-4">
                                <span class="symbol-label bg-light-success">
                                    <i class="ki-duotone ki-document fs-2x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-2">{{ $flipbook->pages_count ?? 0 }}</span>
                                <span class="text-muted fw-semibold">Total Pages</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-50px me-4">
                                <span class="symbol-label bg-light-info">
                                    <i class="ki-duotone ki-eye fs-2x text-info">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-2">{{ $flipbook->views_count ?? 0 }}</span>
                                <span class="text-muted fw-semibold">Total Views</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="fw-bold text-gray-900 mb-7">FlipBook Info</h3>

                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-gray-600 fw-semibold">Status:</span>
                                <span
                                    class="badge badge-light-{{ $flipbook->status === 'live' ? 'success' : 'warning' }}">
                                    {{ ucfirst($flipbook->status ?? 'draft') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-gray-600 fw-semibold">Template:</span>
                                <span
                                    class="text-gray-900 fw-bold">{{ str_replace('_', ' ', ucwords($flipbook->template_type ?? 'Slicer')) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-gray-600 fw-semibold">Visibility:</span>
                                <span
                                    class="text-gray-900 fw-bold">{{ ucfirst($flipbook->visibility ?? 'Private') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-gray-600 fw-semibold">Created:</span>
                                <span
                                    class="text-gray-900 fw-bold">{{ $flipbook->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-gray-600 fw-semibold">Last Updated:</span>
                                <span
                                    class="text-gray-900 fw-bold">{{ $flipbook->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        @if ($flipbook->status === 'live')
                            <div class="separator separator-dashed my-5"></div>
                            <div
                                class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
                                <i class="ki-duotone ki-information-5 fs-2tx text-primary me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-column">
                                    <h5 class="text-gray-900 mb-1">Share URL</h5>
                                    <div class="text-gray-700 fw-semibold fs-7 text-break">
                                        {{ route('flipbook.viewer', $flipbook->slug) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            // Form submission handling
            document.getElementById('editFlipbookForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('save_btn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="indicator-label">Saving...</span>';
            });

            function copyShareLink() {
                const url = "{{ route('flipbook.viewer', $flipbook->slug) }}";
                navigator.clipboard.writeText(url).then(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'Share link copied to clipboard',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }).catch(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to copy link'
                    });
                });
            }

            function publishFlipbook() {
                Swal.fire({
                    title: 'Publish FlipBook?',
                    text: "This will make your flipbook live and accessible!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, publish it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(`/customer/catalog/{{ $flipbook->id }}/publish`, {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }).done(function(response) {
                            Swal.fire('Published!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        }).fail(function() {
                            Swal.fire('Error!', 'Failed to publish flipbook', 'error');
                        });
                    }
                });
            }

            function deleteFlipbook() {
                Swal.fire({
                    title: 'Delete FlipBook?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/customer/catalog/{{ $flipbook->id }}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        }).done(function(response) {
                            Swal.fire('Deleted!', 'FlipBook has been deleted.', 'success').then(() => {
                                window.location.href = "{{ route('customer.catalog.index') }}";
                            });
                        }).fail(function() {
                            Swal.fire('Error!', 'Failed to delete flipbook', 'error');
                        });
                    }
                });
            }
        </script>
    @endpush

</x-default-layout>
