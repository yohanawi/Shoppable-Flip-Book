<?php
// Quick test script to check hotspots
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Flipbook Viewer Test ===\n\n";

// Check flipbooks
$flipbooks = \App\Models\Flipbook::with('pages')->get();
echo "Total Flipbooks: " . $flipbooks->count() . "\n\n";

foreach ($flipbooks as $flipbook) {
    echo "Flipbook: {$flipbook->title} (ID: {$flipbook->id}, Slug: {$flipbook->slug})\n";
    echo "  PDF Path: {$flipbook->pdf_path}\n";
    echo "  Published: " . ($flipbook->is_published ? 'Yes' : 'No') . "\n";
    echo "  Pages: " . $flipbook->pages->count() . "\n";

    // Check hotspots
    $pageHotspots = \App\Models\FlipbookHotspot::whereIn(
        'flipbook_page_id',
        $flipbook->pages->pluck('id')
    )->get();

    $directHotspots = \App\Models\FlipbookHotspot::where('flipbook_id', $flipbook->id)
        ->whereNotNull('page_number')
        ->get();

    $totalHotspots = $pageHotspots->count() + $directHotspots->count();
    echo "  Hotspots: {$totalHotspots}\n";

    if ($totalHotspots > 0) {
        echo "  --- Hotspot Details ---\n";

        foreach ($pageHotspots as $h) {
            $page = $flipbook->pages->firstWhere('id', $h->flipbook_page_id);
            echo "    - Page {$page->page_number}: {$h->type} - {$h->title} / {$h->product_name}\n";
        }

        foreach ($directHotspots as $h) {
            echo "    - Page {$h->page_number}: {$h->type} - {$h->title} / {$h->product_name}\n";
        }
    }

    echo "  Viewer URL: http://127.0.0.1:8000/flipbook/{$flipbook->slug}\n";
    echo "  Editor URL: http://127.0.0.1:8000/flipbooks/{$flipbook->id}/editor\n";
    echo "\n";
}
