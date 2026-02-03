@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        My Catalog
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Catalog</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('customer.catalog.create') }}" class="btn btn-sm fw-bold btn-primary">
                        <i class="ki-duotone ki-plus fs-3"></i>Create FlipBook
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body pt-0">
                        @if ($flipbooks->count() > 0)
                            <div class="row g-6 g-xl-9 mt-3">
                                @foreach ($flipbooks as $flipbook)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card border-2 border-gray-300 border-hover">
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
                                                        class="badge badge-light-{{ $flipbook->status === 'live' ? 'success' : ($flipbook->status === 'draft' ? 'warning' : 'secondary') }} fw-bold me-auto px-4 py-3">
                                                        {{ ucfirst($flipbook->status ?? 'draft') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body p-9">
                                                <div class="fs-3 fw-bold text-dark">{{ $flipbook->title }}</div>
                                                <p class="text-gray-400 fw-semibold fs-5 mt-1 mb-7">
                                                    {{ Str::limit($flipbook->description, 80) }}
                                                </p>
                                                <div class="d-flex flex-wrap mb-5">
                                                    <div
                                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
                                                        <div class="fs-6 text-gray-800 fw-bold">
                                                            {{ $flipbook->template_type ? str_replace('_', ' ', ucwords($flipbook->template_type)) : 'Slicer' }}
                                                        </div>
                                                        <div class="fw-semibold text-gray-400">Template</div>
                                                    </div>
                                                    <div
                                                        class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
                                                        <div class="fs-6 text-gray-800 fw-bold">
                                                            {{ $flipbook->hotspots()->count() }}</div>
                                                        <div class="fw-semibold text-gray-400">Hotspots</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('customer.catalog.edit', $flipbook) }}"
                                                        class="btn btn-sm btn-light-primary">
                                                        <i class="ki-duotone ki-pencil fs-5"><span
                                                                class="path1"></span><span class="path2"></span></i>
                                                        Edit
                                                    </a>
                                                    @if ($flipbook->status === 'draft')
                                                        <button class="btn btn-sm btn-light-success"
                                                            onclick="publishFlipbook({{ $flipbook->id }})">
                                                            <i class="ki-duotone ki-check fs-5"></i>Publish
                                                        </button>
                                                    @else
                                                        <a href="{{ route('flipbook.viewer', $flipbook->slug) }}"
                                                            target="_blank" class="btn btn-sm btn-light-info">
                                                            <i class="ki-duotone ki-eye fs-5"><span
                                                                    class="path1"></span><span class="path2"></span><span
                                                                    class="path3"></span></i>
                                                            View
                                                        </a>
                                                    @endif
                                                    <button class="btn btn-sm btn-light-danger"
                                                        onclick="deleteFlipbook({{ $flipbook->id }})">
                                                        <i class="ki-duotone ki-trash fs-5"><span
                                                                class="path1"></span><span class="path2"></span><span
                                                                class="path3"></span><span class="path4"></span><span
                                                                class="path5"></span></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-10">
                                {{ $flipbooks->links() }}
                            </div>
                        @else
                            <div class="text-center py-20">
                                <i class="ki-duotone ki-book fs-5x text-muted mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <h3 class="text-muted">No FlipBooks Yet</h3>
                                <p class="text-muted fs-5 mb-5">Create your first interactive flipbook</p>
                                <a href="{{ route('customer.catalog.create') }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-plus fs-3"></i>Create FlipBook
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function publishFlipbook(id) {
                Swal.fire({
                    title: 'Publish FlipBook?',
                    text: "This will make your flipbook live and accessible!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, publish it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(`/customer/catalog/${id}/publish`, {
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

            function deleteFlipbook(id) {
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
                            url: `/customer/catalog/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        }).done(function(response) {
                            Swal.fire('Deleted!', 'FlipBook has been deleted.', 'success').then(() => {
                                location.reload();
                            });
                        }).fail(function() {
                            Swal.fire('Error!', 'Failed to delete flipbook', 'error');
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
