@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Page Management - {{ $flipbook->title }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.catalog.index') }}"
                                class="text-muted text-hover-primary">Catalog</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Page Management</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ route('customer.catalog.show', $flipbook) }}" class="btn btn-sm btn-light-primary">
                        <i class="bi bi-arrow-left fs-4"></i> Back to Flipbook
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" id="savePagesBtn">
                        <i class="bi bi-save fs-4"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">

                <!-- Toolbar -->
                <div class="card mb-5">
                    <div class="card-body d-flex justify-content-between align-items-center py-5">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-light-danger" id="bulkDeleteBtn" disabled>
                                <i class="bi bi-trash fs-4"></i> Delete Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-light-warning" id="bulkLockBtn" disabled>
                                <i class="bi bi-lock fs-4"></i> Lock Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-light-info" id="bulkUnlockBtn" disabled>
                                <i class="bi bi-unlock fs-4"></i> Unlock Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-light-secondary" id="bulkHideBtn" disabled>
                                <i class="bi bi-eye-slash fs-4"></i> Hide Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-light-success" id="bulkShowBtn" disabled>
                                <i class="bi bi-eye fs-4"></i> Show Selected
                            </button>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="selectAllPages" />
                                <span class="form-check-label">Select All</span>
                            </label>
                            <span class="badge badge-light-primary" id="selectedCount">0 Selected</span>
                        </div>
                    </div>
                </div>

                <!-- Pages Grid -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pages (Drag to Reorder)</h3>
                    </div>
                    <div class="card-body">
                        <div id="pagesContainer" class="row g-6 g-xl-9">
                            @foreach ($pages as $page)
                                <div class="col-md-6 col-xl-4" data-page-id="{{ $page->id }}"
                                    data-display-order="{{ $page->display_order }}">
                                    <div
                                        class="card card-custom page-card {{ $page->is_locked ? 'border-warning' : '' }} {{ $page->is_hidden ? 'opacity-50' : '' }}">
                                        <div class="card-header min-h-50px">
                                            <div class="card-title">
                                                <input type="checkbox" class="form-check-input page-checkbox me-2"
                                                    value="{{ $page->id }}">
                                                <span class="fw-bold page-title-display">
                                                    {{ $page->custom_name ?? 'Page ' . $page->page_number }}
                                                </span>
                                            </div>
                                            <div class="card-toolbar">
                                                @if ($page->is_locked)
                                                    <span class="badge badge-warning me-2">
                                                        <i class="bi bi-lock"></i>
                                                    </span>
                                                @endif
                                                @if ($page->is_hidden)
                                                    <span class="badge badge-secondary me-2">
                                                        <i class="bi bi-eye-slash"></i>
                                                    </span>
                                                @endif
                                                <button class="btn btn-sm btn-icon btn-light-primary drag-handle"
                                                    title="Drag to reorder">
                                                    <i class="bi bi-arrows-move"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body p-3 text-center">
                                            <img src="{{ $page->getThumbnailUrl() }}" alt="Page {{ $page->page_number }}"
                                                class="img-fluid rounded mb-3" style="max-height: 200px;">
                                            <div class="page-info small text-muted">
                                                Page {{ $page->page_number }} | Order: <span
                                                    class="display-order-text">{{ $page->display_order }}</span>
                                            </div>
                                        </div>
                                        <div class="card-footer d-flex justify-content-between gap-2">
                                            <button class="btn btn-sm btn-light-info rename-page-btn"
                                                data-page-id="{{ $page->id }}"
                                                data-current-name="{{ $page->custom_name ?? '' }}"
                                                {{ $page->is_locked ? 'disabled' : '' }}>
                                                <i class="bi bi-pencil"></i> Rename
                                            </button>
                                            <button
                                                class="btn btn-sm btn-light-{{ $page->is_locked ? 'warning' : 'secondary' }} toggle-lock-btn"
                                                data-page-id="{{ $page->id }}"
                                                data-is-locked="{{ $page->is_locked ? 'true' : 'false' }}">
                                                <i class="bi bi-{{ $page->is_locked ? 'unlock' : 'lock' }}"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light-secondary toggle-visibility-btn"
                                                data-page-id="{{ $page->id }}"
                                                data-is-hidden="{{ $page->is_hidden ? 'true' : 'false' }}"
                                                {{ $page->is_locked ? 'disabled' : '' }}>
                                                <i class="bi bi-{{ $page->is_hidden ? 'eye' : 'eye-slash' }}"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light-danger delete-page-btn"
                                                data-page-id="{{ $page->id }}"
                                                {{ $page->is_locked ? 'disabled' : '' }}>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rename Modal -->
    <div class="modal fade" id="renamePageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rename Page</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="renamePageForm">
                        <input type="hidden" id="renamePageId">
                        <div class="mb-3">
                            <label for="customPageName" class="form-label">Custom Page Name</label>
                            <input type="text" class="form-control" id="customPageName" placeholder="Enter page name"
                                required maxlength="255">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveRenameBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        const flipbookId = {{ $flipbook->id }};
        const csrfToken = '{{ csrf_token() }}';

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Sortable for drag and drop
            const pagesContainer = document.getElementById('pagesContainer');
            const sortable = new Sortable(pagesContainer, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    updateDisplayOrders();
                }
            });

            // Update display orders after drag
            function updateDisplayOrders() {
                const cards = pagesContainer.querySelectorAll('.col-md-6');
                cards.forEach((card, index) => {
                    card.setAttribute('data-display-order', index);
                    card.querySelector('.display-order-text').textContent = index;
                });
            }

            // Select all checkbox
            document.getElementById('selectAllPages').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.page-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateSelectedCount();
            });

            // Individual checkboxes
            document.querySelectorAll('.page-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            function updateSelectedCount() {
                const selected = document.querySelectorAll('.page-checkbox:checked');
                document.getElementById('selectedCount').textContent = `${selected.length} Selected`;

                // Enable/disable bulk buttons
                const hasSelection = selected.length > 0;
                document.getElementById('bulkDeleteBtn').disabled = !hasSelection;
                document.getElementById('bulkLockBtn').disabled = !hasSelection;
                document.getElementById('bulkUnlockBtn').disabled = !hasSelection;
                document.getElementById('bulkHideBtn').disabled = !hasSelection;
                document.getElementById('bulkShowBtn').disabled = !hasSelection;
            }

            // Save pages (reorder)
            document.getElementById('savePagesBtn').addEventListener('click', function() {
                const cards = pagesContainer.querySelectorAll('.col-md-6');
                const pageOrders = Array.from(cards).map((card, index) => ({
                    id: parseInt(card.getAttribute('data-page-id')),
                    display_order: index
                }));

                fetch(`/customer/pages/${flipbookId}/reorder`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            page_orders: pageOrders
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', data.message, 'success');
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Failed to save page order.', 'error');
                    });
            });

            // Rename page
            document.querySelectorAll('.rename-page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pageId = this.getAttribute('data-page-id');
                    const currentName = this.getAttribute('data-current-name');

                    document.getElementById('renamePageId').value = pageId;
                    document.getElementById('customPageName').value = currentName;

                    const modal = new bootstrap.Modal(document.getElementById('renamePageModal'));
                    modal.show();
                });
            });

            document.getElementById('saveRenameBtn').addEventListener('click', function() {
                const pageId = document.getElementById('renamePageId').value;
                const customName = document.getElementById('customPageName').value.trim();

                if (!customName) {
                    Swal.fire('Error!', 'Please enter a page name.', 'error');
                    return;
                }

                fetch(`/customer/pages/${flipbookId}/${pageId}/rename`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            custom_name: customName
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('renamePageModal'))
                                .hide();
                            Swal.fire('Success!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Failed to rename page.', 'error');
                    });
            });

            // Toggle lock
            document.querySelectorAll('.toggle-lock-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pageId = this.getAttribute('data-page-id');

                    fetch(`/customer/pages/${flipbookId}/${pageId}/toggle-lock`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Success!', data.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Failed to toggle lock.', 'error');
                        });
                });
            });

            // Toggle visibility
            document.querySelectorAll('.toggle-visibility-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pageId = this.getAttribute('data-page-id');

                    fetch(`/customer/pages/${flipbookId}/${pageId}/toggle-visibility`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Success!', data.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Failed to toggle visibility.', 'error');
                        });
                });
            });

            // Delete page
            document.querySelectorAll('.delete-page-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const pageId = this.getAttribute('data-page-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This page and all its hotspots will be deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/customer/pages/${flipbookId}/${pageId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Deleted!', data.message, 'success')
                                            .then(() => {
                                                location.reload();
                                            });
                                    } else {
                                        Swal.fire('Error!', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire('Error!', 'Failed to delete page.',
                                        'error');
                                });
                        }
                    });
                });
            });

            // Bulk operations
            function performBulkOperation(operation) {
                const selectedIds = Array.from(document.querySelectorAll('.page-checkbox:checked'))
                    .map(cb => parseInt(cb.value));

                if (selectedIds.length === 0) {
                    Swal.fire('Error!', 'Please select at least one page.', 'error');
                    return;
                }

                fetch(`/customer/pages/${flipbookId}/bulk`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            operation: operation,
                            page_ids: selectedIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error!', 'Failed to perform bulk operation.', 'error');
                    });
            }

            document.getElementById('bulkDeleteBtn').addEventListener('click', () => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Selected pages will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performBulkOperation('delete');
                    }
                });
            });

            document.getElementById('bulkLockBtn').addEventListener('click', () => performBulkOperation('lock'));
            document.getElementById('bulkUnlockBtn').addEventListener('click', () => performBulkOperation(
            'unlock'));
            document.getElementById('bulkHideBtn').addEventListener('click', () => performBulkOperation('hide'));
            document.getElementById('bulkShowBtn').addEventListener('click', () => performBulkOperation('show'));
        });
    </script>
    <style>
        .sortable-ghost {
            opacity: 0.4;
        }

        .page-card {
            transition: all 0.3s ease;
        }

        .page-card:hover {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .drag-handle {
            cursor: move;
        }
    </style>
@endpush
