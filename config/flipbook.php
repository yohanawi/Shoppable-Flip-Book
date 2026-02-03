<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Flipbook Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the flipbook system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | PDF Upload Settings
    |--------------------------------------------------------------------------
    */
    'upload' => [
        // Maximum file size in kilobytes (50MB default)
        'max_file_size' => env('FLIPBOOK_MAX_FILE_SIZE', 51200),

        // Allowed file types
        'allowed_types' => ['pdf'],

        // Storage disk
        'disk' => 'public',

        // Base path for flipbook files
        'path' => 'flipbooks',
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Conversion Settings
    |--------------------------------------------------------------------------
    */
    'conversion' => [
        // Image resolution in DPI (72-300, higher = better quality but larger files)
        'resolution' => env('FLIPBOOK_IMAGE_RESOLUTION', 150),

        // Image format (png, jpg)
        'format' => 'png',

        // Compression quality for images (1-100)
        'quality' => env('FLIPBOOK_COMPRESSION_QUALITY', 90),

        // Generate thumbnails
        'generate_thumbnails' => true,

        // Thumbnail dimensions
        'thumbnail_width' => 300,
        'thumbnail_height' => 400,

        // Background processing (use queue)
        'use_queue' => env('FLIPBOOK_USE_QUEUE', false),

        // Queue name
        'queue_name' => 'flipbooks',
    ],

    /*
    |--------------------------------------------------------------------------
    | Viewer Settings
    |--------------------------------------------------------------------------
    */
    'viewer' => [
        // Default template
        'default_template' => 'classic',

        // Enable zoom controls
        'enable_zoom' => true,

        // Enable fullscreen
        'enable_fullscreen' => true,

        // Enable keyboard navigation
        'enable_keyboard' => true,

        // Enable download by default
        'allow_download' => true,

        // Enable analytics tracking
        'track_analytics' => true,

        // Page turn animation duration (milliseconds)
        'animation_duration' => 1000,

        // Auto-center pages
        'auto_center' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Hotspot Settings
    |--------------------------------------------------------------------------
    */
    'hotspots' => [
        // Default hotspot color
        'default_color' => '#3b82f6',

        // Available hotspot types
        'types' => [
            'external' => 'External Link',
            'internal' => 'Internal Link',
            'product' => 'Product',
            'popup' => 'Popup',
            'video' => 'Video',
        ],

        // Available animations
        'animations' => [
            '' => 'None',
            'pulse' => 'Pulse',
            'bounce' => 'Bounce',
            'shake' => 'Shake',
        ],

        // Target behaviors
        'target_types' => [
            '_blank' => 'New Tab',
            '_self' => 'Same Tab',
            'modal' => 'Modal',
            'cart' => 'Add to Cart',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Settings
    |--------------------------------------------------------------------------
    */
    'analytics' => [
        // Enable analytics tracking
        'enabled' => true,

        // Events to track
        'track_events' => [
            'view',
            'page_turn',
            'hotspot_click',
            'download',
            'share',
        ],

        // Store IP addresses
        'store_ip' => true,

        // Store user agents
        'store_user_agent' => true,

        // Data retention days (null = forever)
        'retention_days' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        // Enable caching
        'enabled' => true,

        // Cache TTL in minutes
        'ttl' => 60,

        // Cache key prefix
        'prefix' => 'flipbook',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        // Require authentication for viewing private flipbooks
        'require_auth_for_private' => true,

        // Rate limiting (requests per minute)
        'rate_limit' => 60,

        // Allowed HTML tags in popup content
        'allowed_html_tags' => '<p><br><strong><em><u><a><img><h1><h2><h3><h4><ul><ol><li>',

        // Sanitize HTML content
        'sanitize_html' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Settings
    |--------------------------------------------------------------------------
    */
    'seo' => [
        // Generate sitemap
        'generate_sitemap' => true,

        // Default meta description
        'default_description' => 'View our interactive flipbook catalog',

        // Include in sitemap (only published flipbooks)
        'sitemap_only_published' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'features' => [
        // Enable commenting on pages
        'comments' => false,

        // Enable sharing
        'sharing' => true,

        // Enable email sharing
        'email_share' => false,

        // Enable social media sharing
        'social_share' => true,

        // Enable search within flipbook
        'search' => false,

        // Enable bookmarks
        'bookmarks' => false,

        // Enable version history
        'versions' => false,

        // Enable collaborative editing
        'collaboration' => false,
    ],

];
