<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $flipbook->title }} - Shoppable Catalog</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        #flipbook-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
        }

        #flipbook-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        #flipbook-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .cart-button {
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .cart-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        #flipbook-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            perspective: 2000px;
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #pdf-viewer {
            width: 100%;
            height: 100%;
            margin: 0 auto;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #flipbook {
            margin: 0 auto;
            box-shadow: 0 0 80px rgba(0, 0, 0, 0.5);
            cursor: grab;
        }

        #flipbook:active {
            cursor: grabbing;
        }

        .page-container {
            background: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            cursor: grab;
            user-select: none;
        }

        .page-container:active {
            cursor: grabbing;
        }

        .page-container canvas {
            display: block;
            width: 100% !important;
            height: 100% !important;
            position: relative;
            z-index: 1;
            pointer-events: none;
        }

        /* Turn.js styling enhancements */
        .turn-page {
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .turn-page-wrapper {
            perspective: 2000px;
        }

        /* Hard page effect */
        .hard {
            background: #f0f0f0 !important;
            border: solid 1px #ccc;
        }

        /* Page shadows during flip */
        .page-wrapper .page {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .even .gradient {
            background: linear-gradient(to right, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0) 10%);
        }

        .odd .gradient {
            background: linear-gradient(to left, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0) 10%);
        }

        /* Hotspots */
        .hotspot {
            position: absolute;
            border: 3px solid;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            padding: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            pointer-events: auto;
        }

        .hotspot:hover {
            transform: scale(1.15);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 1);
            z-index: 101;
        }

        .hotspot .hotspot-title {
            font-size: 11px;
            font-weight: 600;
            color: #333;
            margin-top: 5px;
            text-align: center;
            line-height: 1.2;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .hotspot.pulse {
            animation: pulse 2s infinite;
        }

        .hotspot.product-hotspot {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.15);
        }

        .hotspot.product-hotspot:hover {
            background: rgba(40, 167, 69, 0.25);
        }

        .hotspot.product-hotspot .fa-shopping-cart {
            color: #28a745;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 15px rgba(40, 167, 69, 0);
            }
        }

        /* Controls */
        #controls {
            margin-top: 30px;
            text-align: center;
            padding: 25px;
        }

        #controls button {
            padding: 14px 28px;
            margin: 5px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        #controls button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        #controls button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        #page-indicator {
            display: inline-block;
            padding: 14px 28px;
            background: #f8f9fa;
            border-radius: 25px;
            font-weight: 600;
            color: #495057;
            margin: 0 10px;
        }

        /* Product Modal */
        .product-modal .modal-dialog {
            max-width: 900px;
        }

        .product-image-gallery {
            position: relative;
        }

        .product-image-gallery img {
            width: 100%;
            border-radius: 10px;
        }

        .product-discount-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff4444;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .product-price {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }

        .product-old-price {
            font-size: 1.5rem;
            text-decoration: line-through;
            color: #999;
            margin-left: 10px;
        }

        .product-specs {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .product-specs dt {
            font-weight: 600;
            color: #667eea;
        }

        .add-to-cart-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 25px;
            color: white;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .add-to-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
        }

        .quantity-selector button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .quantity-selector button:hover {
            background: #667eea;
            color: white;
        }

        .quantity-selector input {
            width: 80px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 8px;
        }

        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        .zoom-controls {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 100;
        }

        .zoom-controls button {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #667eea;
        }

        .zoom-controls button:hover {
            background: #667eea;
            color: white;
            transform: scale(1.1);
        }

        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 15px 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 300px;
            animation: slideIn 0.3s;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.success {
            border-left: 4px solid #28a745;
        }

        .toast.error {
            border-left: 4px solid #dc3545;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-container {
                width: 100%;
                max-width: 400px;
            }

            .pages-container {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div id="flipbook-container">
        <!-- Header -->
        <div id="flipbook-header">
            <div>
                <h1>{{ $flipbook->title }}</h1>
                @if ($flipbook->description)
                    <p class="text-muted mb-0">{{ $flipbook->description }}</p>
                @endif
            </div>
            <button class="cart-button" onclick="window.location.href='/cart'">
                <i class="fas fa-shopping-cart"></i> Cart
                <span class="cart-badge" id="cart-count">0</span>
            </button>
        </div>

        <!-- Flipbook Wrapper -->
        <div id="flipbook-wrapper">
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Zoom Controls -->
            <div class="zoom-controls">
                <button id="zoom-in" title="Zoom In"><i class="fas fa-plus"></i></button>
                <button id="zoom-out" title="Zoom Out"><i class="fas fa-minus"></i></button>
                <button id="zoom-reset" title="Reset"><i class="fas fa-expand"></i></button>
                <button id="fullscreen" title="Fullscreen"><i class="fas fa-expand-arrows-alt"></i></button>
            </div>

            <!-- PDF Viewer -->
            <div id="pdf-viewer">
                <div id="flipbook"></div>
            </div>
        </div>

        <!-- Controls -->
        <div id="controls">
            <button id="first-page-btn"><i class="fas fa-fast-backward"></i> First</button>
            <button id="prev-btn"><i class="fas fa-chevron-left"></i> Previous</button>
            <span id="page-indicator">Page <span id="current-page">1</span> of <span id="total-pages">0</span></span>
            <button id="next-btn">Next <i class="fas fa-chevron-right"></i></button>
            <button id="last-page-btn"><i class="fas fa-fast-forward"></i> Last</button>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade product-modal" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="productModalBody">
                    <!-- Product details loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/3/turn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        const flipbookId = {{ $flipbook->id }};
        const flipbookSlug = '{{ $flipbook->slug }}';
        const pdfUrl = '{{ asset('storage/' . $flipbook->pdf_path) }}';
        let currentPage = 1;
        let totalPages = 0;
        let pdfDoc = null;
        let currentZoom = 1;
        const renderScale = 1.5; // Higher quality rendering
        let sessionId = generateSessionId();
        let hotspots = [];
        let renderedPages = new Set();
        let pageWidth = 0;
        let pageHeight = 0;
        let isFlipbookReady = false;
        let flipSound = null;

        // Configure PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        $(document).ready(function() {
            console.log('🚀 Flipbook Viewer Initializing...');
            console.log('PDF URL:', pdfUrl);
            console.log('Flipbook Slug:', flipbookSlug);

            initFlipSound();
            loadPdf();
            bindEvents();
            trackView();
            loadHotspots();
            updateCartCount();
        });

        function loadPdf() {
            console.log('📥 Loading PDF:', pdfUrl);
            $('.loading-spinner').show();

            pdfjsLib.getDocument({
                url: pdfUrl,
                cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                cMapPacked: true
            }).promise.then(function(pdf) {
                console.log('✅ PDF loaded successfully! Total pages:', pdf.numPages);
                pdfDoc = pdf;
                totalPages = pdf.numPages;
                $('#total-pages').text(totalPages);

                prepareFlipbook();
            }).catch(function(error) {
                console.error('❌ PDF load error:', error);
                $('.loading-spinner').hide();
                showToast('Failed to load PDF: ' + error.message, 'error');

                // Show user-friendly error in viewer
                $('#flipbook-wrapper').html(`
                    <div class="alert alert-danger text-center p-5">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h4>Failed to Load PDF</h4>
                        <p>${error.message}</p>
                        <button class="btn btn-primary" onclick="location.reload()">
                            <i class="fas fa-redo"></i> Retry
                        </button>
                    </div>
                `);
            });
        }

        function prepareFlipbook() {
            if (!pdfDoc) return;

            console.log('🔧 Preparing flipbook...');

            pdfDoc.getPage(1).then(function(page) {
                const viewport = page.getViewport({
                    scale: renderScale
                });
                pageWidth = viewport.width;
                pageHeight = viewport.height;

                // Adjust size to fit screen
                const maxWidth = window.innerWidth - 100;
                const maxHeight = window.innerHeight - 300;
                const scaleX = maxWidth / (pageWidth * 2);
                const scaleY = maxHeight / pageHeight;
                const fitScale = Math.min(scaleX, scaleY, 1);

                if (fitScale < 1) {
                    pageWidth = pageWidth * fitScale;
                    pageHeight = pageHeight * fitScale;
                }

                console.log('📏 Page dimensions:', pageWidth, 'x', pageHeight);

                buildPages();

                renderAllPages().then(function() {
                    console.log('🎬 Initializing Turn.js...');
                    initFlipbook();
                    updateControls();

                    setTimeout(() => {
                        $('.loading-spinner').fadeOut(500);
                        showToast('Flipbook loaded! Use arrow keys or click to navigate.',
                            'success');
                    }, 300);
                }).catch(function(error) {
                    console.error('❌ Render pages error:', error);
                    $('.loading-spinner').hide();
                    showToast('Failed to render PDF pages', 'error');
                });
            }).catch(function(error) {
                console.error('❌ Failed to read first page:', error);
                $('.loading-spinner').hide();
                showToast('Failed to initialize viewer', 'error');
            });
        }

        function buildPages() {
            const flipbook = $('#flipbook');
            flipbook.empty();
            for (let i = 1; i <= totalPages; i++) {
                flipbook.append(`<div class="page-container" data-page-number="${i}"></div>`);
            }
        }

        function initFlipbook() {
            const width = pageWidth * 2;
            const height = pageHeight;

            console.log('Initializing Turn.js with dimensions:', width, 'x', height);

            $('#flipbook').turn({
                width: width,
                height: height,
                autoCenter: true,
                gradients: true,
                elevation: 50,
                duration: 800,
                acceleration: true,
                display: 'double',
                page: 1,
                when: {
                    turning: function(event, page, view) {
                        // Play flip sound
                        playFlipSound();

                        // Pre-render adjacent pages for smooth experience
                        renderPage(page);
                        renderPage(page + 1);
                        renderPage(page - 1);
                        renderPage(page + 2);
                    },
                    turned: function(event, page, view) {
                        currentPage = page;
                        updatePageIndicator(page);
                        updateControls();
                        renderVisibleHotspots();
                        trackPageTurn(page);
                        console.log('Turned to page:', page);
                    },
                    start: function(event, pageObject, corner) {
                        console.log('Started turning from corner:', corner);
                    },
                    end: function(event, pageObject, turned) {
                        console.log('Flip animation ended');
                    }
                }
            });

            // Enable touch events for mobile
            $('#flipbook').bind('touchstart', function(e) {
                console.log('Touch started on flipbook');
            });

            isFlipbookReady = true;
            console.log('✅ Turn.js flipbook initialized successfully');
            console.log('💡 TIP: Click and drag page corners to flip pages');

            applyZoom();
            renderVisibleHotspots();
        }

        function renderAllPages() {
            console.log('📄 Starting to render all', totalPages, 'pages...');
            const tasks = [];
            for (let i = 1; i <= totalPages; i++) {
                tasks.push(renderPage(i));
            }
            return Promise.all(tasks).then(() => {
                console.log('✅ All pages rendered successfully');
            });
        }

        function renderPage(pageNum) {
            if (!pdfDoc || pageNum < 1 || pageNum > totalPages || renderedPages.has(pageNum)) {
                return Promise.resolve();
            }

            const pageElement = document.querySelector(
                `#flipbook .page-container[data-page-number="${pageNum}"]`
            );
            if (!pageElement) {
                console.warn('Page element not found for page:', pageNum);
                return Promise.resolve();
            }

            console.log('🎨 Rendering page:', pageNum);

            return pdfDoc.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: renderScale
                });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Clear previous content
                pageElement.innerHTML = '';
                pageElement.appendChild(canvas);

                // Set explicit dimensions
                pageElement.style.width = `${viewport.width}px`;
                pageElement.style.height = `${viewport.height}px`;

                return page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise.then(function() {
                    renderedPages.add(pageNum);
                    console.log('✓ Page', pageNum, 'rendered');
                    renderHotspotsForPage(pageNum, pageElement);
                });
            }).catch(function(error) {
                console.error('Error rendering page', pageNum, ':', error);
            });
        }

        function showPage(pageNum) {
            if (!isFlipbookReady || !$('#flipbook').data('turn')) {
                console.warn('Flipbook not ready yet');
                return;
            }
            if (pageNum < 1 || pageNum > totalPages) {
                console.warn('Invalid page number:', pageNum);
                return;
            }
            console.log('Navigating to page:', pageNum);
            $('#flipbook').turn('page', pageNum);
        }

        function renderVisibleHotspots() {
            if (!isFlipbookReady || !$('#flipbook').data('turn')) return;

            const visiblePages = $('#flipbook').turn('view');
            console.log('Rendering hotspots for visible pages:', visiblePages);

            visiblePages.forEach(pageNum => {
                const pageElement = document.querySelector(
                    `#flipbook .page-container[data-page-number="${pageNum}"]`
                );
                if (pageElement) {
                    renderHotspotsForPage(pageNum, pageElement);
                }
            });
        }

        function loadHotspots() {
            $.ajax({
                url: `/api/flipbook/${flipbookSlug}/hotspots`,
                method: 'GET',
                success: function(data) {
                    hotspots = Array.isArray(data) ? data : (data.hotspots || []);
                    console.log('✅ Hotspots loaded successfully:', hotspots.length);
                    console.table(hotspots);
                    renderVisibleHotspots();
                },
                error: function(xhr) {
                    console.error('❌ Failed to load hotspots:', xhr);
                }
            });
        }

        function renderHotspotsForPage(pageNum, container) {
            const pageHotspots = hotspots.filter(h => h.page_number === pageNum);
            console.log(`Rendering ${pageHotspots.length} hotspots for page ${pageNum}`, pageHotspots);

            $(container).find('.hotspot').remove();

            pageHotspots.forEach(hotspot => {
                const displayTitle = hotspot.product_name || hotspot.title || 'Click to view';
                const hotspotEl = $('<div>')
                    .addClass('hotspot')
                    .addClass(hotspot.animation || 'pulse')
                    .attr('title', displayTitle)
                    .css({
                        left: hotspot.x_position + '%',
                        top: hotspot.y_position + '%',
                        width: hotspot.width + '%',
                        height: hotspot.height + '%',
                        borderColor: hotspot.color || '#667eea'
                    });

                if (hotspot.type === 'product') {
                    hotspotEl.addClass('product-hotspot');
                    hotspotEl.html(
                        `<i class="fas fa-shopping-cart"></i><span class="hotspot-title">${displayTitle}</span>`
                    );
                } else if (hotspot.icon) {
                    hotspotEl.html(
                        `<i class="fas fa-${hotspot.icon}"></i><span class="hotspot-title">${displayTitle}</span>`
                    );
                } else {
                    hotspotEl.html(`<i class="fas fa-link"></i><span class="hotspot-title">${displayTitle}</span>`);
                }

                hotspotEl.on('click', function() {
                    handleHotspotClick(hotspot);
                });

                $(container).append(hotspotEl);
            });
        }

        function handleHotspotClick(hotspot) {
            trackHotspotClick(hotspot.id);
            console.log('Hotspot clicked:', hotspot);

            // Handle different action types
            if (hotspot.type === 'product') {
                if (hotspot.product_id) {
                    // Show product from database
                    showProductModal(hotspot.product_id);
                } else if (hotspot.product_name) {
                    // Show simple product info modal
                    showSimpleProductModal(hotspot);
                }
            } else if (hotspot.type === 'link') {
                if (hotspot.target_url) {
                    window.open(hotspot.target_url, '_blank');
                }
            } else if (hotspot.type === 'internal_page') {
                if (hotspot.target_page_number) {
                    // Jump to internal page
                    if (hotspot.target_page_number <= totalPages) {
                        $('#flipbook').turn('page', hotspot.target_page_number);
                        showToast(`Jumping to page ${hotspot.target_page_number}`, 'success');
                    }
                }
            } else if (hotspot.type === 'popup_image') {
                if (hotspot.popup_media_url) {
                    showImagePopup(hotspot);
                }
            } else if (hotspot.type === 'popup_video') {
                if (hotspot.popup_media_url) {
                    showVideoPopup(hotspot);
                }
            }
        }

        function showSimpleProductModal(hotspot) {
            const hasUrl = hotspot.target_url && hotspot.target_url.trim() !== '';
            const html = `
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-4x text-success mb-4"></i>
                    <h2 class="mb-3">${hotspot.product_name || hotspot.title}</h2>
                    ${hotspot.description ? `<p class="text-muted lead mt-3 mb-4">${hotspot.description}</p>` : ''}
                    ${hotspot.price ? `<div class="mb-4"><span class="h3 text-success fw-bold">$${hotspot.price}</span></div>` : ''}
                    ${hasUrl ? `
                            <a href="${hotspot.target_url}" target="_blank" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-external-link-alt me-2"></i>View Product Details
                            </a>
                        ` : `
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle"></i> This is a product hotspot.
                                <br>Add a URL in the editor to enable direct product links.
                            </div>
                        `}
                </div>
            `;

            $('#productModalBody').html(html);
            new bootstrap.Modal($('#productModal')).show();
        }

        function showImagePopup(hotspot) {
            const imageUrl = hotspot.popup_media_url.startsWith('http') ?
                hotspot.popup_media_url :
                `/storage/${hotspot.popup_media_url}`;

            const html = `
                <div class="text-center">
                    ${hotspot.title ? `<h4 class="mb-3">${hotspot.title}</h4>` : ''}
                    <img src="${imageUrl}" alt="${hotspot.title || 'Image'}" class="img-fluid rounded" style="max-width: 100%; max-height: 70vh;">
                    ${hotspot.description ? `<p class="text-muted mt-3">${hotspot.description}</p>` : ''}
                </div>
            `;

            $('#productModalBody').html(html);
            new bootstrap.Modal($('#productModal')).show();
        }

        function showVideoPopup(hotspot) {
            let videoHtml = '';
            const videoUrl = hotspot.popup_media_url;

            // Check if it's a YouTube video
            if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
                let videoId = '';
                if (videoUrl.includes('youtube.com')) {
                    videoId = videoUrl.split('v=')[1]?.split('&')[0];
                } else {
                    videoId = videoUrl.split('youtu.be/')[1]?.split('?')[0];
                }
                videoHtml =
                    `<iframe width="100%" height="450" src="https://www.youtube.com/embed/${videoId}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
            }
            // Check if it's a Vimeo video
            else if (videoUrl.includes('vimeo.com')) {
                const videoId = videoUrl.split('vimeo.com/')[1]?.split('?')[0];
                videoHtml =
                    `<iframe src="https://player.vimeo.com/video/${videoId}" width="100%" height="450" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
            }
            // Direct video file
            else {
                const fullUrl = videoUrl.startsWith('http') ? videoUrl : `/storage/${videoUrl}`;
                videoHtml =
                    `<video controls width="100%" style="max-height: 70vh;"><source src="${fullUrl}" type="video/mp4">Your browser does not support the video tag.</video>`;
            }

            const html = `
                <div>
                    ${hotspot.title ? `<h4 class="mb-3">${hotspot.title}</h4>` : ''}
                    ${videoHtml}
                    ${hotspot.description ? `<p class="text-muted mt-3">${hotspot.description}</p>` : ''}
                </div>
            `;

            $('#productModalBody').html(html);
            new bootstrap.Modal($('#productModal')).show();
        }

        function showToast(message, type = 'success') {
            const bgColor = type === 'success' ? 'linear-gradient(135deg, #28a745 0%, #20c997 100%)' :
                type === 'error' ? 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)' :
                'linear-gradient(135deg, #007bff 0%, #0056b3 100%)';

            const toast = $(`
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                    <div class="toast show" role="alert">
                        <div class="toast-body text-white fw-bold" style="background: ${bgColor}; border-radius: 8px;">
                            ${message}
                        </div>
                    </div>
                </div>
            `);

            $('body').append(toast);
            setTimeout(() => toast.fadeOut(300, () => toast.remove()), 2000);
        }

        function showProductModal(productId) {
            $.ajax({
                url: `/api/products/${productId}`,
                method: 'GET',
                success: function(product) {
                    const discount = product.sale_price ? Math.round((1 - product.sale_price / product.price) *
                        100) : 0;

                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="product-image-gallery">
                                    <img src="${product.featured_image_url}" alt="${product.name}" class="img-fluid">
                                    ${discount ? `<div class="product-discount-badge">-${discount}% OFF</div>` : ''}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h2>${product.name}</h2>
                                <p class="text-muted">${product.short_description || ''}</p>
                                
                                <div class="my-3">
                                    <span class="product-price">$${product.current_price}</span>
                                    ${product.sale_price ? `<span class="product-old-price">$${product.price}</span>` : ''}
                                </div>

                                ${product.in_stock ? 
                                    `<div class="text-success"><i class="fas fa-check-circle"></i> In Stock (${product.stock_quantity} available)</div>` :
                                    `<div class="text-danger"><i class="fas fa-times-circle"></i> Out of Stock</div>`
                                }

                                ${product.in_stock ? `
                                                <div class="quantity-selector">
                                                    <button onclick="changeQuantity(-1)"><i class="fas fa-minus"></i></button>
                                                    <input type="number" id="quantity" value="1" min="1" max="${product.stock_quantity}" readonly>
                                                                            <button onclick="changeQuantity(1)"><i class="fas fa-plus"></i></button>
                                                </div>
                                                                        
                                                <button class="add-to-cart-btn" onclick="addToCart(${productId})">
                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                </button>
                                                                    ` : ''}

                                ${product.description ? `
                                                                        <div class="mt-4">
                                                                            <h5>Description</h5>
                                                                            <p>${product.description}</p>
                                                                        </div>
                                                                    ` : ''}
                            </div>
                        </div>
                    `;

                    $('#productModalBody').html(html);
                    new bootstrap.Modal($('#productModal')).show();
                },
                error: function() {
                    showToast('Failed to load product details', 'error');
                }
            });
        }

        function changeQuantity(delta) {
            const input = $('#quantity');
            const current = parseInt(input.val());
            const max = parseInt(input.attr('max'));
            const newVal = Math.max(1, Math.min(max, current + delta));
            input.val(newVal);
        }

        function addToCart(productId) {
            const quantity = parseInt($('#quantity').val());

            $.ajax({
                url: '/cart/add',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    showToast('Product added to cart!', 'success');
                    updateCartCount();
                    bootstrap.Modal.getInstance($('#productModal')).hide();
                },
                error: function(xhr) {
                    showToast(xhr.responseJSON?.message || 'Failed to add to cart', 'error');
                }
            });
        }

        function updateCartCount() {
            $.ajax({
                url: '/cart/count',
                method: 'GET',
                success: function(data) {
                    $('#cart-count').text(data.count);
                }
            });
        }

        function showToast(message, type = 'success') {
            const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
            const toast = $(`
                <div class="toast ${type}">
                    <i class="fas fa-${icon}" style="font-size: 24px; color: ${type === 'success' ? '#28a745' : '#dc3545'}"></i>
                    <div>${message}</div>
                </div>
            `);

            $('#toastContainer').append(toast);
            setTimeout(() => toast.fadeOut(() => toast.remove()), 3000);
        }

        function bindEvents() {
            $('#prev-btn').on('click', function(e) {
                e.preventDefault();
                if (isFlipbookReady) {
                    $('#flipbook').turn('previous');
                }
            });

            $('#next-btn').on('click', function(e) {
                e.preventDefault();
                if (isFlipbookReady) {
                    $('#flipbook').turn('next');
                }
            });

            $('#first-page-btn').on('click', function(e) {
                e.preventDefault();
                showPage(1);
            });

            $('#last-page-btn').on('click', function(e) {
                e.preventDefault();
                showPage(totalPages);
            });

            $('#zoom-in').on('click', function() {
                currentZoom = Math.min(currentZoom + 0.2, 2.5);
                applyZoom();
            });

            $('#zoom-out').on('click', function() {
                currentZoom = Math.max(currentZoom - 0.2, 0.5);
                applyZoom();
            });

            $('#zoom-reset').on('click', function() {
                currentZoom = 1;
                applyZoom();
            });

            $('#fullscreen').on('click', toggleFullscreen);

            // Keyboard navigation
            $(document).on('keydown', function(e) {
                if (!isFlipbookReady) return;

                switch (e.key) {
                    case 'ArrowLeft':
                    case 'PageUp':
                        e.preventDefault();
                        $('#flipbook').turn('previous');
                        break;
                    case 'ArrowRight':
                    case 'PageDown':
                    case ' ':
                        e.preventDefault();
                        $('#flipbook').turn('next');
                        break;
                    case 'Home':
                        e.preventDefault();
                        showPage(1);
                        break;
                    case 'End':
                        e.preventDefault();
                        showPage(totalPages);
                        break;
                }
            });

            // Disable mouse wheel - we want drag only for natural page turning
            $('#flipbook').on('mousewheel DOMMouseScroll', function(e) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            // Add visual feedback when hovering over page corners
            $(document).on('mousemove', '#flipbook', function(e) {
                if (!isFlipbookReady) return;

                const flipbook = $(this);
                const offset = flipbook.offset();
                const width = flipbook.width();
                const height = flipbook.height();
                const x = e.pageX - offset.left;
                const y = e.pageY - offset.top;

                // Check if near corners (within 100px)
                const cornerThreshold = 100;
                const nearLeftEdge = x < cornerThreshold;
                const nearRightEdge = x > width - cornerThreshold;
                const nearTopEdge = y < cornerThreshold;
                const nearBottomEdge = y > height - cornerThreshold;

                if ((nearLeftEdge || nearRightEdge) && (nearTopEdge || nearBottomEdge)) {
                    flipbook.css('cursor', 'grab');
                } else {
                    flipbook.css('cursor', 'default');
                }
            });

            // Show helpful tip on first load
            setTimeout(() => {
                if (!localStorage.getItem('flipbook_tip_shown')) {
                    showToast('💡 Tip: Click and drag page corners to flip pages!', 'success');
                    localStorage.setItem('flipbook_tip_shown', 'true');
                }
            }, 2000);
        }

        function updatePageIndicator(page) {
            $('#current-page').text(page);
        }

        function updateControls() {
            $('#prev-btn, #first-page-btn').prop('disabled', currentPage === 1);
            $('#next-btn, #last-page-btn').prop('disabled', currentPage === totalPages);
        }

        function applyZoom() {
            if (!isFlipbookReady) return;

            const container = $('#flipbook-wrapper');
            const flipbook = $('#flipbook');

            flipbook.css({
                transform: `scale(${currentZoom})`,
                transformOrigin: 'center center',
                transition: 'transform 0.3s ease'
            });

            // Adjust container to accommodate zoom
            const scaledHeight = pageHeight * currentZoom;
            container.css('min-height', scaledHeight + 100 + 'px');

            console.log('Zoom applied:', currentZoom);
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        function generateSessionId() {
            return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        function trackView() {
            $.ajax({
                url: `/api/flipbook/${flipbookSlug}/track/view`,
                method: 'POST',
                data: {
                    session_id: sessionId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        function trackPageTurn(page) {
            $.ajax({
                url: `/api/flipbook/${flipbookSlug}/track/page-turn`,
                method: 'POST',
                data: {
                    page_number: page,
                    session_id: sessionId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        function trackHotspotClick(hotspotId) {
            $.ajax({
                url: `/api/flipbook/${flipbookSlug}/track/hotspot-click`,
                method: 'POST',
                data: {
                    hotspot_id: hotspotId,
                    session_id: sessionId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        function initFlipSound() {
            // Create audio context for page flip sound
            try {
                // Create realistic page turn sound using Web Audio API
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                const audioContext = new AudioContext();

                // Create a more realistic page turn sound effect
                flipSound = {
                    context: audioContext,
                    play: function() {
                        try {
                            // Create noise buffer for paper rustling effect
                            const bufferSize = audioContext.sampleRate * 0.3; // 300ms
                            const buffer = audioContext.createBuffer(1, bufferSize, audioContext.sampleRate);
                            const data = buffer.getChannelData(0);

                            // Generate brown noise (sounds like paper)
                            let lastOut = 0;
                            for (let i = 0; i < bufferSize; i++) {
                                const white = Math.random() * 2 - 1;
                                data[i] = (lastOut + (0.02 * white)) / 1.02;
                                lastOut = data[i];
                                data[i] *= 3.5; // Amplify

                                // Apply envelope for realistic fade in/out
                                const envelope = Math.sin((i / bufferSize) * Math.PI);
                                data[i] *= envelope * 0.15; // Volume control
                            }

                            // Create source and play
                            const source = audioContext.createBufferSource();
                            source.buffer = buffer;

                            // Add filtering for more paper-like quality
                            const filter = audioContext.createBiquadFilter();
                            filter.type = 'bandpass';
                            filter.frequency.value = 2000;
                            filter.Q.value = 1;

                            const gainNode = audioContext.createGain();
                            gainNode.gain.value = 0.3;

                            source.connect(filter);
                            filter.connect(gainNode);
                            gainNode.connect(audioContext.destination);

                            source.start(0);

                        } catch (e) {
                            console.debug('Error playing sound:', e);
                        }
                    }
                };

                console.log('✅ Realistic page flip sound initialized');
            } catch (e) {
                console.warn('⚠️ Could not initialize flip sound:', e);
                // Fallback to simple audio if Web Audio API fails
                try {
                    flipSound = new Audio();
                    flipSound.src = '/assets/media/sounds/page-flip.mp3';
                    flipSound.volume = 0.3;
                    flipSound.load();
                } catch (err) {
                    console.warn('Fallback audio also failed');
                }
            }
        }

        function playFlipSound() {
            if (flipSound) {
                try {
                    if (flipSound.context) {
                        // Web Audio API sound
                        flipSound.play();
                    } else if (flipSound.play) {
                        // HTML5 Audio fallback
                        flipSound.currentTime = 0;
                        flipSound.play().catch(e => {
                            console.debug('Sound play prevented:', e.message);
                        });
                    }
                } catch (e) {
                    console.debug('Error playing sound:', e);
                }
            }
        }
    </script>
</body>

</html>
