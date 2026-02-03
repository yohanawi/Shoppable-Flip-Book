@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Shoppable FlipBook Editor
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
                        <li class="breadcrumb-item text-muted">{{ $flipbook->title }}</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button onclick="saveAndPublish()" class="btn btn-sm btn-success">
                        <i class="ki-duotone ki-check fs-3"></i>Save & Publish
                    </button>
                    <a href="{{ route('customer.catalog.index') }}" class="btn btn-sm btn-light">Back to Catalog</a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <div class="row g-5">
                    <!-- PDF Viewer -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">PDF Canvas</h3>
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
                            <div class="card-body position-relative" style="min-height: 600px; background: #f5f5f5;">
                                <div id="pdfCanvasContainer" style="position: relative; display: inline-block;">
                                    <canvas id="pdfCanvas"></canvas>
                                    <div id="hotspotsLayer"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hotspot Control Panel -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Hotspot Tools</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h5 class="mb-1">How to Create Hotspots</h5>
                                        <span>Click and drag on the PDF to create interactive hotspots</span>
                                    </div>
                                </div>

                                <div class="mb-7">
                                    <label class="form-label">Interaction Type</label>
                                    <select id="interactionType" class="form-select">
                                        <option value="popup_product">Product Popup</option>
                                        <option value="external_link">External Link</option>
                                        <option value="internal_link">Internal Page Link</option>
                                        <option value="popup_image">Popup Image</option>
                                        <option value="popup_video">Popup Video</option>
                                    </select>
                                </div>

                                <div id="productFields">
                                    <div class="mb-5">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" id="productName" class="form-control"
                                            placeholder="Enter product name">
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label">Price</label>
                                        <input type="number" id="productPrice" class="form-control" placeholder="0.00"
                                            step="0.01">
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label">Action URL</label>
                                        <input type="url" id="actionUrl" class="form-control"
                                            placeholder="https://example.com/product">
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label">Thumbnail Image</label>
                                        <input type="file" id="thumbnailImage" class="form-control" accept="image/*">
                                    </div>
                                </div>

                                <div id="linkFields" style="display:none;">
                                    <div class="mb-5">
                                        <label class="form-label">Target URL</label>
                                        <input type="url" id="targetUrl" class="form-control"
                                            placeholder="https://example.com">
                                    </div>
                                    <div class="form-check mb-5">
                                        <input class="form-check-input" type="checkbox" id="openNewTab" checked>
                                        <label class="form-check-label" for="openNewTab">Open in new tab</label>
                                    </div>
                                </div>

                                <div class="separator my-7"></div>

                                <h5 class="mb-5">Hotspots on Current Page (<span id="hotspotCount">0</span>)</h5>
                                <div id="hotspotList" class="d-flex flex-column gap-3"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script>
            const flipbookId = {{ $flipbook->id }};
            const pdfUrl = '{{ asset('storage/' . $flipbook->pdf_path) }}';
            let pdfDoc = null;
            let currentPage = 1;
            let totalPages = 0;
            let isDrawing = false;
            let startX, startY;
            let hotspots = [];

            // Initialize PDF.js
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            // Load PDF
            async function loadPDF() {
                try {
                    pdfDoc = await pdfjsLib.getDocument(pdfUrl).promise;
                    totalPages = pdfDoc.numPages;
                    $('#totalPages').text(totalPages);
                    await renderPage(currentPage);
                    loadHotspots();
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

                enableHotspotDrawing();
                renderHotspots();
            }

            function enableHotspotDrawing() {
                const canvas = $('#pdfCanvas');
                canvas.off(); // Remove previous handlers

                canvas.on('mousedown', function(e) {
                    isDrawing = true;
                    const rect = this.getBoundingClientRect();
                    startX = e.clientX - rect.left;
                    startY = e.clientY - rect.top;
                });

                canvas.on('mousemove', function(e) {
                    if (!isDrawing) return;
                    // Draw temporary hotspot preview
                });

                canvas.on('mouseup', function(e) {
                    if (!isDrawing) return;
                    isDrawing = false;

                    const rect = this.getBoundingClientRect();
                    const endX = e.clientX - rect.left;
                    const endY = e.clientY - rect.top;

                    const width = Math.abs(endX - startX);
                    const height = Math.abs(endY - startY);

                    if (width > 20 && height > 20) {
                        createHotspot(startX, startY, width, height);
                    }
                });
            }

            function createHotspot(x, y, width, height) {
                const canvas = $('#pdfCanvas')[0];
                const xPercent = (x / canvas.width) * 100;
                const yPercent = (y / canvas.height) * 100;
                const widthPercent = (width / canvas.width) * 100;
                const heightPercent = (height / canvas.height) * 100;

                const formData = new FormData();
                formData.append('page_number', currentPage);
                formData.append('x_position', xPercent);
                formData.append('y_position', yPercent);
                formData.append('width', widthPercent);
                formData.append('height', heightPercent);
                formData.append('interaction_type', $('#interactionType').val());
                formData.append('product_name', $('#productName').val());
                formData.append('price', $('#productPrice').val());
                formData.append('action_url', $('#actionUrl').val());

                const thumbnailFile = $('#thumbnailImage')[0].files[0];
                if (thumbnailFile) {
                    formData.append('thumbnail_image', thumbnailFile);
                }

                $.ajax({
                    url: `/customer/hotspots/${flipbookId}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Success!', 'Hotspot created successfully', 'success');
                        loadHotspots();
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to create hotspot', 'error');
                    }
                });
            }

            function loadHotspots() {
                $.get(`/flipbooks/${flipbookId}/hotspots-all`, function(data) {
                    hotspots = data.filter(h => h.page_number == currentPage);
                    renderHotspots();
                    updateHotspotList();
                });
            }

            function renderHotspots() {
                const layer = $('#hotspotsLayer');
                layer.empty();

                hotspots.forEach(hotspot => {
                    const div = $('<div>').css({
                        position: 'absolute',
                        left: hotspot.x_position + '%',
                        top: hotspot.y_position + '%',
                        width: hotspot.width + '%',
                        height: hotspot.height + '%',
                        border: '2px solid #00ff00',
                        background: 'rgba(0, 255, 0, 0.1)',
                        pointerEvents: 'all',
                        cursor: 'pointer'
                    });
                    layer.append(div);
                });
            }

            function updateHotspotList() {
                const list = $('#hotspotList');
                list.empty();
                $('#hotspotCount').text(hotspots.length);

                hotspots.forEach((hotspot, index) => {
                    const item = $(`
            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                <div>
                    <div class="fw-bold">${hotspot.product_name || 'Hotspot ' + (index + 1)}</div>
                    <div class="text-muted fs-7">${hotspot.interaction_type}</div>
                </div>
                <button class="btn btn-sm btn-icon btn-light-danger" onclick="deleteHotspot(${hotspot.id})">
                    <i class="ki-duotone ki-trash fs-5"></i>
                </button>
            </div>
        `);
                    list.append(item);
                });
            }

            function deleteHotspot(id) {
                Swal.fire({
                    title: 'Delete Hotspot?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/customer/hotspots/${flipbookId}/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                Swal.fire('Deleted!', 'Hotspot removed', 'success');
                                loadHotspots();
                            }
                        });
                    }
                });
            }

            function prevPage() {
                if (currentPage > 1) {
                    renderPage(currentPage - 1);
                }
            }

            function nextPage() {
                if (currentPage < totalPages) {
                    renderPage(currentPage + 1);
                }
            }

            function saveAndPublish() {
                $.post(`/customer/catalog/${flipbookId}/publish`, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }).done(function(response) {
                    Swal.fire('Published!', response.message, 'success').then(() => {
                        window.location.href = '{{ route('customer.catalog.index') }}';
                    });
                }).fail(function() {
                    Swal.fire('Error!', 'Failed to publish', 'error');
                });
            }

            // Change interaction type handler
            $('#interactionType').on('change', function() {
                const type = $(this).val();
                $('#productFields, #linkFields').hide();

                if (type === 'popup_product') {
                    $('#productFields').show();
                } else if (type === 'external_link' || type === 'internal_link') {
                    $('#linkFields').show();
                }
            });

            // Initialize
            $(document).ready(function() {
                loadPDF();
            });
        </script>
    @endpush
@endsection
