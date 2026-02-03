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
                                    <button id="drawModeBtn" class="btn btn-sm btn-primary ms-3" onclick="toggleDrawMode()">
                                        <i class="ki-duotone ki-plus fs-3"></i>Draw Hotspot
                                    </button>
                                </div>
                            </div>
                            <div class="card-body position-relative" style="min-height: 600px; background: #f5f5f5;">
                                <div id="pdfCanvasContainer" class="page-canvas-wrapper"
                                    style="position: relative; display: inline-block;">
                                    <canvas id="pdfCanvas" class="canvas-container"></canvas>
                                    <div id="hotspotsLayer"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;">
                                    </div>
                                    <div id="drawingOverlay"
                                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: none; background: rgba(59, 130, 246, 0.05); border: 3px dashed #3b82f6; pointer-events: none; z-index: 999;">
                                        <div
                                            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(59, 130, 246, 0.9); color: white; padding: 15px 30px; border-radius: 8px; font-weight: bold; font-size: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                                            <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span
                                                    class="path2"></span></i>
                                            Click and Drag to Draw Hotspot
                                        </div>
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
                                        <span>Click "Draw Hotspot", then click and drag on the PDF to create interactive
                                            regions. You can also drag and resize existing hotspots.</span>
                                    </div>
                                </div>

                                <div class="mb-7">
                                    <label class="form-label required">Interaction Type</label>
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
                                        <label class="form-label required">Product Name</label>
                                        <input type="text" id="productName" class="form-control"
                                            placeholder="Enter product name">
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label">Description</label>
                                        <textarea id="productDesc" class="form-control" rows="2" placeholder="Product description"></textarea>
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
                                        <label class="form-label required">Target URL</label>
                                        <input type="url" id="targetUrl" class="form-control"
                                            placeholder="https://example.com">
                                    </div>
                                    <div class="form-check mb-5">
                                        <input class="form-check-input" type="checkbox" id="openNewTab" checked>
                                        <label class="form-check-label" for="openNewTab">Open in new tab</label>
                                    </div>
                                </div>

                                <div id="videoFields" style="display:none;">
                                    <div class="mb-5">
                                        <label class="form-label required">Video URL</label>
                                        <input type="url" id="videoUrl" class="form-control"
                                            placeholder="https://youtube.com/watch?v=...">
                                        <div class="form-text">YouTube, Vimeo, or direct video URL</div>
                                    </div>
                                </div>

                                <div id="imageFields" style="display:none;">
                                    <div class="mb-5">
                                        <label class="form-label required">Popup Image</label>
                                        <input type="file" id="popupImage" class="form-control" accept="image/*">
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label">Hotspot Color</label>
                                    <input type="color" id="hotspotColor" class="form-control form-control-color"
                                        value="#3b82f6">
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
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <script>
            const flipbookId = {{ $flipbook->id }};
            const pdfUrl = '{{ asset('storage/' . $flipbook->pdf_path) }}';
            let pdfDoc = null;
            let currentPage = 1;
            let totalPages = 0;
            let isDrawing = false;
            let drawMode = false;
            let startX, startY;
            let hotspots = [];
            let selectedHotspot = null;

            // Initialize PDF.js
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

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
                    Swal.fire('Error', 'Failed to load PDF: ' + error.message, 'error');
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

                // Update hotspots layer and drawing overlay size
                const layerCSS = {
                    width: canvas.width + 'px',
                    height: canvas.height + 'px'
                };
                $('#hotspotsLayer').css(layerCSS);
                $('#drawingOverlay').css(layerCSS);

                enableHotspotDrawing();
                renderHotspots();
            }

            function toggleDrawMode() {
                drawMode = !drawMode;
                const btn = $('#drawModeBtn');
                const overlay = $('#drawingOverlay');

                if (drawMode) {
                    btn.removeClass('btn-primary').addClass('btn-danger');
                    btn.html(
                        '<i class="ki-duotone ki-cross fs-3"><span class="path1"></span><span class="path2"></span></i>Cancel Drawing'
                    );
                    $('#pdfCanvas').css('cursor', 'crosshair');
                    overlay.fadeIn(300);
                } else {
                    btn.removeClass('btn-danger').addClass('btn-primary');
                    btn.html(
                        '<i class="ki-duotone ki-plus fs-3"><span class="path1"></span><span class="path2"></span></i>Draw Hotspot'
                    );
                    $('#pdfCanvas').css('cursor', 'default');
                    overlay.fadeOut(300);
                }
            }

            function enableHotspotDrawing() {
                const canvas = $('#pdfCanvas');
                canvas.off(); // Remove previous handlers

                canvas.on('mousedown', function(e) {
                    if (!drawMode) return;

                    isDrawing = true;
                    const rect = this.getBoundingClientRect();
                    startX = e.clientX - rect.left;
                    startY = e.clientY - rect.top;
                });

                canvas.on('mousemove', function(e) {
                    if (!isDrawing || !drawMode) return;
                    // Could add visual preview here
                });

                canvas.on('mouseup', function(e) {
                    if (!isDrawing || !drawMode) return;
                    isDrawing = false;

                    const rect = this.getBoundingClientRect();
                    const endX = e.clientX - rect.left;
                    const endY = e.clientY - rect.top;

                    const x = Math.min(startX, endX);
                    const y = Math.min(startY, endY);
                    const width = Math.abs(endX - startX);
                    const height = Math.abs(endY - startY);

                    if (width > 20 && height > 20) {
                        createHotspot(x, y, width, height);
                        toggleDrawMode(); // Exit draw mode after creating
                    }
                });
            }

            function createHotspot(x, y, width, height) {
                const canvas = $('#pdfCanvas')[0];
                const xPercent = (x / canvas.width) * 100;
                const yPercent = (y / canvas.height) * 100;
                const widthPercent = (width / canvas.width) * 100;
                const heightPercent = (height / canvas.height) * 100;

                const interactionType = $('#interactionType').val();

                const formData = new FormData();
                formData.append('page_number', currentPage);
                formData.append('shape_type', 'rectangle'); // Drawing rectangles
                formData.append('x_position', xPercent);
                formData.append('y_position', yPercent);
                formData.append('width', widthPercent);
                formData.append('height', heightPercent);
                formData.append('color', $('#hotspotColor').val());

                // Map interaction type to action type
                let actionType = '';
                let title = '';

                if (interactionType === 'popup_product') {
                    actionType = 'product';
                    const productName = $('#productName').val();
                    if (!productName) {
                        Swal.fire('Error', 'Please enter a product name', 'error');
                        return;
                    }
                    formData.append('product_name', productName);
                    formData.append('title', productName);
                    formData.append('description', $('#productDesc').val());
                    formData.append('price', $('#productPrice').val() || 0);

                    const actionUrl = $('#actionUrl').val();
                    if (actionUrl) {
                        formData.append('target_url', actionUrl);
                    }

                    const thumbnailFile = $('#thumbnailImage')[0].files[0];
                    if (thumbnailFile) {
                        formData.append('thumbnail_image', thumbnailFile);
                    }
                    title = productName;
                } else if (interactionType === 'external_link') {
                    actionType = 'link';
                    const targetUrl = $('#targetUrl').val();
                    if (!targetUrl) {
                        Swal.fire('Error', 'Please enter a target URL', 'error');
                        return;
                    }
                    formData.append('target_url', targetUrl);
                    formData.append('title', 'Link');
                    title = 'Link';
                } else if (interactionType === 'internal_link') {
                    actionType = 'internal_page';
                    const targetUrl = $('#targetUrl').val();
                    if (!targetUrl) {
                        Swal.fire('Error', 'Please enter a page number', 'error');
                        return;
                    }
                    // Extract page number from URL or use direct number
                    const pageNum = parseInt(targetUrl) || 1;
                    formData.append('target_page_number', pageNum);
                    formData.append('title', 'Internal Link');
                    title = 'Internal Link';
                } else if (interactionType === 'popup_video') {
                    actionType = 'popup_video';
                    const videoUrl = $('#videoUrl').val();
                    if (!videoUrl) {
                        Swal.fire('Error', 'Please enter a video URL', 'error');
                        return;
                    }
                    formData.append('popup_media_url', videoUrl);
                    formData.append('popup_type', 'video');
                    formData.append('title', 'Video');
                    title = 'Video';
                } else if (interactionType === 'popup_image') {
                    actionType = 'popup_image';
                    const popupImageFile = $('#popupImage')[0].files[0];
                    if (!popupImageFile) {
                        Swal.fire('Error', 'Please select an image', 'error');
                        return;
                    }
                    formData.append('popup_image', popupImageFile);
                    formData.append('popup_type', 'image');
                    formData.append('title', 'Image');
                    title = 'Image';
                }

                formData.append('action_type', actionType);

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
                        resetForm();
                    },
                    error: function(xhr) {
                        console.error('Error creating hotspot:', xhr.responseJSON);
                        const message = xhr.responseJSON?.message || 'Failed to create hotspot';
                        const errors = xhr.responseJSON?.errors;
                        let errorDetails = message;
                        if (errors) {
                            errorDetails += '<br><br><small>' + Object.values(errors).flat().join('<br>') +
                                '</small>';
                        }
                        Swal.fire({
                            title: 'Error!',
                            html: errorDetails,
                            icon: 'error'
                        });
                    }
                });
            }

            function resetForm() {
                $('#productName').val('');
                $('#productDesc').val('');
                $('#productPrice').val('');
                $('#actionUrl').val('');
                $('#thumbnailImage').val('');
                $('#targetUrl').val('');
                $('#videoUrl').val('');
                $('#popupImage').val('');
                $('#openNewTab').prop('checked', true);
            }

            function loadHotspots() {
                $.get(`/flipbooks/${flipbookId}/hotspots-all`, function(data) {
                    hotspots = data.filter(h => h.page_number == currentPage);
                    renderHotspots();
                    updateHotspotList();
                }).fail(function() {
                    console.error('Failed to load hotspots');
                });
            }

            function renderHotspots() {
                const layer = $('#hotspotsLayer');
                layer.empty();

                console.log(`Rendering ${hotspots.length} hotspots for page ${currentPage}`);

                hotspots.forEach(hotspot => {
                    const hotspotColor = hotspot.color || '#3b82f6';
                    const hotspotTitle = hotspot.title || hotspot.product_name || 'Hotspot';

                    const div = $('<div>')
                        .addClass('hotspot-overlay')
                        .attr('data-id', hotspot.id)
                        .attr('title', hotspotTitle + ' (Click to edit, drag to move, resize from corners)')
                        .css({
                            position: 'absolute',
                            left: hotspot.x_position + '%',
                            top: hotspot.y_position + '%',
                            width: hotspot.width + '%',
                            height: hotspot.height + '%',
                            border: '3px solid ' + hotspotColor,
                            background: 'rgba(59, 130, 246, 0.15)',
                            cursor: 'move',
                            boxSizing: 'border-box',
                            pointerEvents: 'auto',
                            transition: 'all 0.2s',
                            zIndex: 100
                        });

                    // Add title label
                    const label = $('<div>')
                        .css({
                            position: 'absolute',
                            top: '-28px',
                            left: '0',
                            background: hotspotColor,
                            color: 'white',
                            padding: '4px 10px',
                            borderRadius: '4px',
                            fontSize: '11px',
                            fontWeight: '600',
                            whiteSpace: 'nowrap',
                            pointerEvents: 'none',
                            boxShadow: '0 2px 4px rgba(0,0,0,0.2)'
                        })
                        .text(hotspotTitle);

                    // Add action icon
                    let iconClass = 'fa-link';
                    if (hotspot.type === 'product' || hotspot.action_type === 'product') iconClass = 'fa-shopping-cart';
                    else if (hotspot.type === 'popup_image' || hotspot.action_type === 'popup_image') iconClass =
                        'fa-image';
                    else if (hotspot.type === 'popup_video' || hotspot.action_type === 'popup_video') iconClass =
                        'fa-video';
                    else if (hotspot.type === 'internal_page' || hotspot.action_type === 'internal_page') iconClass =
                        'fa-file-alt';

                    const icon = $('<i>')
                        .addClass(`fas ${iconClass}`)
                        .css({
                            position: 'absolute',
                            top: '50%',
                            left: '50%',
                            transform: 'translate(-50%, -50%)',
                            fontSize: '24px',
                            color: hotspotColor,
                            pointerEvents: 'none',
                            opacity: '0.7'
                        });

                    div.append(label).append(icon);

                    // Hover effect
                    div.on('mouseenter', function() {
                        $(this).css({
                            background: 'rgba(59, 130, 246, 0.25)',
                            borderWidth: '4px',
                            boxShadow: '0 4px 12px rgba(59, 130, 246, 0.4)'
                        });
                    }).on('mouseleave', function() {
                        if (!$(this).hasClass('selected')) {
                            $(this).css({
                                background: 'rgba(59, 130, 246, 0.15)',
                                borderWidth: '3px',
                                boxShadow: 'none'
                            });
                        }
                    });

                    // Make draggable and resizable
                    div.draggable({
                        containment: 'parent',
                        start: function() {
                            $(this).css('cursor', 'grabbing');
                        },
                        stop: function(event, ui) {
                            $(this).css('cursor', 'move');
                            updateHotspotPosition(hotspot.id, ui.position);
                        }
                    }).resizable({
                        containment: 'parent',
                        handles: 'all',
                        stop: function(event, ui) {
                            updateHotspotSize(hotspot.id, ui.size, ui.position);
                        }
                    });

                    // Click to select/edit
                    div.on('click', function(e) {
                        if (!drawMode && !$(e.target).hasClass('ui-resizable-handle')) {
                            e.stopPropagation();
                            selectHotspot(hotspot, $(this));
                        }
                    });

                    layer.append(div);
                });
            }

            function selectHotspot(hotspot, element) {
                selectedHotspot = hotspot;

                // Remove previous selection
                $('.hotspot-overlay').removeClass('selected').css({
                    borderColor: function() {
                        return $(this).data('original-color') || '#3b82f6';
                    },
                    borderWidth: '3px',
                    background: 'rgba(59, 130, 246, 0.15)',
                    boxShadow: 'none'
                });

                // Highlight selected
                element.addClass('selected');
                element.data('original-color', hotspot.color || '#3b82f6');
                element.css({
                    borderColor: '#ff0000',
                    borderWidth: '4px',
                    background: 'rgba(255, 0, 0, 0.1)',
                    boxShadow: '0 0 15px rgba(255, 0, 0, 0.5)'
                });

                // Show edit info
                const actionType = hotspot.type || hotspot.action_type || 'unknown';
                const actionLabel = {
                    'product': 'Product Hotspot',
                    'link': 'External Link',
                    'internal_page': 'Internal Page Link',
                    'popup_image': 'Image Popup',
                    'popup_video': 'Video Popup'
                } [actionType] || 'Hotspot';

                Swal.fire({
                    title: 'Hotspot Selected',
                    html: `
                        <div class="text-start">
                            <p><strong>Title:</strong> ${hotspot.title || hotspot.product_name || 'Untitled'}</p>
                            <p><strong>Type:</strong> ${actionLabel}</p>
                            ${hotspot.description ? `<p><strong>Description:</strong> ${hotspot.description}</p>` : ''}
                            <hr>
                            <p class="text-muted small"><i class="fas fa-info-circle"></i> You can drag to move or resize this hotspot. Click the delete button in the hotspot list to remove it.</p>
                        </div>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-trash"></i> Delete Hotspot',
                    cancelButtonText: 'Close',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteHotspot(hotspot.id);
                    }
                });
            }

            function updateHotspotPosition(id, position) {
                const canvas = $('#pdfCanvas')[0];
                const xPercent = (position.left / canvas.width) * 100;
                const yPercent = (position.top / canvas.height) * 100;

                $.ajax({
                    url: `/customer/hotspots/${flipbookId}/${id}`,
                    type: 'PUT',
                    data: {
                        x_position: xPercent,
                        y_position: yPercent,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        // Position updated
                    }
                });
            }

            function updateHotspotSize(id, size, position) {
                const canvas = $('#pdfCanvas')[0];
                const xPercent = (position.left / canvas.width) * 100;
                const yPercent = (position.top / canvas.height) * 100;
                const widthPercent = (size.width / canvas.width) * 100;
                const heightPercent = (size.height / canvas.height) * 100;

                $.ajax({
                    url: `/customer/hotspots/${flipbookId}/${id}`,
                    type: 'PUT',
                    data: {
                        x_position: xPercent,
                        y_position: yPercent,
                        width: widthPercent,
                        height: heightPercent,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        // Size updated
                    }
                });
            }

            function updateHotspotList() {
                const list = $('#hotspotList');
                list.empty();
                $('#hotspotCount').text(hotspots.length);

                if (hotspots.length === 0) {
                    list.append('<div class="text-muted text-center py-5">No hotspots on this page</div>');
                    return;
                }

                hotspots.forEach((hotspot, index) => {
                    const item = $(`
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                    <div class="flex-grow-1">
                        <div class="fw-bold">${hotspot.title || hotspot.product_name || 'Hotspot ' + (index + 1)}</div>
                        <div class="text-muted fs-7">${formatInteractionType(hotspot.type)}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-icon btn-light" onclick="editHotspot(${hotspot.id})" title="Edit">
                            <i class="ki-duotone ki-pencil fs-5"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-light-danger" onclick="deleteHotspot(${hotspot.id})" title="Delete">
                            <i class="ki-duotone ki-trash fs-5"></i>
                        </button>
                    </div>
                </div>
            `);
                    list.append(item);
                });
            }

            function formatInteractionType(type) {
                const typeMap = {
                    'link': 'External Link',
                    'internal': 'Internal Link',
                    'product': 'Product',
                    'video': 'Video',
                    'popup': 'Image Popup'
                };
                return typeMap[type] || type;
            }

            function editHotspot(id) {
                // Find the hotspot
                const hotspot = hotspots.find(h => h.id === id);
                if (!hotspot) return;

                // TODO: Open edit modal or populate form
                Swal.fire({
                    title: 'Edit Hotspot',
                    html: '<div class="text-start">Drag and resize the hotspot on the canvas to update its position and size.</div>',
                    icon: 'info'
                });
            }

            function deleteHotspot(id) {
                Swal.fire({
                    title: 'Delete Hotspot?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/customer/hotspots/${flipbookId}/${id}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function() {
                                Swal.fire('Deleted!', 'Hotspot removed successfully', 'success');
                                loadHotspots();
                            },
                            error: function() {
                                Swal.fire('Error!', 'Failed to delete hotspot', 'error');
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
                Swal.fire({
                    title: 'Publish FlipBook?',
                    text: 'This will make your flipbook live and visible to viewers.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, publish it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(`/customer/catalog/${flipbookId}/publish`, {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }).done(function(response) {
                            Swal.fire('Published!', response.message, 'success').then(() => {
                                window.location.href = '{{ route('customer.catalog.index') }}';
                            });
                        }).fail(function() {
                            Swal.fire('Error!', 'Failed to publish flipbook', 'error');
                        });
                    }
                });
            }

            // Change interaction type handler
            $('#interactionType').on('change', function() {
                const type = $(this).val();
                $('#productFields, #linkFields, #videoFields, #imageFields').hide();

                if (type === 'popup_product') {
                    $('#productFields').show();
                } else if (type === 'external_link' || type === 'internal_link') {
                    $('#linkFields').show();
                } else if (type === 'popup_video') {
                    $('#videoFields').show();
                } else if (type === 'popup_image') {
                    $('#imageFields').show();
                }
            });

            // Initialize
            $(document).ready(function() {
                loadPDF();
                $('#interactionType').trigger('change');
            });
        </script>

        <style>
            .page-canvas-wrapper {
                position: relative;
                display: inline-block;
                background: #ffffff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            .canvas-container {
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                border: 1px solid #ddd;
                border-radius: 4px;
            }

            .hotspot-overlay {
                transition: all 0.2s;
                pointer-events: all !important;
                box-sizing: border-box;
            }

            .hotspot-overlay:hover {
                border-width: 3px !important;
                box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
            }

            .ui-resizable-handle {
                width: 12px;
                height: 12px;
                background: #fff;
                border: 2px solid #3b82f6;
                position: absolute;
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .ui-resizable-se {
                bottom: -6px;
                right: -6px;
                cursor: se-resize;
            }

            .ui-resizable-sw {
                bottom: -6px;
                left: -6px;
                cursor: sw-resize;
            }

            .ui-resizable-ne {
                top: -6px;
                right: -6px;
                cursor: ne-resize;
            }

            .ui-resizable-nw {
                top: -6px;
                left: -6px;
                cursor: nw-resize;
            }

            #drawingOverlay {
                animation: pulse 2s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 0.8;
                }

                50% {
                    opacity: 1;
                }
            }

            .hotspot-item {
                cursor: pointer;
                transition: all 0.3s;
            }

            .hotspot-item:hover {
                transform: translateX(5px);
            }
        </style>
    @endpush
@endsection
