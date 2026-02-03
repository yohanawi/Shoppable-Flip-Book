@extends('layout.master')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.css" />
    <style>
        .page-canvas-wrapper {
            position: relative;
            display: inline-block;
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
        }

        .canvas-container {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .hotspot-item {
            cursor: pointer;
            transition: all 0.3s;
        }

        .hotspot-item:hover {
            background-color: #f8f9fa;
        }

        .hotspot-item.active {
            background-color: #e3f2fd;
            border-left: 3px solid #2196F3;
        }

        .page-thumbnail {
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .page-thumbnail:hover {
            border-color: #2196F3;
            transform: scale(1.05);
        }

        .page-thumbnail.active {
            border-color: #2196F3;
            box-shadow: 0 0 10px rgba(33, 150, 243, 0.5);
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                        Hotspot Editor - {{ $flipbook->title }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('flipbooks.index') }}" class="text-muted text-hover-primary">Flipbooks</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Editor</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('flipbooks.edit', $flipbook) }}" class="btn btn-sm btn-light">
                        <i class="ki-duotone ki-left fs-2"></i>Back
                    </a>
                    @if ($flipbook->is_published)
                        <a href="{{ $flipbook->getPublicUrl() }}" target="_blank" class="btn btn-sm btn-success">
                            <i class="ki-duotone ki-eye fs-2"></i>Preview
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">
                <div class="row g-5">
                    <!--begin::Left Sidebar - Pages-->
                    <div class="col-lg-2">
                        <div class="card card-flush h-lg-100">
                            <div class="card-header">
                                <h3 class="card-title">Pages</h3>
                            </div>
                            <div class="card-body p-3" id="pages-list">
                                @foreach ($flipbook->pages as $page)
                                    <div class="page-thumbnail mb-3 {{ $loop->first ? 'active' : '' }}"
                                        data-page-id="{{ $page->id }}" data-page-number="{{ $page->page_number }}"
                                        data-image-url="{{ $page->getImageUrl() }}" data-width="{{ $page->width }}"
                                        data-height="{{ $page->height }}">
                                        <img src="{{ $page->getThumbnailUrl() }}" class="img-fluid rounded"
                                            alt="Page {{ $page->page_number }}" />
                                        <div class="text-center mt-1">
                                            <small class="text-muted">Page {{ $page->page_number }}</small>
                                            <br>
                                            <span
                                                class="badge badge-sm badge-light-primary hotspot-count-{{ $page->id }}">
                                                {{ $page->hotspots->count() }} hotspots
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!--end::Left Sidebar-->

                    <!--begin::Center - Canvas-->
                    <div class="col-lg-7">
                        <div class="card card-flush">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Canvas - Page <span id="current-page-number">1</span>
                                </h3>
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-light-primary me-2" id="add-hotspot-btn">
                                        <i class="ki-duotone ki-plus fs-2"></i>Add Hotspot
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light-danger" id="delete-hotspot-btn"
                                        disabled>
                                        <i class="ki-duotone ki-trash fs-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                            <div class="card-body d-flex justify-content-center align-items-center"
                                style="min-height: 600px;">
                                <div class="page-canvas-wrapper">
                                    <canvas id="page-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Center-->

                    <!--begin::Right Sidebar - Properties-->
                    <div class="col-lg-3">
                        <div class="card card-flush sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h3 class="card-title">Hotspot Properties</h3>
                            </div>
                            <div class="card-body" id="hotspot-properties">
                                <div class="text-center py-10 text-muted" id="no-selection-message">
                                    <i class="ki-duotone ki-information-5 fs-5x mb-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <p>Select a hotspot to edit properties</p>
                                </div>

                                <form id="hotspot-form" style="display: none;">
                                    <input type="hidden" id="hotspot-id" />
                                    <input type="hidden" id="page-id" />

                                    <div class="mb-5">
                                        <label class="form-label">Type</label>
                                        <select class="form-select form-select-sm" id="hotspot-type" required>
                                            <option value="external">External Link</option>
                                            <option value="internal">Internal Link</option>
                                            <option value="product">Product</option>
                                            <option value="popup">Popup</option>
                                            <option value="video">Video</option>
                                        </select>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control form-control-sm" id="hotspot-title" />
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control form-control-sm" id="hotspot-description" rows="2"></textarea>
                                    </div>

                                    <div class="mb-5" id="target-url-field">
                                        <label class="form-label">Target URL</label>
                                        <input type="url" class="form-control form-control-sm"
                                            id="hotspot-target-url" />
                                    </div>

                                    <div class="mb-5" id="product-id-field" style="display: none;">
                                        <label class="form-label">Product ID</label>
                                        <input type="number" class="form-control form-control-sm"
                                            id="hotspot-product-id" />
                                    </div>

                                    <div class="mb-5" id="popup-content-field" style="display: none;">
                                        <label class="form-label">Popup Content (HTML)</label>
                                        <textarea class="form-control form-control-sm" id="hotspot-popup-content" rows="3"></textarea>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Target Behavior</label>
                                        <select class="form-select form-select-sm" id="hotspot-target-type">
                                            <option value="_blank">New Tab</option>
                                            <option value="_self">Same Tab</option>
                                            <option value="modal">Modal</option>
                                            <option value="cart">Add to Cart</option>
                                        </select>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Icon (Optional)</label>
                                        <input type="text" class="form-control form-control-sm" id="hotspot-icon"
                                            placeholder="e.g., ki-shopping-cart" />
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Color</label>
                                        <input type="color" class="form-control form-control-sm" id="hotspot-color"
                                            value="#3b82f6" />
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Animation</label>
                                        <select class="form-select form-select-sm" id="hotspot-animation">
                                            <option value="">None</option>
                                            <option value="pulse">Pulse</option>
                                            <option value="bounce">Bounce</option>
                                            <option value="shake">Shake</option>
                                        </select>
                                    </div>

                                    <div class="separator my-5"></div>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-primary flex-grow-1"
                                            id="save-hotspot-btn">
                                            <i class="ki-duotone ki-check fs-2"></i>Save
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light" id="cancel-hotspot-btn">
                                            Cancel
                                        </button>
                                    </div>
                                </form>

                                <!--begin::Hotspots List-->
                                <div class="separator my-5"></div>
                                <h4 class="fs-6 fw-bold mb-3">Hotspots on this page</h4>
                                <div id="hotspots-list"></div>
                                <!--end::Hotspots List-->
                            </div>
                        </div>
                    </div>
                    <!--end::Right Sidebar-->
                </div>
            </div>
        </div>
        <!--end::Content-->
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
    <script>
        let canvas;
        let currentPage = null;
        let currentHotspots = [];
        let isDrawingMode = false;
        let selectedHotspot = null;

        $(document).ready(function() {
            initializeCanvas();
            loadFirstPage();
            bindEvents();
        });

        function initializeCanvas() {
            canvas = new fabric.Canvas('page-canvas', {
                selection: true,
                backgroundColor: '#ffffff'
            });
        }

        function loadFirstPage() {
            const firstPage = $('.page-thumbnail').first();
            loadPage(firstPage);
        }

        function loadPage($pageElement) {
            const pageId = $pageElement.data('page-id');
            const pageNumber = $pageElement.data('page-number');
            const imageUrl = $pageElement.data('image-url');
            const width = $pageElement.data('width');
            const height = $pageElement.data('height');

            currentPage = {
                id: pageId,
                number: pageNumber,
                imageUrl: imageUrl,
                width: width,
                height: height
            };

            // Update UI
            $('.page-thumbnail').removeClass('active');
            $pageElement.addClass('active');
            $('#current-page-number').text(pageNumber);
            $('#page-id').val(pageId);

            // Load page image
            fabric.Image.fromURL(imageUrl, function(img) {
                canvas.clear();

                // Scale image to fit
                const maxWidth = 800;
                const scale = maxWidth / img.width;

                canvas.setWidth(img.width * scale);
                canvas.setHeight(img.height * scale);

                img.scale(scale);
                img.selectable = false;
                img.evented = false;

                canvas.add(img);
                canvas.sendToBack(img);

                loadHotspotsForPage(pageId);
            });
        }

        function loadHotspotsForPage(pageId) {
            $.get(`/api/flipbooks/pages/${pageId}/hotspots`, function(response) {
                currentHotspots = response.hotspots || [];
                renderHotspots();
                updateHotspotsList();
            });
        }

        function renderHotspots() {
            // Remove existing hotspot rectangles
            canvas.getObjects('rect').forEach(obj => {
                if (obj.hotspotId) {
                    canvas.remove(obj);
                }
            });

            // Add hotspots to canvas
            currentHotspots.forEach(hotspot => {
                addHotspotToCanvas(hotspot);
            });
        }

        function addHotspotToCanvas(hotspot) {
            const canvasWidth = canvas.getWidth();
            const canvasHeight = canvas.getHeight();

            const rect = new fabric.Rect({
                left: (hotspot.x_position / 100) * canvasWidth,
                top: (hotspot.y_position / 100) * canvasHeight,
                width: (hotspot.width / 100) * canvasWidth,
                height: (hotspot.height / 100) * canvasHeight,
                fill: hotspot.color || 'rgba(59, 130, 246, 0.3)',
                stroke: hotspot.color || '#3b82f6',
                strokeWidth: 2,
                hotspotId: hotspot.id,
                hotspotData: hotspot
            });

            canvas.add(rect);
        }

        function updateHotspotsList() {
            const $list = $('#hotspots-list');
            $list.empty();

            if (currentHotspots.length === 0) {
                $list.html('<p class="text-muted text-center">No hotspots yet</p>');
                return;
            }

            currentHotspots.forEach(hotspot => {
                const $item = $(`
            <div class="hotspot-item p-3 rounded mb-2 border" data-hotspot-id="${hotspot.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold">${hotspot.title || 'Untitled'}</div>
                        <small class="text-muted">${hotspot.type}</small>
                    </div>
                    <button class="btn btn-sm btn-light-danger delete-hotspot" data-hotspot-id="${hotspot.id}">
                        <i class="ki-duotone ki-trash fs-5"></i>
                    </button>
                </div>
            </div>
        `);
                $list.append($item);
            });
        }

        function bindEvents() {
            // Page selection
            $(document).on('click', '.page-thumbnail', function() {
                loadPage($(this));
                clearSelection();
            });

            // Add hotspot button
            $('#add-hotspot-btn').click(function() {
                isDrawingMode = true;
                $(this).addClass('active').text('Drawing... Click and drag on canvas');
                canvas.isDrawingMode = false;

                // Enable rectangle drawing
                let isDown, origX, origY, rect;

                canvas.on('mouse:down', function(o) {
                    if (!isDrawingMode) return;

                    isDown = true;
                    const pointer = canvas.getPointer(o.e);
                    origX = pointer.x;
                    origY = pointer.y;

                    rect = new fabric.Rect({
                        left: origX,
                        top: origY,
                        width: 0,
                        height: 0,
                        fill: 'rgba(59, 130, 246, 0.3)',
                        stroke: '#3b82f6',
                        strokeWidth: 2
                    });
                    canvas.add(rect);
                });

                canvas.on('mouse:move', function(o) {
                    if (!isDown || !isDrawingMode) return;

                    const pointer = canvas.getPointer(o.e);

                    if (origX > pointer.x) {
                        rect.set({
                            left: Math.abs(pointer.x)
                        });
                    }
                    if (origY > pointer.y) {
                        rect.set({
                            top: Math.abs(pointer.y)
                        });
                    }

                    rect.set({
                        width: Math.abs(origX - pointer.x)
                    });
                    rect.set({
                        height: Math.abs(origY - pointer.y)
                    });
                    canvas.renderAll();
                });

                canvas.on('mouse:up', function(o) {
                    if (!isDrawingMode) return;

                    isDown = false;
                    isDrawingMode = false;
                    $('#add-hotspot-btn').removeClass('active').html(
                        '<i class="ki-duotone ki-plus fs-2"></i>Add Hotspot');

                    // Convert to percentage
                    const canvasWidth = canvas.getWidth();
                    const canvasHeight = canvas.getHeight();

                    showHotspotForm({
                        x_position: (rect.left / canvasWidth) * 100,
                        y_position: (rect.top / canvasHeight) * 100,
                        width: (rect.width / canvasWidth) * 100,
                        height: (rect.height / canvasHeight) * 100
                    }, rect);

                    canvas.off('mouse:down');
                    canvas.off('mouse:move');
                    canvas.off('mouse:up');
                });
            });

            // Hotspot type change
            $('#hotspot-type').change(function() {
                const type = $(this).val();

                $('#target-url-field, #product-id-field, #popup-content-field').hide();

                if (type === 'external') {
                    $('#target-url-field').show();
                } else if (type === 'product') {
                    $('#product-id-field').show();
                } else if (type === 'popup') {
                    $('#popup-content-field').show();
                }
            });

            // Save hotspot
            $('#save-hotspot-btn').click(function() {
                saveHotspot();
            });

            // Cancel
            $('#cancel-hotspot-btn').click(function() {
                clearSelection();
            });

            // Delete hotspot
            $(document).on('click', '.delete-hotspot', function(e) {
                e.stopPropagation();
                const hotspotId = $(this).data('hotspot-id');
                if (confirm('Delete this hotspot?')) {
                    deleteHotspot(hotspotId);
                }
            });

            // Select hotspot from list
            $(document).on('click', '.hotspot-item', function() {
                const hotspotId = $(this).data('hotspot-id');
                selectHotspotById(hotspotId);
            });

            // Canvas object selection
            canvas.on('selection:created', function(e) {
                const obj = e.selected[0];
                if (obj && obj.hotspotId) {
                    selectHotspotById(obj.hotspotId);
                }
            });
        }

        function showHotspotForm(position, rect) {
            $('#no-selection-message').hide();
            $('#hotspot-form').show();
            $('#hotspot-id').val('');

            // Store position temporarily
            rect.positionData = position;
            selectedHotspot = rect;
        }

        function saveHotspot() {
            const hotspotId = $('#hotspot-id').val();
            const pageId = $('#page-id').val();

            const data = {
                type: $('#hotspot-type').val(),
                title: $('#hotspot-title').val(),
                description: $('#hotspot-description').val(),
                target_url: $('#hotspot-target-url').val(),
                product_id: $('#hotspot-product-id').val(),
                popup_content: $('#hotspot-popup-content').val(),
                target_type: $('#hotspot-target-type').val(),
                icon: $('#hotspot-icon').val(),
                color: $('#hotspot-color').val(),
                animation: $('#hotspot-animation').val(),
                is_active: true
            };

            // Add position if new hotspot
            if (selectedHotspot && selectedHotspot.positionData) {
                Object.assign(data, selectedHotspot.positionData);
            }

            const url = hotspotId ?
                `/api/flipbooks/hotspots/${hotspotId}` :
                `/api/flipbooks/pages/${pageId}/hotspots`;

            const method = hotspotId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success('Hotspot saved successfully!');
                    loadHotspotsForPage(pageId);
                    clearSelection();
                },
                error: function(xhr) {
                    toastr.error('Failed to save hotspot');
                }
            });
        }

        function deleteHotspot(hotspotId) {
            $.ajax({
                url: `/api/flipbooks/hotspots/${hotspotId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    toastr.success('Hotspot deleted!');
                    loadHotspotsForPage(currentPage.id);
                    clearSelection();
                },
                error: function() {
                    toastr.error('Failed to delete hotspot');
                }
            });
        }

        function selectHotspotById(hotspotId) {
            const hotspot = currentHotspots.find(h => h.id == hotspotId);
            if (!hotspot) return;

            // Highlight in list
            $('.hotspot-item').removeClass('active');
            $(`.hotspot-item[data-hotspot-id="${hotspotId}"]`).addClass('active');

            // Show form with data
            $('#no-selection-message').hide();
            $('#hotspot-form').show();
            $('#hotspot-id').val(hotspot.id);
            $('#hotspot-type').val(hotspot.type).trigger('change');
            $('#hotspot-title').val(hotspot.title);
            $('#hotspot-description').val(hotspot.description);
            $('#hotspot-target-url').val(hotspot.target_url);
            $('#hotspot-product-id').val(hotspot.product_id);
            $('#hotspot-popup-content').val(hotspot.popup_content);
            $('#hotspot-target-type').val(hotspot.target_type);
            $('#hotspot-icon').val(hotspot.icon);
            $('#hotspot-color').val(hotspot.color || '#3b82f6');
            $('#hotspot-animation').val(hotspot.animation);

            // Select on canvas
            const objects = canvas.getObjects('rect');
            const rect = objects.find(obj => obj.hotspotId == hotspotId);
            if (rect) {
                canvas.setActiveObject(rect);
                canvas.renderAll();
            }
        }

        function clearSelection() {
            $('#hotspot-form').hide();
            $('#no-selection-message').show();
            $('#hotspot-form')[0].reset();
            $('#hotspot-id').val('');
            selectedHotspot = null;
            $('.hotspot-item').removeClass('active');
            canvas.discardActiveObject();
            canvas.renderAll();
        }
    </script>
@endpush
