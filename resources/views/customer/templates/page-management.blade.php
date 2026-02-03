@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Page Management
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('customer.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">{{ $flipbook->title }}</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button onclick="saveConfiguration()" class="btn btn-sm btn-success">
                        <i class="ki-duotone ki-check fs-3"></i>Save & Continue
                    </button>
                    <a href="{{ route('customer.catalog.index') }}" class="btn btn-sm btn-light">Back to Catalog</a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <div class="row g-5">
                    <!-- PDF Preview -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">PDF Preview</h3>
                                <div class="card-toolbar">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-light" onclick="prevPage()">
                                            <i class="ki-duotone ki-left fs-3"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light disabled">
                                            Page <span id="currentPage">1</span> of <span id="totalPages">0</span>
                                        </button>
                                        <button class="btn btn-sm btn-light" onclick="nextPage()">
                                            <i class="ki-duotone ki-right fs-3"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body"
                                style="min-height: 600px; background: #f5f5f5; display: flex; justify-content: center; align-items: center;">
                                <canvas id="pdfCanvas"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Page Controls -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Page Settings</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h5 class="mb-1">Page Management</h5>
                                        <span>Reorder, rename, and organize your pages</span>
                                    </div>
                                </div>

                                <div id="currentPageSettings" class="mb-10">
                                    <h5 class="mb-5">Current Page Settings</h5>

                                    <div class="mb-5">
                                        <label class="form-label">Page Name</label>
                                        <input type="text" id="pageName" class="form-control"
                                            placeholder="Enter custom page name">
                                    </div>

                                    <div class="form-check form-switch mb-5">
                                        <input class="form-check-input" type="checkbox" id="pageLocked">
                                        <label class="form-check-label" for="pageLocked">
                                            Lock Page (Prevent deletion)
                                        </label>
                                    </div>

                                    <div class="form-check form-switch mb-5">
                                        <input class="form-check-input" type="checkbox" id="pageHidden">
                                        <label class="form-check-label" for="pageHidden">
                                            Hide Page (Not visible in viewer)
                                        </label>
                                    </div>

                                    <button onclick="updateCurrentPage()" class="btn btn-sm btn-primary w-100">
                                        Update Page Settings
                                    </button>
                                </div>

                                <div class="separator my-7"></div>

                                <h5 class="mb-5">All Pages</h5>
                                <div id="pagesList" class="d-flex flex-column gap-2"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            const flipbookId = {{ $flipbook->id }};
            const pdfUrl = '{{ asset('storage/' . $flipbook->pdf_path) }}';
            let pdfDoc = null;
            let currentPage = 1;
            let totalPages = 0;
            let pageStructure = @json($flipbook->page_structure ?? []);

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            async function loadPDF() {
                try {
                    pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
                    totalPages = pdfDoc.numPages;
                    $('#totalPages').text(totalPages);
                    await renderPage(currentPage);
                    renderPagesList();
                } catch (error) {
                    console.error('Error loading PDF:', error);
                    Swal.fire('Error', 'Failed to load PDF', 'error');
                }
            }

            async function renderPage(pageNum) {
                const page = await pdfDoc.getPage(pageNum);
                const viewport = page.getViewport({
                    scale: 1.5
                });

                const canvas = document.getElementById('pdfCanvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;
                currentPage = pageNum;
                $('#currentPage').text(pageNum);

                loadPageSettings();
            }

            function loadPageSettings() {
                const settings = pageStructure[currentPage] || {};
                $('#pageName').val(settings.name || '');
                $('#pageLocked').prop('checked', settings.locked || false);
                $('#pageHidden').prop('checked', settings.hidden || false);
            }

            function updateCurrentPage() {
                const settings = {
                    name: $('#pageName').val(),
                    locked: $('#pageLocked').is(':checked'),
                    hidden: $('#pageHidden').is(':checked')
                };

                $.ajax({
                    url: `/customer/pages/${flipbookId}/${currentPage}`,
                    type: 'PUT',
                    data: settings,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Success!', 'Page settings updated', 'success');
                        pageStructure[currentPage] = settings;
                        renderPagesList();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to update page', 'error');
                    }
                });
            }

            function renderPagesList() {
                const list = $('#pagesList');
                list.empty();

                for (let i = 1; i <= totalPages; i++) {
                    const settings = pageStructure[i] || {};
                    const item = $(`
            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded page-item" data-page="${i}">
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-menu fs-2 text-muted me-3" style="cursor: move;">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div>
                        <div class="fw-bold">Page ${i}</div>
                        ${settings.name ? `<div class="text-muted fs-7">${settings.name}</div>` : ''}
                    </div>
                </div>
                <div class="d-flex gap-2">
                    ${settings.locked ? '<span class="badge badge-light-warning">Locked</span>' : ''}
                    ${settings.hidden ? '<span class="badge badge-light-secondary">Hidden</span>' : ''}
                    <button class="btn btn-sm btn-icon btn-light" onclick="goToPage(${i})">
                        <i class="ki-duotone ki-eye fs-5"></i>
                    </button>
                </div>
            </div>
        `);
                    list.append(item);
                }

                // Enable drag-and-drop reordering
                new Sortable(list[0], {
                    animation: 150,
                    handle: '.ki-menu',
                    onEnd: function(evt) {
                        reorderPages();
                    }
                });
            }

            function goToPage(pageNum) {
                renderPage(pageNum);
            }

            function reorderPages() {
                const newOrder = [];
                $('#pagesList .page-item').each(function(index) {
                    newOrder.push({
                        page_number: $(this).data('page'),
                        new_order: index + 1
                    });
                });

                $.ajax({
                    url: `/customer/pages/${flipbookId}/reorder`,
                    type: 'POST',
                    data: {
                        pages: newOrder
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Success!', 'Pages reordered', 'success');
                    }
                });
            }

            function prevPage() {
                if (currentPage > 1) renderPage(currentPage - 1);
            }

            function nextPage() {
                if (currentPage < totalPages) renderPage(currentPage + 1);
            }

            function saveConfiguration() {
                $.post(`/customer/template/${flipbookId}/config`, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    page_structure: pageStructure
                }).done(function() {
                    Swal.fire('Saved!', 'Configuration saved successfully', 'success').then(() => {
                        window.location.href = '{{ route('customer.catalog.index') }}';
                    });
                }).fail(function() {
                    Swal.fire('Error!', 'Failed to save configuration', 'error');
                });
            }

            $(document).ready(function() {
                loadPDF();
            });
        </script>
    @endpush
@endsection
