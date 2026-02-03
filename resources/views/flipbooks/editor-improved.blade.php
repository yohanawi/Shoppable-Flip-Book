@extends('layout.master')

@push('styles')
    <style>
        .page-canvas-wrapper {
            position: relative;
            display: inline-block;
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            max-width: 100%;
        }

        .canvas-container {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: 1px solid #ddd;
        }

        .hotspot-overlay {
            position: absolute;
            border: 2px solid #3b82f6;
            background: rgba(59, 130, 246, 0.2);
            cursor: move;
            transition: all 0.2s;
            z-index: 10;
        }

        .hotspot-overlay:hover {
            background: rgba(59, 130, 246, 0.3);
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .hotspot-overlay.selected {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.3);
        }

        .hotspot-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #fff;
            border: 2px solid #3b82f6;
            border-radius: 50%;
        }

        .hotspot-handle.nw {
            top: -5px;
            left: -5px;
            cursor: nw-resize;
        }

        .hotspot-handle.ne {
            top: -5px;
            right: -5px;
            cursor: ne-resize;
        }

        .hotspot-handle.sw {
            bottom: -5px;
            left: -5px;
            cursor: sw-resize;
        }

        .hotspot-handle.se {
            bottom: -5px;
            right: -5px;
            cursor: se-resize;
        }

        .hotspot-label {
            position: absolute;
            top: -20px;
            left: 0;
            background: #3b82f6;
            color: white;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 3px;
            white-space: nowrap;
        }

        .hotspot-item {
            cursor: pointer;
            transition: all 0.3s;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .hotspot-item:hover {
            background-color: #f8f9fa;
            border-color: #3b82f6;
        }

        .hotspot-item.active {
            background-color: #eff6ff;
            border-color: #3b82f6;
            border-left: 3px solid #3b82f6;
        }

        .page-thumbnail {
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
            border-radius: 6px;
            overflow: hidden;
        }

        .page-thumbnail:hover {
            border-color: #3b82f6;
            transform: scale(1.05);
        }

        .page-thumbnail.active {
            border-color: #3b82f6;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .drawing-mode {
            cursor: crosshair !important;
        }

        #page-image-container {
            position: relative;
            display: inline-block;
            background: white;
            user-select: none;
        }

        #page-image {
            display: block;
            max-width: 100%;
            height: auto;
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
                        <i class="ki-duotone ki-abstract-26 fs-1 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Hotspot Editor - {{ $flipbook->title }}
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('flipbooks.index') }}" class="text-muted text-hover-primary">Flipbooks</a>
                        </li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-500 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Editor</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('flipbooks.edit', $flipbook) }}" class="btn btn-sm btn-light">
                        <i class="ki-duotone ki-left fs-2"></i>Back to Details
                    </a>
                    <a href="{{ route('flipbook.view', $flipbook->slug) }}" target="_blank" class="btn btn-sm btn-success">
                        <i class="ki-duotone ki-eye fs-2"></i>Preview Flipbook
                    </a>
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
                            <div class="card-header pt-5">
                                <h3 class="card-title fw-bold">Pages</h3>
                            </div>
                            <div class="card-body p-3" id="pages-list" style="max-height: 600px; overflow-y: auto;">
                                @foreach ($flipbook->pages as $page)
                                    <div class="page-thumbnail mb-3 {{ $loop->first ? 'active' : '' }}"
                                        data-page-id="{{ $page->id }}" data-page-number="{{ $page->page_number }}"
                                        data-image-url="{{ $page->image_url }}" data-width="{{ $page->width }}"
                                        data-height="{{ $page->height }}">
                                        <img src="{{ $page->thumbnail_url }}" class="img-fluid rounded"
                                            alt="Page {{ $page->page_number }}" />
                                        <div class="text-center mt-2">
                                            <small class="text-muted fw-bold">Page {{ $page->page_number }}</small>
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
                                    <i class="ki-duotone ki-picture fs-1 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Canvas - Page <span id="current-page-number">1</span>
                                </h3>
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-sm btn-primary me-2" id="add-hotspot-btn">
                                        <i class="ki-duotone ki-plus fs-2"></i>Add Hotspot
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" id="delete-hotspot-btn"
                                        style="display: none;">
                                        <i class="ki-duotone ki-trash fs-2"></i>Delete Selected
                                    </button>
                                </div>
                            </div>
                            <div class="card-body d-flex justify-content-center align-items-center p-5"
                                style="min-height: 600px; background: #f5f8fa;">
                                <div class="page-canvas-wrapper">
                                    <div id="page-image-container">
                                        <img id="page-image" src="" alt="Page" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hotspots List for Current Page -->
                        <div class="card card-flush mt-5">
                            <div class="card-header">
                                <h3 class="card-title">Hotspots on Current Page</h3>
                            </div>
                            <div class="card-body" id="hotspots-list">
                                <div class="text-center py-5 text-muted">
                                    <p>No hotspots yet. Click "Add Hotspot" to create one.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Center-->

                    <!--begin::Right Sidebar - Properties-->
                    <div class="col-lg-3">
                        <div class="card card-flush sticky-top" style="top: 20px;">
                            <div class="card-header">
                                <h3 class="card-title fw-bold">
                                    <i class="ki-duotone ki-setting-2 fs-1 me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Hotspot Properties
                                </h3>
                            </div>
                            <div class="card-body" id="hotspot-properties">
                                <div class="text-center py-10 text-muted" id="no-selection-message">
                                    <i class="ki-duotone ki-information-5 fs-5x mb-5 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <p class="fw-bold">No Hotspot Selected</p>
                                    <p class="text-gray-600 fs-7">Click "Add Hotspot" or select an existing hotspot to
                                        edit</p>
                                </div>

                                <form id="hotspot-form" style="display: none;">
                                    <input type="hidden" id="hotspot-id" />
                                    <input type="hidden" id="page-id" />

                                    <div class="mb-5">
                                        <label class="form-label required">Type</label>
                                        <select class="form-select form-select-sm" id="hotspot-type" required>
                                            <option value="link">External Link</option>
                                            <option value="internal">Internal Page</option>
                                            <option value="product">Product</option>
                                            <option value="popup">Popup</option>
                                            <option value="video">Video</option>
                                        </select>
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control form-control-sm" id="hotspot-title"
                                            placeholder="e.g., View Product" />
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control form-control-sm" id="hotspot-description" rows="2"
                                            placeholder="Optional description"></textarea>
                                    </div>

                                    <div class="mb-5" id="target-url-field">
                                        <label class="form-label">Target URL</label>
                                        <input type="url" class="form-control form-control-sm"
                                            id="hotspot-target-url" placeholder="https://example.com" />
                                    </div>

                                    <div class="mb-5" id="target-page-field" style="display: none;">
                                        <label class="form-label">Target Page Number</label>
                                        <input type="number" class="form-control form-control-sm"
                                            id="hotspot-target-page" min="1" max="{{ $flipbook->total_pages }}"
                                            placeholder="Page number to jump to" />
                                    </div>

                                    <div class="mb-5" id="product-id-field" style="display: none;">
                                        <label class="form-label">Product</label>
                                        <select class="form-select form-select-sm" id="hotspot-product-id">
                                            <option value="">Select Product</option>
                                            @foreach (\App\Models\Product::where('is_active', true)->get() as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} -
                                                    ${{ number_format($product->price, 2) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="separator my-5"></div>

                                    <div class="mb-5">
                                        <label class="form-label">Color</label>
                                        <input type="color" class="form-control form-control-sm form-control-color"
                                            id="hotspot-color" value="#3b82f6" />
                                    </div>

                                    <div class="mb-5">
                                        <label class="form-label">Opacity</label>
                                        <input type="range" class="form-range" id="hotspot-opacity" min="0"
                                            max="1" step="0.1" value="0.3" />
                                        <small class="text-muted"><span id="opacity-value">30</span>%</small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success btn-sm flex-fill"
                                            id="save-hotspot-btn">
                                            <i class="ki-duotone ki-check fs-2"></i>Save
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm flex-fill"
                                            id="cancel-hotspot-btn">
                                            <i class="ki-duotone ki-cross fs-2"></i>Cancel
                                        </button>
                                    </div>
                                </form>
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
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        let currentPage = null;
        let currentHotspots = [];
        let isDrawing = false;
        let drawStartX, drawStartY;
        let selectedHotspot = null;
        let tempHotspotElement = null;

        $(document).ready(function() {
            // Load first page
            const firstPage = $('.page-thumbnail').first();
            if (firstPage.length) {
                loadPage(firstPage);
            }

            initializeEventHandlers();
        });

        function initializeEventHandlers() {
            // Page selection
            $(document).on('click', '.page-thumbnail', function() {
                $('.page-thumbnail').removeClass('active');
                $(this).addClass('active');
                loadPage($(this));
                clearSelection();
            });

            // Add hotspot button
            $('#add-hotspot-btn').click(function() {
                startDrawingMode();
            });

            // Hotspot type change
            $('#hotspot-type').change(function() {
                const type = $(this).val();
                $('#target-url-field, #target-page-field, #product-id-field').hide();

                if (type === 'link') {
                    $('#target-url-field').show();
                } else if (type === 'internal') {
                    $('#target-page-field').show();
                } else if (type === 'product') {
                    $('#product-id-field').show();
                } else if (type === 'video') {
                    $('#target-url-field').show();
                }
            });

            // Opacity slider
            $('#hotspot-opacity').on('input', function() {
                const value = Math.round($(this).val() * 100);
                $('#opacity-value').text(value);
            });

            // Save hotspot
            $('#save-hotspot-btn').click(function() {
                saveHotspot();
            });

            // Cancel
            $('#cancel-hotspot-btn').click(function() {
                if (tempHotspotElement) {
                    tempHotspotElement.remove();
                    tempHotspotElement = null;
                }
                clearSelection();
            });

            // Delete hotspot
            $('#delete-hotspot-btn').click(function() {
                if (selectedHotspot && confirm('Delete this hotspot?')) {
                    deleteHotspot(selectedHotspot);
                }
            });
        }

        function loadPage(pageElement) {
            currentPage = {
                id: pageElement.data('page-id'),
                number: pageElement.data('page-number'),
                imageUrl: pageElement.data('image-url'),
                width: pageElement.data('width'),
                height: pageElement.data('height')
            };

            $('#current-page-number').text(currentPage.number);
            $('#page-id').val(currentPage.id);

            // Load image
            const img = $('#page-image');
            img.attr('src', currentPage.imageUrl);

            img.off('load').on('load', function() {
                loadHotspotsForPage(currentPage.id);
            });
        }

        function loadHotspotsForPage(pageId) {
            $.ajax({
                url: `/api/flipbooks/pages/${pageId}/hotspots`,
                method: 'GET',
                success: function(response) {
                    currentHotspots = response.hotspots || [];
                    renderHotspots();
                    updateHotspotsList();
                },
                error: function(xhr) {
                    console.error('Failed to load hotspots', xhr);
                    currentHotspots = [];
                    renderHotspots();
                }
            });
        }

        function renderHotspots() {
            // Remove existing hotspot overlays
            $('.hotspot-overlay').remove();

            const container = $('#page-image-container');
            const img = $('#page-image');
            const imgWidth = img.width();
            const imgHeight = img.height();

            currentHotspots.forEach(hotspot => {
                const left = (hotspot.x * imgWidth) / 100;
                const top = (hotspot.y * imgHeight) / 100;
                const width = (hotspot.width * imgWidth) / 100;
                const height = (hotspot.height * imgHeight) / 100;

                const hotspotDiv = $('<div>', {
                    class: 'hotspot-overlay',
                    'data-hotspot-id': hotspot.id,
                    css: {
                        left: left + 'px',
                        top: top + 'px',
                        width: width + 'px',
                        height: height + 'px',
                        borderColor: hotspot.color || '#3b82f6',
                        background: hexToRgba(hotspot.color || '#3b82f6', hotspot.opacity || 0.3)
                    }
                });

                // Add label
                const label = $('<div>', {
                    class: 'hotspot-label',
                    text: hotspot.title || `Hotspot #${hotspot.id}`,
                    css: {
                        background: hotspot.color || '#3b82f6'
                    }
                });
                hotspotDiv.append(label);

                // Add resize handles
                ['nw', 'ne', 'sw', 'se'].forEach(pos => {
                    hotspotDiv.append($('<div>', {
                        class: `hotspot-handle ${pos}`
                    }));
                });

                container.append(hotspotDiv);

                // Make draggable and resizable
                makeHotspotInteractive(hotspotDiv, hotspot);
            });
        }

        function makeHotspotInteractive(element, hotspot) {
            element.draggable({
                containment: '#page-image-container',
                stop: function(event, ui) {
                    // Hotspot moved - could auto-save or mark as changed
                }
            });

            element.resizable({
                handles: {
                    'nw': '.hotspot-handle.nw',
                    'ne': '.hotspot-handle.ne',
                    'sw': '.hotspot-handle.sw',
                    'se': '.hotspot-handle.se'
                },
                containment: '#page-image-container',
                stop: function(event, ui) {
                    // Hotspot resized - could auto-save or mark as changed
                }
            });

            element.click(function(e) {
                e.stopPropagation();
                selectHotspot(hotspot, element);
            });
        }

        function startDrawingMode() {
            isDrawing = true;
            $('#add-hotspot-btn').addClass('btn-warning').html(
                '<i class="ki-duotone ki-pencil fs-2"></i>Drawing... Click & drag on canvas');
            $('#page-image-container').addClass('drawing-mode');

            const container = $('#page-image-container');
            container.off('mousedown').on('mousedown', function(e) {
                if (!isDrawing) return;

                const offset = container.offset();
                drawStartX = e.pageX - offset.left;
                drawStartY = e.pageY - offset.top;

                tempHotspotElement = $('<div>', {
                    class: 'hotspot-overlay',
                    css: {
                        left: drawStartX + 'px',
                        top: drawStartY + 'px',
                        width: '0px',
                        height: '0px',
                        borderColor: '#3b82f6',
                        background: 'rgba(59, 130, 246, 0.3)'
                    }
                });

                container.append(tempHotspotElement);

                container.on('mousemove', handleDrawMove);
                container.on('mouseup', handleDrawEnd);
            });
        }

        function handleDrawMove(e) {
            if (!tempHotspotElement) return;

            const container = $('#page-image-container');
            const offset = container.offset();
            const currentX = e.pageX - offset.left;
            const currentY = e.pageY - offset.top;

            const left = Math.min(drawStartX, currentX);
            const top = Math.min(drawStartY, currentY);
            const width = Math.abs(currentX - drawStartX);
            const height = Math.abs(currentY - drawStartY);

            tempHotspotElement.css({
                left: left + 'px',
                top: top + 'px',
                width: width + 'px',
                height: height + 'px'
            });
        }

        function handleDrawEnd(e) {
            const container = $('#page-image-container');
            container.off('mousemove', handleDrawMove);
            container.off('mouseup', handleDrawEnd);
            container.removeClass('drawing-mode');

            isDrawing = false;
            $('#add-hotspot-btn').removeClass('btn-warning').html(
                '<i class="ki-duotone ki-plus fs-2"></i>Add Hotspot');

            if (!tempHotspotElement) return;

            const width = parseFloat(tempHotspotElement.css('width'));
            const height = parseFloat(tempHotspotElement.css('height'));

            // Minimum size check
            if (width < 20 || height < 20) {
                tempHotspotElement.remove();
                tempHotspotElement = null;
                toastr.warning('Hotspot too small. Please draw a larger area.');
                return;
            }

            // Show form with position data
            showHotspotForm(tempHotspotElement);
        }

        function showHotspotForm(element) {
            const container = $('#page-image-container');
            const img = $('#page-image');
            const imgWidth = img.width();
            const imgHeight = img.height();

            const left = parseFloat(element.css('left'));
            const top = parseFloat(element.css('top'));
            const width = parseFloat(element.css('width'));
            const height = parseFloat(element.css('height'));

            // Store percentage values
            element.data('position', {
                x: (left / imgWidth) * 100,
                y: (top / imgHeight) * 100,
                width: (width / imgWidth) * 100,
                height: (height / imgHeight) * 100
            });

            $('#no-selection-message').hide();
            $('#hotspot-form').show();
            $('#hotspot-id').val('');
            $('#hotspot-form')[0].reset();
            $('#hotspot-type').val('link').trigger('change');
        }

        function selectHotspot(hotspot, element) {
            $('.hotspot-overlay').removeClass('selected');
            element.addClass('selected');
            selectedHotspot = hotspot;

            $('#no-selection-message').hide();
            $('#hotspot-form').show();
            $('#delete-hotspot-btn').show();

            // Populate form
            $('#hotspot-id').val(hotspot.id);
            $('#hotspot-type').val(hotspot.type).trigger('change');
            $('#hotspot-title').val(hotspot.title || '');
            $('#hotspot-description').val(hotspot.description || '');
            $('#hotspot-target-url').val(hotspot.target_url || '');
            $('#hotspot-target-page').val(hotspot.target_page || '');
            $('#hotspot-product-id').val(hotspot.product_id || '');
            $('#hotspot-color').val(hotspot.color || '#3b82f6');
            $('#hotspot-opacity').val(hotspot.opacity || 0.3);
            $('#opacity-value').text(Math.round((hotspot.opacity || 0.3) * 100));

            // Highlight in list
            $('.hotspot-item').removeClass('active');
            $(`.hotspot-item[data-hotspot-id="${hotspot.id}"]`).addClass('active');
        }

        function saveHotspot() {
            const hotspotId = $('#hotspot-id').val();
            const pageId = $('#page-id').val();

            const data = {
                type: $('#hotspot-type').val(),
                title: $('#hotspot-title').val(),
                description: $('#hotspot-description').val(),
                target_url: $('#hotspot-target-url').val(),
                target_page: $('#hotspot-target-page').val(),
                product_id: $('#hotspot-product-id').val(),
                color: $('#hotspot-color').val(),
                opacity: parseFloat($('#hotspot-opacity').val()),
                is_active: true
            };

            // Get position data
            if (tempHotspotElement) {
                const position = tempHotspotElement.data('position');
                Object.assign(data, {
                    x: position.x,
                    y: position.y,
                    width: position.width,
                    height: position.height
                });
            } else if (selectedHotspot) {
                // Get updated position from DOM element
                const element = $(`.hotspot-overlay[data-hotspot-id="${selectedHotspot.id}"]`);
                const img = $('#page-image');
                const imgWidth = img.width();
                const imgHeight = img.height();

                const left = parseFloat(element.css('left'));
                const top = parseFloat(element.css('top'));
                const width = parseFloat(element.css('width'));
                const height = parseFloat(element.css('height'));

                Object.assign(data, {
                    x: (left / imgWidth) * 100,
                    y: (top / imgHeight) * 100,
                    width: (width / imgWidth) * 100,
                    height: (height / imgHeight) * 100
                });
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

                    if (tempHotspotElement) {
                        tempHotspotElement.remove();
                        tempHotspotElement = null;
                    }

                    loadHotspotsForPage(pageId);
                    updateHotspotCount(pageId);
                    clearSelection();
                },
                error: function(xhr) {
                    toastr.error('Failed to save hotspot: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    console.error(xhr);
                }
            });
        }

        function deleteHotspot(hotspot) {
            $.ajax({
                url: `/api/flipbooks/hotspots/${hotspot.id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    toastr.success('Hotspot deleted successfully!');
                    loadHotspotsForPage(currentPage.id);
                    updateHotspotCount(currentPage.id);
                    clearSelection();
                },
                error: function(xhr) {
                    toastr.error('Failed to delete hotspot');
                    console.error(xhr);
                }
            });
        }

        function clearSelection() {
            selectedHotspot = null;
            $('.hotspot-overlay').removeClass('selected');
            $('.hotspot-item').removeClass('active');
            $('#hotspot-form').hide();
            $('#no-selection-message').show();
            $('#delete-hotspot-btn').hide();
            $('#hotspot-form')[0].reset();
        }

        function updateHotspotsList() {
            const listContainer = $('#hotspots-list');

            if (currentHotspots.length === 0) {
                listContainer.html(`
                    <div class="text-center py-5 text-muted">
                        <p>No hotspots on this page yet.</p>
                    </div>
                `);
                return;
            }

            let html = '<div class="d-flex flex-column gap-2">';
            currentHotspots.forEach(hotspot => {
                const typeIcon = getTypeIcon(hotspot.type);
                const typeColor = getTypeColor(hotspot.type);

                html += `
                    <div class="hotspot-item" data-hotspot-id="${hotspot.id}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-40px" style="background: ${typeColor}20;">
                                    <i class="ki-duotone ${typeIcon} fs-2x" style="color: ${typeColor};">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div>
                                    <div class="fw-bold text-gray-800">${hotspot.title || 'Untitled'}</div>
                                    <div class="text-muted fs-7">${hotspot.type}</div>
                                </div>
                            </div>
                            <span class="badge badge-light-${getTypeBadgeColor(hotspot.type)}">${hotspot.type}</span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';

            listContainer.html(html);

            // Add click handlers
            $('.hotspot-item').click(function() {
                const hotspotId = $(this).data('hotspot-id');
                const hotspot = currentHotspots.find(h => h.id == hotspotId);
                const element = $(`.hotspot-overlay[data-hotspot-id="${hotspotId}"]`);
                if (hotspot && element.length) {
                    selectHotspot(hotspot, element);
                }
            });
        }

        function updateHotspotCount(pageId) {
            $.ajax({
                url: `/api/flipbooks/pages/${pageId}/hotspots/count`,
                method: 'GET',
                success: function(response) {
                    $(`.hotspot-count-${pageId}`).text(`${response.count} hotspots`);
                }
            });
        }

        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        function getTypeIcon(type) {
            const icons = {
                'link': 'ki-abstract-26',
                'internal': 'ki-arrow-right',
                'product': 'ki-basket',
                'popup': 'ki-message-text-2',
                'video': 'ki-youtube'
            };
            return icons[type] || 'ki-abstract-26';
        }

        function getTypeColor(type) {
            const colors = {
                'link': '#3b82f6',
                'internal': '#8b5cf6',
                'product': '#10b981',
                'popup': '#f59e0b',
                'video': '#ef4444'
            };
            return colors[type] || '#3b82f6';
        }

        function getTypeBadgeColor(type) {
            const colors = {
                'link': 'primary',
                'internal': 'info',
                'product': 'success',
                'popup': 'warning',
                'video': 'danger'
            };
            return colors[type] || 'primary';
        }
    </script>
@endpush
