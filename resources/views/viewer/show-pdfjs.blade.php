<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $flipbook->title }}</title>

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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        #flipbook-container {
            max-width: 1400px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        #flipbook-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        #flipbook-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        #flipbook-wrapper {
            position: relative;
            padding: 40px 20px;
            background: #f8f9fa;
            min-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* PDF.js Viewer Styles */
        #pdf-viewer {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }

        .page-container {
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: white;
            position: relative;
            display: none;
        }

        .page-container.active {
            display: block;
        }

        .page-container canvas {
            display: block;
            width: 100% !important;
            height: auto !important;
            max-width: 100%;
        }

        /* Hotspots */
        .hotspot {
            position: absolute;
            border: 2px solid;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            z-index: 10;
        }

        .hotspot:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .hotspot.pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        /* Controls */
        #controls {
            background: white;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        #controls button {
            padding: 12px 24px;
            margin: 5px;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }

        #controls button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        #controls button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        #page-indicator {
            display: inline-block;
            padding: 12px 24px;
            background: #f8f9fa;
            border-radius: 8px;
            font-weight: 600;
            color: #495057;
            margin: 0 10px;
        }

        .zoom-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 100;
        }

        .zoom-controls button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .zoom-controls button:hover {
            background: #667eea;
            color: white;
            transform: scale(1.1);
        }

        /* Loading */
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
        }

        /* Hotspot Tooltip */
        .hotspot-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            pointer-events: none;
            z-index: 1000;
            display: none;
            max-width: 300px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .hotspot-tooltip::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid rgba(0, 0, 0, 0.9);
        }

        /* Alert Banner */
        .alert-banner {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px 20px;
            margin: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-banner i {
            color: #ffc107;
            font-size: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #flipbook-header h1 {
                font-size: 1.5rem;
            }

            #controls button {
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <div id="flipbook-container">
        <!-- Header -->
        <div id="flipbook-header">
            <h1>{{ $flipbook->title }}</h1>
            @if ($flipbook->description)
                <p class="text-muted">{{ $flipbook->description }}</p>
            @endif
        </div>

        <!-- Flipbook Wrapper -->
        <div id="flipbook-wrapper">
            <div class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Zoom Controls -->
            <div class="zoom-controls">
                <button id="zoom-in" title="Zoom In"><i class="fas fa-plus"></i></button>
                <button id="zoom-out" title="Zoom Out"><i class="fas fa-minus"></i></button>
                <button id="zoom-reset" title="Reset Zoom"><i class="fas fa-expand"></i></button>
                <button id="fullscreen" title="Fullscreen"><i class="fas fa-expand-arrows-alt"></i></button>
            </div>

            <!-- PDF Viewer Container -->
            <div id="pdf-viewer">
                <!-- Pages will be rendered here -->
            </div>
        </div>

        <!-- Controls -->
        <div id="controls">
            <button id="prev-btn"><i class="fas fa-chevron-left"></i> Previous</button>
            <span id="page-indicator">Page <span id="current-page">1</span> of <span id="total-pages">0</span></span>
            <button id="next-btn">Next <i class="fas fa-chevron-right"></i></button>

            <div class="mt-3">
                <button id="first-page-btn"><i class="fas fa-fast-backward"></i> First</button>
                <button id="last-page-btn"><i class="fas fa-fast-forward"></i> Last</button>
                @if (isset($flipbook->settings['allow_download']) && $flipbook->settings['allow_download'])
                    <button id="download-btn"><i class="fas fa-download"></i> Download PDF</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Hotspot Modal -->
    <div class="modal fade" id="hotspotModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hotspotModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="hotspotModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tooltip -->
    <div class="hotspot-tooltip" id="hotspot-tooltip"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        const flipbookId = {{ $flipbook->id }};
        const flipbookSlug = '{{ $flipbook->slug }}';
        const pdfUrl = '{{ asset('storage/' . $flipbook->pdf_path) }}';
        let currentPage = 1;
        let totalPages = 0;
        let pdfDoc = null;
        let currentZoom = 1;
        let sessionId = generateSessionId();
        let pageRendered = false;
        let hotspots = [];

        // Configure PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        console.log('Flipbook ID:', flipbookId);
        console.log('PDF URL:', pdfUrl);

        $(document).ready(function() {
            loadPdf();
            bindEvents();
            trackView();
            loadHotspots();
        });

        function loadPdf() {
            console.log('Loading PDF from:', pdfUrl);

            pdfjsLib.getDocument({
                url: pdfUrl,
                cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
                cMapPacked: true,
                withCredentials: false
            }).promise.then(function(pdf) {
                console.log('PDF loaded successfully, pages:', pdf.numPages);
                pdfDoc = pdf;
                totalPages = pdf.numPages;
                $('#total-pages').text(totalPages);

                // Render first page
                renderPage(1);
                updateControls();
                $('.loading-spinner').fadeOut();
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                console.error('Error details:', {
                    name: error.name,
                    message: error.message,
                    stack: error.stack
                });

                let errorMessage = '<div class="alert alert-danger m-5">';
                errorMessage += '<h4><i class="fas fa-exclamation-triangle"></i> Failed to Load PDF</h4>';
                errorMessage += '<p><strong>Error:</strong> ' + error.message + '</p>';
                errorMessage += '<p><strong>PDF URL:</strong> <a href="' + pdfUrl + '" target="_blank">' + pdfUrl +
                    '</a></p>';
                errorMessage += '<p>Please check:</p>';
                errorMessage += '<ul>';
                errorMessage += '<li>The PDF file exists in storage</li>';
                errorMessage +=
                    '<li>Storage symlink is properly configured (<code>php artisan storage:link</code>)</li>';
                errorMessage += '<li>The PDF is not corrupted</li>';
                errorMessage += '<li>Your browser supports PDF.js</li>';
                errorMessage += '</ul>';
                errorMessage +=
                    '<a href="{{ route('flipbook.test') }}" class="btn btn-primary mt-3">Run System Diagnostic</a>';
                errorMessage += '</div>';

                $('.loading-spinner').html(errorMessage);
            });
        }

        function renderPage(pageNum) {
            if (!pdfDoc) return;

            pdfDoc.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({
                    scale: currentZoom * 1.5
                });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                canvas.height = viewport.height;
                canvas.width = viewport.width;
                canvas.className = 'pdf-page';

                // Create page container
                const pageContainer = document.createElement('div');
                pageContainer.className = 'page-container';
                pageContainer.id = `page-${pageNum}`;
                pageContainer.dataset.pageNumber = pageNum;
                pageContainer.appendChild(canvas);

                // Clear and add to viewer
                if (pageNum === 1 || !pageRendered) {
                    $('#pdf-viewer').empty();
                }
                $('#pdf-viewer').append(pageContainer);

                // Render PDF page on canvas
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };

                page.render(renderContext).promise.then(function() {
                    pageContainer.classList.add('active');
                    renderHotspotsForPage(pageNum, pageContainer);
                    pageRendered = true;
                });
            });
        }

        function showPage(pageNum) {
            if (pageNum < 1 || pageNum > totalPages) return;

            // Hide all pages
            $('.page-container').removeClass('active');

            // Check if page is already rendered
            const existingPage = $(`#page-${pageNum}`);
            if (existingPage.length > 0) {
                existingPage.addClass('active');
                currentPage = pageNum;
                updatePageIndicator(pageNum);
                trackPageTurn(pageNum);
            } else {
                // Render the page if not already rendered
                renderPage(pageNum);
                currentPage = pageNum;
                updatePageIndicator(pageNum);
                trackPageTurn(pageNum);
            }

            updateControls();
        }

        function loadHotspots() {
            $.ajax({
                url: `/api/flipbooks/${flipbookSlug}/hotspots`,
                method: 'GET',
                success: function(data) {
                    hotspots = data;
                },
                error: function(xhr) {
                    console.error('Failed to load hotspots:', xhr);
                }
            });
        }

        function renderHotspotsForPage(pageNum, container) {
            const pageHotspots = hotspots.filter(h => h.page_number === pageNum);

            pageHotspots.forEach(hotspot => {
                const hotspotEl = $('<div>')
                    .addClass('hotspot')
                    .addClass(hotspot.animation || '')
                    .css({
                        left: hotspot.x_position + '%',
                        top: hotspot.y_position + '%',
                        width: hotspot.width + '%',
                        height: hotspot.height + '%',
                        borderColor: hotspot.color || '#667eea',
                        backgroundColor: (hotspot.color || '#667eea') + '33'
                    })
                    .attr('data-hotspot-id', hotspot.id)
                    .attr('data-type', hotspot.type)
                    .attr('data-title', hotspot.title)
                    .attr('data-description', hotspot.description);

                if (hotspot.icon) {
                    hotspotEl.html(`<i class="fas fa-${hotspot.icon}" style="color: ${hotspot.color}"></i>`);
                }

                // Click handler
                hotspotEl.on('click', function() {
                    handleHotspotClick(hotspot);
                });

                // Tooltip
                hotspotEl.on('mouseenter', function(e) {
                    showTooltip(e, hotspot.title, hotspot.description);
                });

                hotspotEl.on('mouseleave', function() {
                    hideTooltip();
                });

                $(container).append(hotspotEl);
            });
        }

        function bindEvents() {
            // Navigation
            $('#prev-btn').on('click', function() {
                showPage(currentPage - 1);
            });

            $('#next-btn').on('click', function() {
                showPage(currentPage + 1);
            });

            $('#first-page-btn').on('click', function() {
                showPage(1);
            });

            $('#last-page-btn').on('click', function() {
                showPage(totalPages);
            });

            // Zoom controls
            $('#zoom-in').on('click', function() {
                currentZoom = Math.min(currentZoom + 0.25, 3);
                renderPage(currentPage);
            });

            $('#zoom-out').on('click', function() {
                currentZoom = Math.max(currentZoom - 0.25, 0.5);
                renderPage(currentPage);
            });

            $('#zoom-reset').on('click', function() {
                currentZoom = 1;
                renderPage(currentPage);
            });

            $('#fullscreen').on('click', function() {
                toggleFullscreen();
            });

            // Download
            $('#download-btn').on('click', function() {
                downloadPdf();
            });

            // Keyboard navigation
            $(document).on('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    showPage(currentPage - 1);
                } else if (e.key === 'ArrowRight') {
                    showPage(currentPage + 1);
                }
            });
        }

        function updatePageIndicator(page) {
            $('#current-page').text(page);
        }

        function updateControls() {
            $('#prev-btn, #first-page-btn').prop('disabled', currentPage === 1);
            $('#next-btn, #last-page-btn').prop('disabled', currentPage === totalPages);
        }

        function handleHotspotClick(hotspot) {
            // Track click
            trackHotspotClick(hotspot.id);

            if (hotspot.type === 'link') {
                window.open(hotspot.target_url, '_blank');
            } else if (hotspot.type === 'popup') {
                showHotspotModal(hotspot);
            } else if (hotspot.type === 'product') {
                // Handle product hotspot - could redirect to product page or add to cart
                window.location.href = `/products/${hotspot.product_id}`;
            }
        }

        function showHotspotModal(hotspot) {
            $('#hotspotModalTitle').text(hotspot.title);
            $('#hotspotModalBody').html(hotspot.popup_content || hotspot.description);
            new bootstrap.Modal($('#hotspotModal')).show();
        }

        function showTooltip(event, title, description) {
            const tooltip = $('#hotspot-tooltip');
            tooltip.html(`<strong>${title}</strong>${description ? '<br>' + description : ''}`);
            tooltip.css({
                left: event.pageX - tooltip.outerWidth() / 2,
                top: event.pageY - tooltip.outerHeight() - 15,
                display: 'block'
            });
        }

        function hideTooltip() {
            $('#hotspot-tooltip').hide();
        }

        function toggleFullscreen() {
            const elem = document.getElementById('flipbook-container');

            if (!document.fullscreenElement) {
                elem.requestFullscreen().catch(err => {
                    console.error('Error attempting to enable fullscreen:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }

        function downloadPdf() {
            window.location.href = `/flipbook/${flipbookSlug}/download`;
        }

        // Analytics functions
        function generateSessionId() {
            return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }

        function trackView() {
            $.ajax({
                url: `/api/flipbooks/${flipbookSlug}/track/view`,
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
                url: `/api/flipbooks/${flipbookSlug}/track/page-turn`,
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
                url: `/api/flipbooks/${flipbookSlug}/track/hotspot-click`,
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
    </script>
</body>

</html>
