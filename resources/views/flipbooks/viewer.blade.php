<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $flipbook->title }} - Interactive Flipbook</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .flipbook-header {
            background: #34495e;
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .flipbook-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 150px);
            padding: 40px 20px;
        }

        #flipbook {
            width: 900px;
            height: 600px;
            margin: 0 auto;
        }

        #flipbook .page {
            width: 450px;
            height: 600px;
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        #flipbook .page img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .flipbook-hotspot {
            position: absolute;
            border: 2px solid #3b82f6;
            background: rgba(59, 130, 246, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .flipbook-hotspot:hover {
            background: rgba(59, 130, 246, 0.4);
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.6);
        }

        .flipbook-hotspot.product {
            border-color: #10b981;
        }

        .flipbook-hotspot.product:hover {
            background: rgba(16, 185, 129, 0.4);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.6);
        }

        .hotspot-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .flipbook-controls {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(52, 73, 94, 0.95);
            padding: 15px 30px;
            border-radius: 50px;
            display: flex;
            gap: 15px;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .control-btn {
            background: #3b82f6;
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .control-btn:hover {
            background: #2563eb;
            transform: scale(1.1);
        }

        .control-btn:disabled {
            background: #6b7280;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .control-btn:disabled:hover {
            transform: none;
        }

        .page-indicator {
            color: white;
            font-weight: bold;
            font-size: 16px;
            padding: 0 20px;
            min-width: 100px;
            text-align: center;
        }

        .zoom-controls {
            position: fixed;
            top: 100px;
            right: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        }

        /* Product Modal */
        .product-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .product-modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            line-height: 20px;
        }

        .close-modal:hover {
            color: #000;
        }

        .fullscreen-mode #flipbook {
            width: 100vw;
            height: 100vh;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 400px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="flipbook-header">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">{{ $flipbook->title }}</h1>
                @if ($flipbook->description)
                    <p class="mb-0 text-light">{{ $flipbook->description }}</p>
                @endif
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-light btn-sm" id="toggle-fullscreen">
                    <i class="fas fa-expand"></i> Fullscreen
                </button>
                @auth
                    @if (auth()->user()->isAdministrator())
                        <a href="{{ route('flipbooks.editor', $flipbook) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit Hotspots
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Flipbook Container -->
    <div class="flipbook-container">
        <div id="flipbook">
            @foreach ($flipbook->pages as $page)
                <div class="page" data-page-id="{{ $page->id }}" data-page-number="{{ $page->page_number }}">
                    <img src="{{ $page->image_url }}" alt="Page {{ $page->page_number }}" />
                </div>
            @endforeach
        </div>
    </div>

    <!-- Controls -->
    <div class="flipbook-controls">
        <button class="control-btn" id="first-page" title="First Page">
            <i class="fas fa-fast-backward"></i>
        </button>
        <button class="control-btn" id="prev-page" title="Previous Page">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="page-indicator">
            <span id="current-page">1</span> / <span id="total-pages">{{ $flipbook->total_pages }}</span>
        </div>
        <button class="control-btn" id="next-page" title="Next Page">
            <i class="fas fa-chevron-right"></i>
        </button>
        <button class="control-btn" id="last-page" title="Last Page">
            <i class="fas fa-fast-forward"></i>
        </button>
    </div>

    <!-- Zoom Controls -->
    <div class="zoom-controls">
        <button class="control-btn" id="zoom-in" title="Zoom In">
            <i class="fas fa-search-plus"></i>
        </button>
        <button class="control-btn" id="zoom-out" title="Zoom Out">
            <i class="fas fa-search-minus"></i>
        </button>
        <button class="control-btn" id="zoom-reset" title="Reset Zoom">
            <i class="fas fa-compress"></i>
        </button>
    </div>

    <!-- Product Modal -->
    <div id="product-modal" class="product-modal">
        <div class="product-modal-content">
            <span class="close-modal">&times;</span>
            <div id="product-details">
                <!-- Product details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/3/turn.min.js"></script>

    <script>
        let currentPageNumber = 1;
        let totalPages = {{ $flipbook->total_pages }};
        let hotspots = [];
        let zoomLevel = 1;

        $(document).ready(function() {
            initializeFlipbook();
            loadHotspots();
            initializeControls();
            trackView();
        });

        function initializeFlipbook() {
            $("#flipbook").turn({
                width: 900,
                height: 600,
                autoCenter: true,
                elevation: 50,
                gradients: true,
                acceleration: true,
                duration: 1000
            });

            $("#flipbook").bind("turned", function(event, page, view) {
                currentPageNumber = page;
                updatePageIndicator();
                renderHotspotsForPage(page);
                trackPageTurn(page);
                updateNavigationButtons();
            });

            // Render initial hotspots
            renderHotspotsForPage(1);
            updateNavigationButtons();
        }

        function loadHotspots() {
            $.ajax({
                url: `/api/flipbook/{{ $flipbook->slug }}/hotspots`,
                method: 'GET',
                success: function(response) {
                    hotspots = Array.isArray(response) ? response : (response.hotspots || []);
                    renderHotspotsForPage(currentPageNumber);
                },
                error: function(xhr) {
                    console.error('Failed to load hotspots', xhr);
                }
            });
        }

        function renderHotspotsForPage(pageNumber) {
            // Remove existing hotspots
            $('.flipbook-hotspot').remove();

            // Get the visible pages
            const pages = $("#flipbook").turn("view");

            pages.forEach(visiblePage => {
                const pageHotspots = hotspots.filter(h => h.page_number == visiblePage || (h.page &&
                    h.page.page_number == visiblePage));

                const pageElement = $(`.page[data-page-number="${visiblePage}"]`);
                if (!pageElement.length) return;

                const img = pageElement.find('img');
                const imgWidth = img.width();
                const imgHeight = img.height();

                pageHotspots.forEach(hotspot => {
                    const left = (hotspot.x * imgWidth) / 100;
                    const top = (hotspot.y * imgHeight) / 100;
                    const width = (hotspot.width * imgWidth) / 100;
                    const height = (hotspot.height * imgHeight) / 100;

                    const hotspotDiv = $('<div>', {
                        class: `flipbook-hotspot ${hotspot.type}`,
                        'data-hotspot-id': hotspot.id,
                        css: {
                            left: left + 'px',
                            top: top + 'px',
                            width: width + 'px', 
                            height: height + 'px',
                            borderColor: hotspot.color || '#3b82f6',
                            background: hexToRgba(hotspot.color || '#3b82f6', hotspot.opacity ||
                                0.3)
                        },
                        title: hotspot.title || ''
                    });

                    // Add icon
                    const icon = getHotspotIcon(hotspot.type);
                    hotspotDiv.append(`<i class="hotspot-icon ${icon}"></i>`);

                    // Add click handler
                    hotspotDiv.click(function(e) {
                        e.stopPropagation();
                        handleHotspotClick(hotspot);
                    });

                    pageElement.append(hotspotDiv);
                });
            });
        }

        function handleHotspotClick(hotspot) {
            // Track click
            trackHotspotClick(hotspot.id);

            switch (hotspot.type) {
                case 'link':
                    if (hotspot.target_url) {
                        window.open(hotspot.target_url, '_blank');
                    }
                    break;
                case 'internal':
                    if (hotspot.target_page) {
                        $("#flipbook").turn("page", hotspot.target_page);
                    }
                    break;
                case 'product':
                    if (hotspot.product_id) {
                        showProductModal(hotspot.product_id);
                    }
                    break;
                case 'video':
                    if (hotspot.target_url) {
                        showVideoModal(hotspot.target_url);
                    }
                    break;
                case 'popup':
                    showPopup(hotspot.description);
                    break;
            }
        }

        function showProductModal(productId) {
            $.ajax({
                url: `/api/products/${productId}`,
                method: 'GET',
                success: function(product) {
                    const html = `
                        <h2 class="mb-3">${product.name}</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <img src="${product.image_url}" class="img-fluid rounded" alt="${product.name}">
                            </div>
                            <div class="col-md-6">
                                <p class="text-muted">SKU: ${product.sku}</p>
                                <h3 class="text-success mb-3">${product.formatted_price}</h3>
                                <p>${product.description || ''}</p>
                                ${product.is_in_stock ? `
                                        <button class="btn btn-primary btn-lg w-100" onclick="addToCart(${product.id})">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    ` : '<p class="text-danger">Out of Stock</p>'}
                            </div>
                        </div>
                    `;
                    $('#product-details').html(html);
                    $('#product-modal').fadeIn();
                },
                error: function() {
                    alert('Failed to load product details');
                }
            });
        }

        function showVideoModal(url) {
            // Implement video modal if needed
            window.open(url, '_blank');
        }

        function showPopup(content) {
            alert(content);
        }

        function addToCart(productId) {
            $.ajax({
                url: '/api/cart/add',
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: 1
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    alert('Product added to cart!');
                    $('#product-modal').fadeOut();
                },
                error: function() {
                    alert('Failed to add product to cart');
                }
            });
        }

        function initializeControls() {
            $('#prev-page').click(function() {
                $("#flipbook").turn("previous");
            });

            $('#next-page').click(function() {
                $("#flipbook").turn("next");
            });

            $('#first-page').click(function() {
                $("#flipbook").turn("page", 1);
            });

            $('#last-page').click(function() {
                $("#flipbook").turn("page", totalPages);
            });

            // Keyboard navigation
            $(document).keydown(function(e) {
                if (e.keyCode == 37) $("#flipbook").turn("previous");
                if (e.keyCode == 39) $("#flipbook").turn("next");
            });

            // Zoom controls
            $('#zoom-in').click(function() {
                zoomLevel = Math.min(zoomLevel + 0.2, 2);
                applyZoom();
            });

            $('#zoom-out').click(function() {
                zoomLevel = Math.max(zoomLevel - 0.2, 0.5);
                applyZoom();
            });

            $('#zoom-reset').click(function() {
                zoomLevel = 1;
                applyZoom();
            });

            // Fullscreen toggle
            $('#toggle-fullscreen').click(function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                    $(this).html('<i class="fas fa-compress"></i> Exit Fullscreen');
                } else {
                    document.exitFullscreen();
                    $(this).html('<i class="fas fa-expand"></i> Fullscreen');
                }
            });

            // Close modal
            $('.close-modal').click(function() {
                $('#product-modal').fadeOut();
            });

            $(window).click(function(e) {
                if (e.target.id === 'product-modal') {
                    $('#product-modal').fadeOut();
                }
            });
        }

        function applyZoom() {
            $('#flipbook').css('transform', `scale(${zoomLevel})`);
        }

        function updatePageIndicator() {
            $('#current-page').text(currentPageNumber);
        }

        function updateNavigationButtons() {
            $('#prev-page, #first-page').prop('disabled', currentPageNumber <= 1);
            $('#next-page, #last-page').prop('disabled', currentPageNumber >= totalPages);
        }

        function getHotspotIcon(type) {
            const icons = {
                'link': 'fas fa-external-link-alt',
                'internal': 'fas fa-arrow-right',
                'product': 'fas fa-shopping-bag',
                'video': 'fas fa-play-circle',
                'popup': 'fas fa-info-circle'
            };
            return icons[type] || 'fas fa-hand-pointer';
        }

        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        // Analytics
        function trackView() {
            $.post('/api/flipbook/{{ $flipbook->slug }}/track/view', {
                _token: $('meta[name="csrf-token"]').attr('content')
            });
        }

        function trackPageTurn(page) {
            $.post('/api/flipbook/{{ $flipbook->slug }}/track/page-turn', {
                page_number: page,
                _token: $('meta[name="csrf-token"]').attr('content')
            });
        }

        function trackHotspotClick(hotspotId) {
            $.post(`/api/flipbooks/hotspots/${hotspotId}/track`, {
                _token: $('meta[name="csrf-token"]').attr('content')
            });
        }
    </script>
</body>

</html>
