<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $flipbook->title }}</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        #flipbook-container {
            width: 90%;
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
            background: #34495e;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        #flipbook-header {
            text-align: center;
            color: #ecf0f1;
            margin-bottom: 20px;
        }

        #flipbook-header h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        #flipbook-wrapper {
            position: relative;
            width: 100%;
            height: 700px;
            margin: 0 auto;
        }

        #flipbook {
            width: 100%;
            height: 100%;
        }

        .page {
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .page img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .hotspot {
            position: absolute;
            border: 2px solid transparent;
            background: rgba(59, 130, 246, 0.2);
            cursor: pointer;
            transition: all 0.3s;
            z-index: 10;
        }

        .hotspot:hover {
            background: rgba(59, 130, 246, 0.4);
            border-color: #3b82f6;
            transform: scale(1.05);
        }

        .hotspot.pulse {
            animation: pulse 2s infinite;
        }

        .hotspot.bounce {
            animation: bounce 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .hotspot-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
            white-space: nowrap;
            z-index: 1000;
            pointer-events: none;
            display: none;
        }

        #controls {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #2c3e50;
            border-radius: 8px;
        }

        #controls button {
            margin: 0 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: #3498db;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }

        #controls button:hover {
            background: #2980b9;
        }

        #controls button:disabled {
            background: #7f8c8d;
            cursor: not-allowed;
        }

        #page-indicator {
            display: inline-block;
            margin: 0 20px;
            color: #ecf0f1;
            font-weight: bold;
        }

        .zoom-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 100;
        }

        .zoom-controls button {
            display: block;
            width: 40px;
            height: 40px;
            margin-bottom: 5px;
            border: none;
            border-radius: 50%;
            background: rgba(52, 152, 219, 0.9);
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .zoom-controls button:hover {
            background: #2980b9;
            transform: scale(1.1);
        }

        /* Modal */
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #flipbook-wrapper {
                height: 500px;
            }

            #flipbook-header h1 {
                font-size: 1.5rem;
            }
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
                <div class="spinner-border text-light" role="status">
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

            <!-- Flipbook -->
            <div id="flipbook">
                @foreach ($flipbook->pages as $page)
                    <div class="page" data-page-id="{{ $page->id }}" data-page-number="{{ $page->page_number }}">
                        <img src="{{ $page->getImageUrl() }}" alt="Page {{ $page->page_number }}" />

                        <!-- Hotspots -->
                        @foreach ($page->activeHotspots as $hotspot)
                            <div class="hotspot {{ $hotspot->animation }}"
                                style="{{ $hotspot->style }} background-color: {{ $hotspot->color }}33;"
                                data-hotspot-id="{{ $hotspot->id }}" data-type="{{ $hotspot->type }}"
                                data-target="{{ $hotspot->getTargetUrl() }}"
                                data-target-type="{{ $hotspot->target_type }}" data-title="{{ $hotspot->title }}"
                                data-description="{{ $hotspot->description }}"
                                data-popup-content="{{ $hotspot->popup_content }}">
                                @if ($hotspot->icon)
                                    <i class="fas fa-{{ $hotspot->icon }}" style="color: {{ $hotspot->color }};"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Controls -->
        <div id="controls">
            <button id="prev-btn"><i class="fas fa-chevron-left"></i> Previous</button>
            <span id="page-indicator">Page <span id="current-page">1</span> of <span
                    id="total-pages">{{ $flipbook->total_pages }}</span></span>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/3/turn.min.js"></script>

    <script>
        const flipbookId = {{ $flipbook->id }};
        const flipbookSlug = '{{ $flipbook->slug }}';
        let currentZoom = 1;
        let sessionId = generateSessionId();

        $(document).ready(function() {
            initializeFlipbook();
            bindEvents();
            trackView();
        });

        function initializeFlipbook() {
            $('#flipbook').turn({
                width: 1200,
                height: 700,
                autoCenter: true,
                duration: 1000,
                gradients: true,
                elevation: 50,
                pages: {{ $flipbook->total_pages }},
                when: {
                    turning: function(event, page, view) {
                        updatePageIndicator(page);
                    },
                    turned: function(event, page, view) {
                        trackPageTurn(page);
                    }
                }
            });

            $('.loading-spinner').fadeOut();
            updateControls();
        }

        function bindEvents() {
            // Navigation buttons
            $('#prev-btn').click(function() {
                $('#flipbook').turn('previous');
            });

            $('#next-btn').click(function() {
                $('#flipbook').turn('next');
            });

            $('#first-page-btn').click(function() {
                $('#flipbook').turn('page', 1);
            });

            $('#last-page-btn').click(function() {
                $('#flipbook').turn('page', {{ $flipbook->total_pages }});
            });

            // Zoom controls
            $('#zoom-in').click(function() {
                zoomIn();
            });

            $('#zoom-out').click(function() {
                zoomOut();
            });

            $('#zoom-reset').click(function() {
                resetZoom();
            });

            $('#fullscreen').click(function() {
                toggleFullscreen();
            });

            // Download
            $('#download-btn').click(function() {
                window.location.href = `/flipbook/${flipbookSlug}/download`;
            });

            // Keyboard navigation
            $(document).keydown(function(e) {
                if (e.keyCode === 37) { // Left arrow
                    $('#flipbook').turn('previous');
                } else if (e.keyCode === 39) { // Right arrow
                    $('#flipbook').turn('next');
                }
            });

            // Hotspot interactions
            $(document).on('click', '.hotspot', function(e) {
                e.stopPropagation();
                handleHotspotClick($(this));
            });

            $(document).on('mouseenter', '.hotspot', function() {
                showHotspotTooltip($(this));
            });

            $(document).on('mouseleave', '.hotspot', function() {
                hideHotspotTooltip();
            });

            // Update controls on page change
            $('#flipbook').bind('turned', function() {
                updateControls();
            });
        }

        function updatePageIndicator(page) {
            $('#current-page').text(page);
        }

        function updateControls() {
            const page = $('#flipbook').turn('page');
            const totalPages = $('#flipbook').turn('pages');

            $('#prev-btn').prop('disabled', page === 1);
            $('#first-page-btn').prop('disabled', page === 1);
            $('#next-btn').prop('disabled', page === totalPages);
            $('#last-page-btn').prop('disabled', page === totalPages);
        }

        function handleHotspotClick($hotspot) {
            const hotspotId = $hotspot.data('hotspot-id');
            const type = $hotspot.data('type');
            const target = $hotspot.data('target');
            const targetType = $hotspot.data('target-type');
            const title = $hotspot.data('title');
            const description = $hotspot.data('description');
            const popupContent = $hotspot.data('popup-content');

            // Track click
            trackHotspotClick(hotspotId);

            // Handle based on type
            if (targetType === 'modal' || type === 'popup') {
                $('#hotspotModalTitle').text(title || 'Details');
                $('#hotspotModalBody').html(popupContent || description || 'No content available');
                new bootstrap.Modal(document.getElementById('hotspotModal')).show();
            } else if (targetType === 'cart') {
                // Add to cart logic (implement based on your cart system)
                alert('Add to cart functionality - implement based on your system');
            } else if (targetType === '_blank') {
                window.open(target, '_blank');
            } else {
                window.location.href = target;
            }
        }

        function showHotspotTooltip($hotspot) {
            const title = $hotspot.data('title');
            if (!title) return;

            const $tooltip = $('#hotspot-tooltip');
            $tooltip.text(title);

            const offset = $hotspot.offset();
            $tooltip.css({
                top: offset.top - 30,
                left: offset.left + ($hotspot.width() / 2) - ($tooltip.width() / 2),
                display: 'block'
            });
        }

        function hideHotspotTooltip() {
            $('#hotspot-tooltip').hide();
        }

        function zoomIn() {
            currentZoom += 0.1;
            applyZoom();
        }

        function zoomOut() {
            if (currentZoom > 0.5) {
                currentZoom -= 0.1;
                applyZoom();
            }
        }

        function resetZoom() {
            currentZoom = 1;
            applyZoom();
        }

        function applyZoom() {
            $('#flipbook').css('transform', `scale(${currentZoom})`);
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.getElementById('flipbook-container').requestFullscreen();
                $('#fullscreen i').removeClass('fa-expand-arrows-alt').addClass('fa-compress-arrows-alt');
            } else {
                document.exitFullscreen();
                $('#fullscreen i').removeClass('fa-compress-arrows-alt').addClass('fa-expand-arrows-alt');
            }
        }

        // Analytics functions
        function trackView() {
            $.post(`/api/flipbook/${flipbookSlug}/track/view`, {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId
            });
        }

        function trackPageTurn(page) {
            const $page = $(`.page[data-page-number="${page}"]`);
            const pageId = $page.data('page-id');

            $.post(`/api/flipbook/${flipbookSlug}/track/page-turn`, {
                _token: $('meta[name="csrf-token"]').attr('content'),
                page_id: pageId,
                page_number: page,
                session_id: sessionId
            });
        }

        function trackHotspotClick(hotspotId) {
            $.post(`/api/flipbooks/hotspots/${hotspotId}/track`, {
                _token: $('meta[name="csrf-token"]').attr('content'),
                session_id: sessionId
            });
        }

        function generateSessionId() {
            return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
    </script>
</body>

</html>
