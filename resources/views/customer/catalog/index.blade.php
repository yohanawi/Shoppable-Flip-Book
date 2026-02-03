<x-default-layout>

    @section('title')
        My FlipBooks
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('customer.catalog.index') }}
    @endsection

    {{-- Content --}}
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container">

            {{-- Success Alert --}}
            @if (session('success'))
                <div class="alert alert-dismissible bg-light-success d-flex flex-column flex-sm-row p-5 mb-10">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4 mb-5 mb-sm-0">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-column pe-0 pe-sm-10">
                        <h4 class="fw-semibold">Success!</h4>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button"
                        class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                        data-bs-dismiss="alert">
                        <i class="ki-duotone ki-cross fs-1 text-success">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                </div>
            @endif

            {{-- Statistics Cards --}}
            @if ($flipbooks->count() > 0)
                <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                    <div class="col-xl-3">
                        <div class="card card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i
                                    class="ki-duotone ki-book fs-2hx text-gray-600 position-absolute top-0 end-0 mt-5 me-5 opacity-15">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <div class="fw-bold fs-2 text-gray-800 mb-2 mt-5">{{ $flipbooks->total() }}</div>
                                <div class="fw-semibold text-gray-400">Total FlipBooks</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i
                                    class="ki-duotone ki-check-circle fs-2hx text-success position-absolute top-0 end-0 mt-5 me-5 opacity-15">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="fw-bold fs-2 text-success mb-2 mt-5">
                                    {{ $flipbooks->where('status', 'live')->count() }}</div>
                                <div class="fw-semibold text-gray-400">Live FlipBooks</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i
                                    class="ki-duotone ki-notepad-edit fs-2hx text-warning position-absolute top-0 end-0 mt-5 me-5 opacity-15">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="fw-bold fs-2 text-warning mb-2 mt-5">
                                    {{ $flipbooks->where('status', 'draft')->count() }}</div>
                                <div class="fw-semibold text-gray-400">Draft FlipBooks</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3">
                        <div class="card card-xl-stretch mb-xl-8">
                            <div class="card-body">
                                <i
                                    class="ki-duotone ki-abstract-26 fs-2hx text-primary position-absolute top-0 end-0 mt-5 me-5 opacity-15">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="fw-bold fs-2 text-primary mb-2 mt-5">
                                    {{ $flipbooks->sum(function ($f) {return $f->hotspots()->count();}) }}</div>
                                <div class="fw-semibold text-gray-400">Total Hotspots</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- FlipBooks Grid --}}
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" class="form-control form-control-solid w-250px ps-13"
                                placeholder="Search FlipBooks..." id="kt_search_flipbooks">
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-subscription-table-toolbar="base">
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                                data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-filter fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Filter
                            </button>
                        </div>
                        <a href="{{ route('customer.catalog.create') }}" class="btn btn-sm btn-primary">
                            <i class="ki-duotone ki-plus fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Create FlipBook
                        </a>
                    </div>
                </div>

                <div class="card-body pt-0">
                    @if ($flipbooks->count() > 0)
                        <div class="row g-6 g-xl-9">
                            @foreach ($flipbooks as $flipbook)
                                <div class="col-md-6 col-xl-4">
                                    <div class="card border-2 border-gray-300 border-hover-primary h-100">
                                        <div class="card-header border-0 pt-9">
                                            <div class="card-title m-0">
                                                <div class="symbol symbol-50px w-50px bg-light">
                                                    <i class="ki-duotone ki-book fs-2x text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <div class="card-toolbar">
                                                <span
                                                    class="badge badge-light-{{ $flipbook->status === 'live' ? 'success' : ($flipbook->status === 'draft' ? 'warning' : 'secondary') }} fw-bold px-4 py-3">
                                                    {{ ucfirst($flipbook->status ?? 'draft') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="card-body d-flex flex-column p-9">
                                            <div class="mb-5">
                                                <a href="{{ route('customer.catalog.edit', $flipbook) }}"
                                                    class="fs-4 fw-bold text-gray-900 text-hover-primary text-dark mb-2 d-block">
                                                    {{ $flipbook->title }}
                                                </a>
                                                <p class="text-gray-400 fw-semibold fs-6 mb-0">
                                                    {{ Str::limit($flipbook->description, 100) }}
                                                </p>
                                            </div>

                                            <div class="d-flex flex-stack flex-wrap mb-5">
                                                <div class="d-flex align-items-center me-5 mb-2">
                                                    <div class="symbol symbol-30px symbol-circle me-3">
                                                        <span class="symbol-label bg-light-success">
                                                            <i class="ki-duotone ki-abstract-41 fs-5 text-success">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fs-7 text-gray-800 fw-bold">
                                                            {{ $flipbook->template_type ? str_replace('_', ' ', ucwords($flipbook->template_type)) : 'Slicer' }}
                                                        </div>
                                                        <div class="fs-8 fw-semibold text-gray-400">Template</div>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="symbol symbol-30px symbol-circle me-3">
                                                        <span class="symbol-label bg-light-primary">
                                                            <i class="ki-duotone ki-abstract-26 fs-5 text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fs-7 text-gray-800 fw-bold">
                                                            {{ $flipbook->hotspots()->count() }}</div>
                                                        <div class="fs-8 fw-semibold text-gray-400">Hotspots</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed mb-5"></div>

                                            <div class="d-flex flex-stack mt-auto">
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ route('customer.catalog.edit', $flipbook) }}"
                                                        class="btn btn-sm btn-light btn-active-light-primary"
                                                        data-bs-toggle="tooltip" title="Edit FlipBook">
                                                        <i class="ki-duotone ki-pencil fs-5">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </a>

                                                    @if ($flipbook->status === 'draft')
                                                        <button class="btn btn-sm btn-light btn-active-light-success"
                                                            onclick="publishFlipbook({{ $flipbook->id }})"
                                                            data-bs-toggle="tooltip" title="Publish FlipBook">
                                                            <i class="ki-duotone ki-check-circle fs-5">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('flipbook.viewer', $flipbook->slug) }}"
                                                            target="_blank"
                                                            class="btn btn-sm btn-light btn-active-light-info"
                                                            data-bs-toggle="tooltip" title="View FlipBook">
                                                            <i class="ki-duotone ki-eye fs-5">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </a>
                                                    @endif

                                                    <button class="btn btn-sm btn-light btn-active-light-danger"
                                                        onclick="deleteFlipbook({{ $flipbook->id }})"
                                                        data-bs-toggle="tooltip" title="Delete FlipBook">
                                                        <i class="ki-duotone ki-trash fs-5">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                            <span class="path5"></span>
                                                        </i>
                                                    </button>
                                                </div>

                                                <div class="text-end">
                                                    <span class="text-gray-400 fw-semibold fs-8">
                                                        {{ $flipbook->updated_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex flex-stack flex-wrap pt-10">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Showing {{ $flipbooks->firstItem() }} to {{ $flipbooks->lastItem() }} of
                                {{ $flipbooks->total() }} entries
                            </div>
                            {{ $flipbooks->links() }}
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="card card-flush h-xl-100">
                            <div class="card-body text-center py-20">
                                <div class="mb-10">
                                    <div class="symbol symbol-100px symbol-circle mb-7">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-book fs-3x text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </span>
                                    </div>

                                    <h1 class="fw-bold text-gray-900 mb-5">No FlipBooks Yet</h1>
                                    <div class="fw-semibold fs-6 text-gray-500 mb-8">
                                        Get started by creating your first interactive flipbook.<br>
                                        Transform your PDFs into engaging digital experiences!
                                    </div>

                                    <div class="text-center">
                                        <a href="{{ route('customer.catalog.create') }}" class="btn btn-primary">
                                            <i class="ki-duotone ki-plus fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Create Your First FlipBook
                                        </a>
                                    </div>
                                </div>

                                <div class="separator separator-dashed border-gray-300 mb-10"></div>

                                <div class="row g-5">
                                    <div class="col-md-4">
                                        <div class="d-flex flex-column">
                                            <div class="symbol symbol-60px symbol-circle mb-5 mx-auto">
                                                <span class="symbol-label bg-light-success">
                                                    <i class="ki-duotone ki-rocket fs-2x text-success">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                            </div>
                                            <h3 class="fs-5 fw-bold text-gray-800 mb-2">Quick & Easy</h3>
                                            <span class="text-gray-400 fw-semibold fs-7">Upload your PDF and create
                                                a flipbook in minutes</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="d-flex flex-column">
                                            <div class="symbol symbol-60px symbol-circle mb-5 mx-auto">
                                                <span class="symbol-label bg-light-primary">
                                                    <i class="ki-duotone ki-abstract-26 fs-2x text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                            </div>
                                            <h3 class="fs-5 fw-bold text-gray-800 mb-2">Interactive Hotspots</h3>
                                            <span class="text-gray-400 fw-semibold fs-7">Add clickable hotspots to
                                                engage your audience</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="d-flex flex-column">
                                            <div class="symbol symbol-60px symbol-circle mb-5 mx-auto">
                                                <span class="symbol-label bg-light-warning">
                                                    <i class="ki-duotone ki-chart-simple fs-2x text-warning">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                    </i>
                                                </span>
                                            </div>
                                            <h3 class="fs-5 fw-bold text-gray-800 mb-2">Track Performance</h3>
                                            <span class="text-gray-400 fw-semibold fs-7">Monitor views and
                                                engagement analytics</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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

            // Search functionality
            document.getElementById('kt_search_flipbooks')?.addEventListener('keyup', function(e) {
                const searchText = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.col-md-6.col-xl-4');

                cards.forEach(card => {
                    const title = card.querySelector('.fs-4')?.textContent.toLowerCase() || '';
                    const description = card.querySelector('.text-gray-400')?.textContent.toLowerCase() || '';

                    if (title.includes(searchText) || description.includes(searchText)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            function publishFlipbook(id) {
                Swal.fire({
                    title: 'Publish FlipBook?',
                    text: "This will make your flipbook live and accessible to viewers!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, publish it!',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Publishing...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.post(`/customer/catalog/${id}/publish`, {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }).done(function(response) {
                            Swal.fire({
                                title: 'Published!',
                                text: response.message,
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            }).then(() => {
                                location.reload();
                            });
                        }).fail(function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to publish flipbook',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        });
                    }
                });
            }

            function deleteFlipbook(id) {
                Swal.fire({
                    title: 'Delete FlipBook?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Deleting...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: `/customer/catalog/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        }).done(function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'FlipBook has been deleted.',
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            }).then(() => {
                                location.reload();
                            });
                        }).fail(function() {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to delete flipbook',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        });
                    }
                });
            }
        </script>
    @endpush
</x-default-layout>
