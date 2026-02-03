@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Hotspot Editor (Slicer) - {{ $flipbook->title }}
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
                        <li class="breadcrumb-item text-muted">Hotspot Editor</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('customer.catalog.show', $flipbook) }}" class="btn btn-sm btn-light-primary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">

                <div class="row g-5">
                    <!-- Left Toolbar -->
                    <div class="col-lg-2">
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h3 class="card-title">Drawing Tools</h3>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-light tool-btn active" data-tool="select">
                                        <i class="bi bi-cursor"></i> Select
                                    </button>
                                    <button class="btn btn-light tool-btn" data-tool="rectangle">
                                        <i class="bi bi-square"></i> Rectangle
                                    </button>
                                    <button class="btn btn-light tool-btn" data-tool="polygon">
                                        <i class="bi bi-pentagon"></i> Polygon
                                    </button>
                                    <button class="btn btn-light tool-btn" data-tool="freeform">
                                        <i class="bi bi-pen"></i> Freeform
                                    </button>
                                    <hr>
                                    <button class="btn btn-light-danger" id="deleteSelectedBtn" disabled>
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                    <button class="btn btn-light-warning" id="clearAllBtn">
                                        <i class="bi bi-x-circle"></i> Clear All
                                    </button>
                                </div>

                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">Page</label>
                                    <select class="form-select form-select-sm" id="pageSelector">
                                        @foreach ($pages as $page)
                                            <option value="{{ $page->page_number }}">
                                                Page {{ $page->page_number }}
                                                {{ $page->custom_name ? '- ' . $page->custom_name : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Zoom</label>
                                    <select class="form-select form-select-sm" id="zoomSelector">
                                        <option value="0.5">50%</option>
                                        <option value="0.75">75%</option>
                                        <option value="1" selected>100%</option>
                                        <option value="1.25">125%</option>
                                        <option value="1.5">150%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Center Canvas -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Canvas - Page <span id="currentPageDisplay">1</span></h3>
                                <div class="card-toolbar">
                                    <span class="badge badge-light-info" id="toolStatus">Select Tool Active</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="canvasContainer"
                                    style="position: relative; overflow: auto; max-height: 700px; background: #f5f5f5;">
                                    <canvas id="drawingCanvas"
                                        style="display: block; margin: 0 auto; cursor: crosshair;"></canvas>
                                    <img id="pageImage" style="display: none;" />
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <span id="mouseCoords">X: 0, Y: 0</span>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-light" id="undoBtn" disabled>
                                            <i class="bi bi-arrow-counterclockwise"></i> Undo
                                        </button>
                                        <button class="btn btn-sm btn-light" id="redoBtn" disabled>
                                            <i class="bi bi-arrow-clockwise"></i> Redo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Hotspot List & Properties -->
                    <div class="col-lg-3">
                        <!-- Hotspot Properties -->
                        <div class="card mb-5" id="propertiesPanel" style="display: none;">
                            <div class="card-header">
                                <h3 class="card-title">Hotspot Properties</h3>
                            </div>
                            <div class="card-body">
                                <form id="hotspotForm">
                                    <input type="hidden" id="hotspotId">
                                    <input type="hidden" id="hotspotPageNumber">
                                    <input type="hidden" id="hotspotShapeType">
                                    <input type="hidden" id="hotspotCoordinates">

                                    <div class="mb-3">
                                        <label class="form-label">Action Type</label>
                                        <select class="form-select form-select-sm" id="actionType" required>
                                            <option value="">Select Action...</option>
                                            <option value="link">External Link</option>
                                            <option value="internal_page">Internal Page Link</option>
                                            <option value="popup_image">Popup Image</option>
                                            <option value="popup_video">Popup Video</option>
                                            <option value="product">Product</option>
                                        </select>
                                    </div>

                                    <!-- External Link Fields -->
                                    <div class="mb-3 action-fields" id="linkFields" style="display: none;">
                                        <label class="form-label">Target URL</label>
                                        <input type="url" class="form-control form-control-sm" id="targetUrl"
                                            placeholder="https://...">
                                    </div>

                                    <!-- Internal Page Fields -->
                                    <div class="mb-3 action-fields" id="internalPageFields" style="display: none;">
                                        <label class="form-label">Target Page</label>
                                        <select class="form-select form-select-sm" id="targetPageNumber">
                                            @foreach ($pages as $page)
                                                <option value="{{ $page->page_number }}">Page {{ $page->page_number }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Popup Image Fields -->
                                    <div class="mb-3 action-fields" id="popupImageFields" style="display: none;">
                                        <label class="form-label">Upload Image</label>
                                        <input type="file" class="form-control form-control-sm" id="popupImage"
                                            accept="image/*">
                                        <div id="imagePreview" class="mt-2" style="display: none;">
                                            <img id="previewImg" style="max-width: 100%; border-radius: 4px;">
                                        </div>
                                    </div>

                                    <!-- Popup Video Fields -->
                                    <div class="mb-3 action-fields" id="popupVideoFields" style="display: none;">
                                        <label class="form-label">Video URL</label>
                                        <input type="url" class="form-control form-control-sm" id="popupVideoUrl"
                                            placeholder="https://...">
                                        <small class="text-muted">YouTube, Vimeo, or direct video URL</small>
                                    </div>

                                    <!-- Product Fields -->
                                    <div class="action-fields" id="productFields" style="display: none;">
                                        <div class="mb-3">
                                            <label class="form-label">Product Name</label>
                                            <input type="text" class="form-control form-control-sm" id="productName"
                                                placeholder="Product name">
                                        </div>
                                    </div>

                                    <!-- Common Fields -->
                                    <div class="mb-3">
                                        <label class="form-label">Title (Optional)</label>
                                        <input type="text" class="form-control form-control-sm" id="hotspotTitle"
                                            placeholder="Hotspot title">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description (Optional)</label>
                                        <textarea class="form-control form-control-sm" id="hotspotDescription" rows="2" placeholder="Description"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Color</label>
                                        <input type="color" class="form-control form-control-color" id="hotspotColor"
                                            value="#3b82f6">
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="bi bi-save"></i> Save Hotspot
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light" id="cancelHotspotBtn">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Hotspots List -->
                        <div class="card sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h3 class="card-title">Hotspots</h3>
                                <div class="card-toolbar">
                                    <span class="badge badge-primary" id="hotspotCount">0</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="hotspotsList" class="list-group list-group-flush"
                                    style="max-height: 400px; overflow-y: auto;">
                                    <div class="list-group-item text-center text-muted">
                                        <i class="bi bi-inbox fs-3x mb-2"></i>
                                        <p class="mb-0">No hotspots yet</p>
                                        <small>Draw a shape to create one</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const flipbookId = {{ $flipbook->id }};
        const csrfToken = '{{ csrf_token() }}';
        const pagesData = @json($pages);

        let canvas, ctx, pageImage;
        let currentTool = 'select';
        let currentPage = 1;
        let isDrawing = false;
        let startX, startY;
        let hotspots = [];
        let selectedHotspot = null;
        let polygonPoints = [];
        let freeformPoints = [];
        let tempShape = null;
        let scale = 1;

        document.addEventListener('DOMContentLoaded', function() {
            canvas = document.getElementById('drawingCanvas');
            ctx = canvas.getContext('2d');
            pageImage = document.getElementById('pageImage');

            // Initialize
            loadPage(currentPage);
            loadHotspots();

            // Tool selection
            document.querySelectorAll('.tool-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.tool-btn').forEach(b => b.classList.remove(
                        'active'));
                    this.classList.add('active');
                    currentTool = this.getAttribute('data-tool');
                    document.getElementById('toolStatus').textContent = currentTool.charAt(0)
                        .toUpperCase() + currentTool.slice(1) + ' Tool Active';
                    canvas.style.cursor = currentTool === 'select' ? 'default' : 'crosshair';

                    // Reset drawing state
                    polygonPoints = [];
                    freeformPoints = [];
                    tempShape = null;
                    redrawCanvas();
                });
            });

            // Page selector
            document.getElementById('pageSelector').addEventListener('change', function() {
                currentPage = parseInt(this.value);
                loadPage(currentPage);
                loadHotspots();
            });

            // Zoom selector
            document.getElementById('zoomSelector').addEventListener('change', function() {
                scale = parseFloat(this.value);
                redrawCanvas();
            });

            // Canvas events
            canvas.addEventListener('mousedown', handleMouseDown);
            canvas.addEventListener('mousemove', handleMouseMove);
            canvas.addEventListener('mouseup', handleMouseUp);
            canvas.addEventListener('dblclick', handleDoubleClick);

            // Action type change
            document.getElementById('actionType').addEventListener('change', function() {
                document.querySelectorAll('.action-fields').forEach(el => el.style.display = 'none');
                const actionType = this.value;

                if (actionType === 'link') {
                    document.getElementById('linkFields').style.display = 'block';
                } else if (actionType === 'internal_page') {
                    document.getElementById('internalPageFields').style.display = 'block';
                } else if (actionType === 'popup_image') {
                    document.getElementById('popupImageFields').style.display = 'block';
                } else if (actionType === 'popup_video') {
                    document.getElementById('popupVideoFields').style.display = 'block';
                } else if (actionType === 'product') {
                    document.getElementById('productFields').style.display = 'block';
                }
            });

            // Image preview
            document.getElementById('popupImage').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('previewImg').src = e.target.result;
                        document.getElementById('imagePreview').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Hotspot form submit
            document.getElementById('hotspotForm').addEventListener('submit', function(e) {
                e.preventDefault();
                saveHotspot();
            });

            // Cancel hotspot
            document.getElementById('cancelHotspotBtn').addEventListener('click', function() {
                document.getElementById('propertiesPanel').style.display = 'none';
                tempShape = null;
                selectedHotspot = null;
                redrawCanvas();
            });

            // Delete selected
            document.getElementById('deleteSelectedBtn').addEventListener('click', function() {
                if (selectedHotspot) {
                    deleteHotspot(selectedHotspot.id);
                }
            });

            // Clear all
            document.getElementById('clearAllBtn').addEventListener('click', function() {
                Swal.fire({
                    title: 'Clear All Hotspots?',
                    text: "This will delete all hotspots on this page!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, clear all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const pageHotspots = hotspots.filter(h => h.page_number === currentPage);
                        if (pageHotspots.length > 0) {
                            const ids = pageHotspots.map(h => h.id);
                            bulkDeleteHotspots(ids);
                        }
                    }
                });
            });
        });

        function loadPage(pageNumber) {
            const page = pagesData.find(p => p.page_number === pageNumber);
            if (!page) return;

            document.getElementById('currentPageDisplay').textContent = pageNumber;

            // Load page image
            pageImage.onload = function() {
                canvas.width = pageImage.width;
                canvas.height = pageImage.height;
                redrawCanvas();
            };

            // Use the thumbnail or image path
            const imagePath = page.image_path ? `/storage/${page.image_path}` : page.thumbnail_path ?
                `/storage/${page.thumbnail_path}` : '';
            if (imagePath) {
                pageImage.src = imagePath;
            }
        }

        function loadHotspots() {
            fetch(`/customer/hotspots/${flipbookId}/data?page_number=${currentPage}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hotspots = data;
                    redrawCanvas();
                    updateHotspotsList();
                })
                .catch(error => console.error('Error loading hotspots:', error));
        }

        function redrawCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw page image
            if (pageImage.complete) {
                ctx.drawImage(pageImage, 0, 0, canvas.width, canvas.height);
            }

            // Draw hotspots
            hotspots.filter(h => h.page_number === currentPage).forEach(hotspot => {
                drawHotspot(hotspot, hotspot === selectedHotspot);
            });

            // Draw temp shape
            if (tempShape) {
                drawTempShape();
            }
        }

        function drawHotspot(hotspot, isSelected) {
            ctx.save();
            ctx.strokeStyle = isSelected ? '#ff0000' : (hotspot.color || '#3b82f6');
            ctx.lineWidth = isSelected ? 3 : 2;
            ctx.fillStyle = (hotspot.color || '#3b82f6') + '33';

            if (hotspot.shape_type === 'rectangle' || !hotspot.shape_type) {
                const x = (hotspot.x_position / 100) * canvas.width;
                const y = (hotspot.y_position / 100) * canvas.height;
                const w = (hotspot.width / 100) * canvas.width;
                const h = (hotspot.height / 100) * canvas.height;

                ctx.fillRect(x, y, w, h);
                ctx.strokeRect(x, y, w, h);
            } else if (hotspot.shape_type === 'polygon' && hotspot.coordinates) {
                ctx.beginPath();
                hotspot.coordinates.forEach((point, index) => {
                    const x = (point.x / 100) * canvas.width;
                    const y = (point.y / 100) * canvas.height;
                    if (index === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.closePath();
                ctx.fill();
                ctx.stroke();
            } else if (hotspot.shape_type === 'freeform' && hotspot.coordinates) {
                ctx.beginPath();
                hotspot.coordinates.forEach((point, index) => {
                    const x = (point.x / 100) * canvas.width;
                    const y = (point.y / 100) * canvas.height;
                    if (index === 0) ctx.moveTo(x, y);
                    else ctx.lineTo(x, y);
                });
                ctx.closePath();
                ctx.fill();
                ctx.stroke();
            }

            ctx.restore();
        }

        function drawTempShape() {
            ctx.save();
            ctx.strokeStyle = '#00ff00';
            ctx.lineWidth = 2;
            ctx.fillStyle = '#00ff0033';

            if (tempShape.type === 'rectangle') {
                ctx.fillRect(tempShape.x, tempShape.y, tempShape.width, tempShape.height);
                ctx.strokeRect(tempShape.x, tempShape.y, tempShape.width, tempShape.height);
            } else if (tempShape.type === 'polygon' && polygonPoints.length > 0) {
                ctx.beginPath();
                polygonPoints.forEach((point, index) => {
                    if (index === 0) ctx.moveTo(point.x, point.y);
                    else ctx.lineTo(point.x, point.y);
                });
                ctx.stroke();

                // Draw points
                polygonPoints.forEach(point => {
                    ctx.beginPath();
                    ctx.arc(point.x, point.y, 4, 0, Math.PI * 2);
                    ctx.fillStyle = '#00ff00';
                    ctx.fill();
                });
            } else if (tempShape.type === 'freeform' && freeformPoints.length > 0) {
                ctx.beginPath();
                freeformPoints.forEach((point, index) => {
                    if (index === 0) ctx.moveTo(point.x, point.y);
                    else ctx.lineTo(point.x, point.y);
                });
                ctx.stroke();
            }

            ctx.restore();
        }

        function handleMouseDown(e) {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            if (currentTool === 'select') {
                // Check if clicking on existing hotspot
                const clicked = hotspots.find(h => isPointInHotspot(x, y, h));
                if (clicked) {
                    selectedHotspot = clicked;
                    document.getElementById('deleteSelectedBtn').disabled = false;
                    redrawCanvas();
                }
            } else if (currentTool === 'rectangle') {
                isDrawing = true;
                startX = x;
                startY = y;
                tempShape = {
                    type: 'rectangle',
                    x,
                    y,
                    width: 0,
                    height: 0
                };
            } else if (currentTool === 'polygon') {
                polygonPoints.push({
                    x,
                    y
                });
                tempShape = {
                    type: 'polygon'
                };
                redrawCanvas();
            } else if (currentTool === 'freeform') {
                isDrawing = true;
                freeformPoints = [{
                    x,
                    y
                }];
                tempShape = {
                    type: 'freeform'
                };
            }
        }

        function handleMouseMove(e) {
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            // Update coordinates display
            document.getElementById('mouseCoords').textContent = `X: ${Math.round(x)}, Y: ${Math.round(y)}`;

            if (currentTool === 'rectangle' && isDrawing) {
                tempShape.width = x - startX;
                tempShape.height = y - startY;
                redrawCanvas();
            } else if (currentTool === 'freeform' && isDrawing) {
                freeformPoints.push({
                    x,
                    y
                });
                redrawCanvas();
            }
        }

        function handleMouseUp(e) {
            if (currentTool === 'rectangle' && isDrawing) {
                isDrawing = false;
                if (Math.abs(tempShape.width) > 10 && Math.abs(tempShape.height) > 10) {
                    showHotspotProperties();
                } else {
                    tempShape = null;
                    redrawCanvas();
                }
            } else if (currentTool === 'freeform' && isDrawing) {
                isDrawing = false;
                if (freeformPoints.length > 10) {
                    showHotspotProperties();
                } else {
                    tempShape = null;
                    freeformPoints = [];
                    redrawCanvas();
                }
            }
        }

        function handleDoubleClick(e) {
            if (currentTool === 'polygon' && polygonPoints.length >= 3) {
                showHotspotProperties();
            }
        }

        function isPointInHotspot(x, y, hotspot) {
            if (hotspot.shape_type === 'rectangle' || !hotspot.shape_type) {
                const hx = (hotspot.x_position / 100) * canvas.width;
                const hy = (hotspot.y_position / 100) * canvas.height;
                const hw = (hotspot.width / 100) * canvas.width;
                const hh = (hotspot.height / 100) * canvas.height;
                return x >= hx && x <= hx + hw && y >= hy && y <= hy + hh;
            }
            return false;
        }

        function showHotspotProperties() {
            document.getElementById('propertiesPanel').style.display = 'block';
            document.getElementById('hotspotPageNumber').value = currentPage;
            document.getElementById('hotspotShapeType').value = tempShape.type;

            if (tempShape.type === 'rectangle') {
                // Normalize rectangle (handle negative dimensions)
                const x = Math.min(tempShape.x, tempShape.x + tempShape.width);
                const y = Math.min(tempShape.y, tempShape.y + tempShape.height);
                const w = Math.abs(tempShape.width);
                const h = Math.abs(tempShape.height);

                document.getElementById('hotspotCoordinates').value = JSON.stringify({
                    x_position: (x / canvas.width) * 100,
                    y_position: (y / canvas.height) * 100,
                    width: (w / canvas.width) * 100,
                    height: (h / canvas.height) * 100
                });
            } else if (tempShape.type === 'polygon') {
                const coords = polygonPoints.map(p => ({
                    x: (p.x / canvas.width) * 100,
                    y: (p.y / canvas.height) * 100
                }));
                document.getElementById('hotspotCoordinates').value = JSON.stringify(coords);
            } else if (tempShape.type === 'freeform') {
                const coords = freeformPoints.map(p => ({
                    x: (p.x / canvas.width) * 100,
                    y: (p.y / canvas.height) * 100
                }));
                document.getElementById('hotspotCoordinates').value = JSON.stringify(coords);
            }

            // Scroll to properties panel
            document.getElementById('propertiesPanel').scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }

        function saveHotspot() {
            const actionType = document.getElementById('actionType').value;
            if (!actionType) {
                Swal.fire('Error', 'Please select an action type', 'error');
                return;
            }

            const shapeType = document.getElementById('hotspotShapeType').value;
            const coords = JSON.parse(document.getElementById('hotspotCoordinates').value);

            const formData = new FormData();
            formData.append('page_number', document.getElementById('hotspotPageNumber').value);
            formData.append('shape_type', shapeType);
            formData.append('action_type', actionType);
            formData.append('title', document.getElementById('hotspotTitle').value);
            formData.append('description', document.getElementById('hotspotDescription').value);
            formData.append('color', document.getElementById('hotspotColor').value);

            if (shapeType === 'rectangle') {
                formData.append('x_position', coords.x_position);
                formData.append('y_position', coords.y_position);
                formData.append('width', coords.width);
                formData.append('height', coords.height);
            } else {
                formData.append('coordinates', JSON.stringify(coords));
            }

            // Action-specific fields
            if (actionType === 'link') {
                formData.append('target_url', document.getElementById('targetUrl').value);
            } else if (actionType === 'internal_page') {
                formData.append('target_page_number', document.getElementById('targetPageNumber').value);
            } else if (actionType === 'popup_image') {
                const imageFile = document.getElementById('popupImage').files[0];
                if (imageFile) {
                    formData.append('popup_image', imageFile);
                }
            } else if (actionType === 'popup_video') {
                formData.append('popup_media_url', document.getElementById('popupVideoUrl').value);
                formData.append('popup_type', 'video');
            } else if (actionType === 'product') {
                formData.append('product_name', document.getElementById('productName').value);
            }

            fetch(`/customer/hotspots/${flipbookId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success!', data.message, 'success');
                        document.getElementById('propertiesPanel').style.display = 'none';
                        document.getElementById('hotspotForm').reset();
                        tempShape = null;
                        polygonPoints = [];
                        freeformPoints = [];
                        loadHotspots();
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Failed to save hotspot.', 'error');
                });
        }

        function deleteHotspot(hotspotId) {
            Swal.fire({
                title: 'Delete Hotspot?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/customer/hotspots/${flipbookId}/${hotspotId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Deleted!', data.message, 'success');
                                selectedHotspot = null;
                                document.getElementById('deleteSelectedBtn').disabled = true;
                                loadHotspots();
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Failed to delete hotspot.', 'error');
                        });
                }
            });
        }

        function bulkDeleteHotspots(ids) {
            fetch(`/customer/hotspots/${flipbookId}/bulk-delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        hotspot_ids: ids
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success!', data.message, 'success');
                        loadHotspots();
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Failed to delete hotspots.', 'error');
                });
        }

        function updateHotspotsList() {
            const pageHotspots = hotspots.filter(h => h.page_number === currentPage);
            const listContainer = document.getElementById('hotspotsList');
            document.getElementById('hotspotCount').textContent = pageHotspots.length;

            if (pageHotspots.length === 0) {
                listContainer.innerHTML = `
                <div class="list-group-item text-center text-muted">
                    <i class="bi bi-inbox fs-3x mb-2"></i>
                    <p class="mb-0">No hotspots yet</p>
                    <small>Draw a shape to create one</small>
                </div>
            `;
                return;
            }

            listContainer.innerHTML = pageHotspots.map(hotspot => `
            <div class="list-group-item list-group-item-action" data-hotspot-id="${hotspot.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${hotspot.title || 'Hotspot #' + hotspot.id}</h6>
                        <small class="text-muted">${hotspot.action_type || hotspot.type}</small>
                    </div>
                    <button class="btn btn-sm btn-light-danger delete-hotspot-btn" data-hotspot-id="${hotspot.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');

            // Add click events
            listContainer.querySelectorAll('.list-group-item-action').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (!e.target.closest('.delete-hotspot-btn')) {
                        const hotspotId = parseInt(this.getAttribute('data-hotspot-id'));
                        selectedHotspot = hotspots.find(h => h.id === hotspotId);
                        document.getElementById('deleteSelectedBtn').disabled = false;
                        redrawCanvas();
                    }
                });
            });

            listContainer.querySelectorAll('.delete-hotspot-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const hotspotId = parseInt(this.getAttribute('data-hotspot-id'));
                    deleteHotspot(hotspotId);
                });
            });
        }
    </script>
    <style>
        #drawingCanvas {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .tool-btn.active {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        .list-group-item-action {
            cursor: pointer;
        }

        .list-group-item-action:hover {
            background-color: #f8f9fa;
        }
    </style>
@endpush
