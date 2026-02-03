@extends('layout.master')

@section('content')
    <style>
        body {
            overflow: hidden;
        }

        #editor-container {
            display: flex;
            height: calc(100vh - 140px);
            background: #f5f8fa;
        }

        .editor-sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid #e4e6ef;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #e4e6ef;
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }

        .page-thumbnail {
            position: relative;
            margin-bottom: 15px;
            cursor: pointer;
            border: 3px solid transparent;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.2s;
        }

        .page-thumbnail:hover {
            border-color: #3699FF;
            transform: scale(1.02);
        }

        .page-thumbnail.active {
            border-color: #3699FF;
            box-shadow: 0 0 20px rgba(54, 153, 255, 0.3);
        }

        .page-thumbnail canvas {
            width: 100%;
            display: block;
        }

        .page-number {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .hotspot-count {
            position: absolute;
            top: 5px;
            left: 5px;
            background: #50cd89;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .editor-canvas {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            overflow: auto;
        }

        .canvas-wrapper {
            position: relative;
            background: white;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        #pdf-canvas {
            display: block;
        }

        #hotspots-layer {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        #draw-preview {
            position: absolute;
            border: 3px dashed #3699FF;
            background: rgba(54, 153, 255, 0.1);
            pointer-events: none;
            display: none;
        }

        .hotspot-overlay {
            position: absolute;
            border: 3px solid #50cd89;
            background: rgba(80, 205, 137, 0.2);
            cursor: move;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: auto;
            transition: all 0.2s;
        }

        .hotspot-overlay:hover {
            background: rgba(80, 205, 137, 0.3);
            border-color: #3e9b6f;
            transform: scale(1.05);
        }

        .hotspot-overlay.selected {
            border-color: #3699FF;
            background: rgba(54, 153, 255, 0.3);
            z-index: 10;
            box-shadow: 0 0 20px rgba(54, 153, 255, 0.5);
        }

        .hotspot-label {
            position: absolute;
            top: -30px;
            left: 0;
            background: #50cd89;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            font-weight: 600;
        }

        .hotspot-delete {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 24px;
            height: 24px;
            background: #f1416c;
            color: white;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }

        .hotspot-overlay:hover .hotspot-delete {
            display: flex;
        }

        .editor-properties {
            width: 350px;
            background: white;
            border-left: 1px solid #e4e6ef;
            display: flex;
            flex-direction: column;
        }

        .properties-header {
            padding: 20px;
            border-bottom: 1px solid #e4e6ef;
        }

        .properties-content {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .editor-toolbar {
            background: white;
            padding: 10px 20px;
            border-bottom: 1px solid #e4e6ef;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .tool-btn {
            padding: 8px 16px;
            border: 1px solid #e4e6ef;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 13px;
        }

        .tool-btn:hover {
            background: #f5f8fa;
            border-color: #3699FF;
        }

        .tool-btn.active {
            background: #3699FF;
            color: white;
            border-color: #3699FF;
        }

        .zoom-controls {
            margin-left: auto;
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .instruction-box {
            background: #fff8dd;
            border: 2px solid #ffc700;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            max-width: 600px;
        }

        .instruction-box h6 {
            color: #7e5f00;
            margin-bottom: 10px;
        }

        .instruction-box ol {
            color: #7e5f00;
            margin: 0;
            padding-left: 20px;
        }
    </style>

    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-4">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 my-0">
                        <i class="fas fa-edit text-primary me-2"></i> {{ $flipbook->title }}
                    </h1>
                    <span class="text-muted fs-7 fw-semibold mt-1">Hotspot Editor</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('flipbooks.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('flipbook.viewer', $flipbook->slug) }}" target="_blank"
                        class="btn btn-sm btn-success">
                        <i class="fas fa-eye"></i> Preview
                    </a>
                </div>
            </div>
        </div>

        <div class="editor-toolbar">
            <button class="tool-btn active" data-tool="select">
                <i class="fas fa-mouse-pointer"></i> Select
            </button>
            <button class="tool-btn" data-tool="product">
                <i class="fas fa-shopping-cart"></i> Add Product
            </button>
            <button class="tool-btn" data-tool="link">
                <i class="fas fa-link"></i> Add Link
            </button>
            <div class="zoom-controls">
                <span class="text-muted me-2">Zoom:</span>
                <button class="tool-btn" id="zoom-out"><i class="fas fa-minus"></i></button>
                <span class="text-muted mx-2" id="zoom-level">100%</span>
                <button class="tool-btn" id="zoom-in"><i class="fas fa-plus"></i></button>
            </div>
        </div>

        <div id="editor-container">
            <div class="editor-sidebar">
                <div class="sidebar-header">
                    <h5 class="mb-0">Pages (<span id="total-pages">0</span>)</h5>
                </div>
                <div class="sidebar-content" id="pages-list">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>

            <div class="editor-canvas">
                <div class="instruction-box">
                    <h6><i class="fas fa-info-circle"></i> How to Use</h6>
                    <ol>
                        <li>Click <strong>"Add Product"</strong> or <strong>"Add Link"</strong></li>
                        <li><strong>Drag</strong> on the PDF to create hotspot</li>
                        <li>Configure in <strong>Properties Panel</strong></li>
                        <li>Click <strong>"Save Changes"</strong></li>
                    </ol>
                </div>
                <div class="canvas-wrapper" id="canvas-wrapper" style="display:none;">
                    <canvas id="pdf-canvas"></canvas>
                    <div id="hotspots-layer"></div>
                    <div id="draw-preview"></div>
                </div>
            </div>

            <div class="editor-properties">
                <div class="properties-header">
                    <h5 class="mb-0">Properties</h5>
                </div>
                <div class="properties-content" id="properties-panel">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-mouse-pointer fa-3x mb-3"></i>
                        <p>Select or create a hotspot</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        const flipbookId = {{ $flipbook->id }};
        const pdfUrl = '{{ asset('storage/' . $flipbook->pdf_path) }}';
        let pdfDoc = null;
        let currentPage = 1;
        let currentScale = 1.5;
        let currentTool = 'select';
        let hotspots = [];
        let selectedHotspot = null;
        let isDrawing = false;
        let drawStart = null;

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        $(document).ready(function() {
            loadPDF();
            loadHotspots();
            bindEvents();
        });

        function loadPDF() {
            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                pdfDoc = pdf;
                $('#total-pages').text(pdf.numPages);
                renderPageThumbnails();
                renderPage(1);
            }).catch(function(error) {
                Swal.fire('Error', 'Failed to load PDF', 'error');
            });
        }

        function renderPageThumbnails() {
            const container = $('#pages-list');
            container.empty();
            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const pageEl = $(`
                    <div class="page-thumbnail ${i === 1 ? 'active' : ''}" data-page="${i}">
                        <canvas id="thumb-${i}"></canvas>
                        <div class="page-number">Page ${i}</div>
                        <div class="hotspot-count" style="display:none;">0</div>
                    </div>
                `);
                container.append(pageEl);
                pdfDoc.getPage(i).then(function(page) {
                    const canvas = document.getElementById(`thumb-${page.pageNumber}`);
                    const context = canvas.getContext('2d');
                    const viewport = page.getViewport({
                        scale: 0.3
                    });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    page.render({
                        canvasContext: context,
                        viewport: viewport
                    });
                });
            }
            updateHotspotCounts();
        }

        function renderPage(pageNum) {
            currentPage = pageNum;
            $('.page-thumbnail').removeClass('active');
            $(`.page-thumbnail[data-page="${pageNum}"]`).addClass('active');
            pdfDoc.getPage(pageNum).then(function(page) {
                const canvas = document.getElementById('pdf-canvas');
                const context = canvas.getContext('2d');
                const viewport = page.getViewport({
                    scale: currentScale
                });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                $('#canvas-wrapper').show();
                page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise.then(() => renderHotspots());
            });
        }

        function loadHotspots() {
            $.get(`/flipbooks/${flipbookId}/hotspots-all`, function(data) {
                hotspots = data;
                renderHotspots();
                updateHotspotCounts();
            });
        }

        function renderHotspots() {
            $('#hotspots-layer').empty();
            const canvas = document.getElementById('pdf-canvas');
            if (!canvas) {
                console.log('Canvas not found, retrying...');
                setTimeout(renderHotspots, 100);
                return;
            }

            const pageHotspots = hotspots.filter(h => h.page_number === currentPage);
            console.log(`Rendering ${pageHotspots.length} hotspots for page ${currentPage}`, pageHotspots);

            pageHotspots.forEach(hotspot => {
                const x = (hotspot.x_position / 100) * canvas.width;
                const y = (hotspot.y_position / 100) * canvas.height;
                const w = (hotspot.width / 100) * canvas.width;
                const h = (hotspot.height / 100) * canvas.height;
                const icon = hotspot.type === 'product' ? 'shopping-cart' : 'link';
                const displayTitle = hotspot.product_name || hotspot.title || hotspot.type;
                const hotspotEl = $(`
                    <div class="hotspot-overlay" data-id="${hotspot.id}" 
                         style="left:${x}px; top:${y}px; width:${w}px; height:${h}px;">
                        <div class="hotspot-label">${displayTitle}</div>
                        <i class="fas fa-${icon} text-white"></i>
                        <div class="hotspot-delete"><i class="fas fa-times"></i></div>
                    </div>
                `);
                $('#hotspots-layer').append(hotspotEl);
            });

            $('#hotspots-layer').css({
                position: 'absolute',
                top: 0,
                left: 0,
                width: canvas.width + 'px',
                height: canvas.height + 'px',
                pointerEvents: currentTool === 'select' ? 'auto' : 'none',
                zIndex: 10
            });
        }

        function updateHotspotCounts() {
            for (let i = 1; i <= (pdfDoc?.numPages || 0); i++) {
                const count = hotspots.filter(h => h.page_number === i).length;
                const badge = $(`.page-thumbnail[data-page="${i}"] .hotspot-count`);
                if (count > 0) {
                    badge.text(count).show();
                } else {
                    badge.hide();
                }
            }
        }

        function bindEvents() {
            $('.tool-btn[data-tool]').click(function() {
                currentTool = $(this).data('tool');
                $('.tool-btn[data-tool]').removeClass('active');
                $(this).addClass('active');
                $('#pdf-canvas').css('cursor', currentTool === 'select' ? 'default' : 'crosshair');
                $('#hotspots-layer').css('pointerEvents', currentTool === 'select' ? 'auto' : 'none');
            });

            $('#zoom-in').click(() => {
                currentScale = Math.min(3, currentScale + 0.25);
                renderPage(currentPage);
                $('#zoom-level').text(Math.round((currentScale / 1.5) * 100) + '%');
            });

            $('#zoom-out').click(() => {
                currentScale = Math.max(0.5, currentScale - 0.25);
                renderPage(currentPage);
                $('#zoom-level').text(Math.round((currentScale / 1.5) * 100) + '%');
            });

            $(document).on('click', '.page-thumbnail', function() {
                renderPage($(this).data('page'));
            });

            $('#pdf-canvas').on('mousedown', function(e) {
                if (currentTool === 'select') return;
                isDrawing = true;
                const rect = this.getBoundingClientRect();
                drawStart = {
                    x: e.clientX - rect.left,
                    y: e.clientY - rect.top
                };
            });

            $('#pdf-canvas').on('mousemove', function(e) {
                if (!isDrawing) return;
                const rect = this.getBoundingClientRect();
                const x = Math.min(drawStart.x, e.clientX - rect.left);
                const y = Math.min(drawStart.y, e.clientY - rect.top);
                const w = Math.abs((e.clientX - rect.left) - drawStart.x);
                const h = Math.abs((e.clientY - rect.top) - drawStart.y);
                $('#draw-preview').css({
                    left: x,
                    top: y,
                    width: w,
                    height: h,
                    display: 'block'
                });
            });

            $('#pdf-canvas').on('mouseup', function(e) {
                if (!isDrawing) return;
                isDrawing = false;
                $('#draw-preview').hide();
                const rect = this.getBoundingClientRect();
                createHotspot(drawStart, {
                    x: e.clientX - rect.left,
                    y: e.clientY - rect.top
                });
            });

            $(document).on('click', '.hotspot-overlay', function(e) {
                e.stopPropagation();
                if (currentTool !== 'select') return;
                selectHotspot($(this).data('id'));
            });

            $(document).on('click', '.hotspot-delete', function(e) {
                e.stopPropagation();
                deleteHotspot($(this).parent().data('id'));
            });
        }

        function createHotspot(start, end) {
            const canvas = document.getElementById('pdf-canvas');
            const x = Math.min(start.x, end.x);
            const y = Math.min(start.y, end.y);
            const w = Math.abs(end.x - start.x);
            const h = Math.abs(end.y - start.y);

            if (w < 20 || h < 20) {
                Swal.fire('Too Small', 'Draw a larger area', 'warning');
                return;
            }

            const data = {
                flipbook_id: flipbookId,
                page_number: currentPage,
                type: currentTool,
                title: currentTool === 'product' ? 'New Product' : 'New Link',
                x_position: parseFloat(((x / canvas.width) * 100).toFixed(2)),
                y_position: parseFloat(((y / canvas.height) * 100).toFixed(2)),
                width: parseFloat(((w / canvas.width) * 100).toFixed(2)),
                height: parseFloat(((h / canvas.height) * 100).toFixed(2)),
                color: '#50cd89',
                animation: 'pulse',
                is_active: 1
            };

            $.ajax({
                url: `/flipbooks/${flipbookId}/hotspots`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(data),
                success: function(hotspot) {
                    hotspots.push(hotspot);
                    renderHotspots();
                    updateHotspotCounts();
                    selectHotspot(hotspot.id);
                    Swal.fire({
                        icon: 'success',
                        title: 'Created!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Failed', 'error');
                }
            });
        }

        function selectHotspot(id) {
            selectedHotspot = hotspots.find(h => h.id === id);
            $('.hotspot-overlay').removeClass('selected');
            $(`.hotspot-overlay[data-id="${id}"]`).addClass('selected');
            showProperties(selectedHotspot);
        }

        function deleteHotspot(id) {
            Swal.fire({
                title: 'Delete?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f1416c',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/flipbooks/${flipbookId}/hotspots/${id}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            hotspots = hotspots.filter(h => h.id !== id);
                            renderHotspots();
                            updateHotspotCounts();
                            $('#properties-panel').html(
                                '<div class="text-center text-muted py-5"><p>Select a hotspot</p></div>'
                            );
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        }

        function showProperties(hotspot) {
            const productsHtml = hotspot.type === 'product' ? `
                <div class="mb-3">
                    <label class="form-label fw-bold">Product Name *</label>
                    <input type="text" class="form-control" id="product_name" value="${hotspot.product_name || hotspot.title || ''}" placeholder="Enter product name">
                </div>
            ` : `
                <div class="mb-3">
                    <label class="form-label fw-bold">URL *</label>
                    <input type="url" class="form-control" id="target_url" value="${hotspot.target_url || ''}" placeholder="https://example.com">
                </div>
            `;

            $('#properties-panel').html(`
                <div class="mb-3">
                    <label class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" value="${hotspot.title || ''}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <textarea class="form-control" id="description" rows="3">${hotspot.description || ''}</textarea>
                </div>
                ${productsHtml}
                <div class="mb-3">
                    <label class="form-label fw-bold">Color</label>
                    <input type="color" class="form-control" id="color" value="${hotspot.color || '#50cd89'}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Animation</label>
                    <select class="form-select" id="animation">
                        <option value="pulse" ${hotspot.animation === 'pulse' ? 'selected' : ''}>Pulse</option>
                        <option value="bounce" ${hotspot.animation === 'bounce' ? 'selected' : ''}>Bounce</option>
                        <option value="none" ${hotspot.animation === 'none' ? 'selected' : ''}>None</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="is_active" ${hotspot.is_active ? 'checked' : ''}>
                    <label class="form-check-label">Active</label>
                </div>
                <button class="btn btn-primary w-100" id="save-hotspot">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            `);

            $('#save-hotspot').click(() => updateHotspot());
        }

        function updateHotspot() {
            const data = {
                title: $('#title').val(),
                description: $('#description').val(),
                color: $('#color').val(),
                animation: $('#animation').val(),
                is_active: $('#is_active').is(':checked') ? 1 : 0
            };

            if (selectedHotspot.type === 'product') {
                data.product_name = $('#product_name').val();
                if (!data.product_name) {
                    Swal.fire('Error', 'Enter product name', 'warning');
                    return;
                }
            } else {
                data.target_url = $('#target_url').val();
                if (!data.target_url) {
                    Swal.fire('Error', 'Enter URL', 'warning');
                    return;
                }
            }

            $.ajax({
                url: `/flipbooks/${flipbookId}/hotspots/${selectedHotspot.id}`,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(data),
                success: function(updatedHotspot) {
                    const index = hotspots.findIndex(h => h.id === selectedHotspot.id);
                    hotspots[index] = updatedHotspot;
                    renderHotspots();
                    selectHotspot(updatedHotspot.id);
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>
@endsection
