<?php

namespace App\Http\Controllers;

use App\Models\Flipbook;
use App\Models\FlipbookAnalytic;
use App\Models\FlipbookHotspot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FlipbookViewerController extends Controller
{
    /**
     * Display the flipbook viewer
     */
    public function show($slug)
    {
        $flipbook = Flipbook::where('slug', $slug)
            ->published()
            ->with(['pages.activeHotspots', 'template'])
            ->firstOrFail();

        // Check if user has permission to view
        if (!$flipbook->is_public && !Auth::check()) {
            abort(403, 'This flipbook is private.');
        }

        // Verify PDF file exists
        $pdfPath = storage_path('app/public/' . $flipbook->pdf_path);
        if (!file_exists($pdfPath)) {
            Log::error('PDF file not found for flipbook: ' . $flipbook->id, [
                'pdf_path' => $flipbook->pdf_path,
                'full_path' => $pdfPath
            ]);
            abort(404, 'PDF file not found. Please contact administrator.');
        }

        // Log view event
        FlipbookAnalytic::logEvent($flipbook->id, FlipbookAnalytic::EVENT_VIEW);

        // Increment views count
        $flipbook->incrementViews();

        // Use enhanced viewer (PDF.js + Turn.js page flip)
        return view('viewer.show-flip-enhanced', compact('flipbook'));
    }

    /**
     * Get flipbook data as JSON (for AJAX loading)  
     */
    public function data($slug)
    {
        $flipbook = Flipbook::where('slug', $slug)
            ->published()
            ->with(['pages.activeHotspots'])
            ->firstOrFail();

        $pages = $flipbook->pages->map(function ($page) {
            return [
                'id' => $page->id,
                'number' => $page->page_number,
                'image' => $page->getImageUrl(),
                'thumbnail' => $page->getThumbnailUrl(),
                'width' => $page->width,
                'height' => $page->height,
                'hotspots' => $page->activeHotspots->map(function ($hotspot) {
                    return [
                        'id' => $hotspot->id,
                        'type' => $hotspot->type,
                        'title' => $hotspot->title,
                        'description' => $hotspot->description,
                        'x' => $hotspot->x_position,
                        'y' => $hotspot->y_position,
                        'width' => $hotspot->width,
                        'height' => $hotspot->height,
                        'target_url' => $hotspot->getTargetUrl(),
                        'target_type' => $hotspot->target_type,
                        'icon' => $hotspot->icon,
                        'color' => $hotspot->color,
                        'animation' => $hotspot->animation,
                        'popup_content' => $hotspot->popup_content,
                    ];
                }),
            ];
        });

        return response()->json([
            'id' => $flipbook->id,
            'title' => $flipbook->title,
            'description' => $flipbook->description,
            'total_pages' => $flipbook->total_pages,
            'template' => $flipbook->template?->slug,
            'settings' => $flipbook->settings,
            'pages' => $pages,
        ]);
    }

    /**
     * Track page turn event
     */
    public function trackPageTurn(Request $request, $slug)
    {
        $flipbook = Flipbook::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'page_number' => 'required|integer',
            'session_id' => 'nullable|string',
        ]);

        FlipbookAnalytic::logEvent($flipbook->id, FlipbookAnalytic::EVENT_PAGE_TURN, [
            'metadata' => ['page_number' => $validated['page_number']],
            'session_id' => $validated['session_id'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get all hotspots for a flipbook (for PDF.js viewer)
     */
    public function getHotspots($slug)
    {
        $flipbook = Flipbook::where('slug', $slug)
            ->published()
            ->with(['pages.activeHotspots'])
            ->firstOrFail();

        $hotspots = [];

        // Get hotspots from pages (Imagick-based)
        foreach ($flipbook->pages as $page) {
            foreach ($page->activeHotspots as $hotspot) {
                $hotspots[] = [
                    'id' => $hotspot->id,
                    'page_number' => $page->page_number,
                    'type' => $hotspot->type,
                    'title' => $hotspot->title,
                    'description' => $hotspot->description,
                    'x_position' => $hotspot->x_position,
                    'y_position' => $hotspot->y_position,
                    'width' => $hotspot->width,
                    'height' => $hotspot->height,
                    'target_url' => $hotspot->getTargetUrl(),
                    'target_type' => $hotspot->target_type,
                    'product_id' => $hotspot->product_id,
                    'product_name' => $hotspot->product_name,
                    'icon' => $hotspot->icon,
                    'color' => $hotspot->color,
                    'animation' => $hotspot->animation,
                    'popup_content' => $hotspot->popup_content,
                ];
            }
        }

        // Get hotspots with direct page numbers (PDF.js-based)
        $directHotspots = FlipbookHotspot::where('flipbook_id', $flipbook->id)
            ->where('is_active', true)
            ->whereNotNull('page_number')
            ->get();

        foreach ($directHotspots as $hotspot) {
            $hotspots[] = [
                'id' => $hotspot->id,
                'page_number' => $hotspot->page_number,
                'type' => $hotspot->type,
                'title' => $hotspot->title,
                'description' => $hotspot->description,
                'x_position' => $hotspot->x_position,
                'y_position' => $hotspot->y_position,
                'width' => $hotspot->width,
                'height' => $hotspot->height,
                'target_url' => $hotspot->getTargetUrl(),
                'target_type' => $hotspot->target_type,
                'product_id' => $hotspot->product_id,
                'product_name' => $hotspot->product_name,
                'icon' => $hotspot->icon,
                'color' => $hotspot->color,
                'animation' => $hotspot->animation,
                'popup_content' => $hotspot->popup_content,
            ];
        }

        return response()->json($hotspots);
    }

    /**
     * Track hotspot click event
     */
    public function trackHotspotClick(Request $request, $slug)
    {
        $flipbook = Flipbook::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'hotspot_id' => 'required|exists:flipbook_hotspots,id',
            'session_id' => 'nullable|string',
        ]);

        FlipbookAnalytic::logEvent($flipbook->id, FlipbookAnalytic::EVENT_HOTSPOT_CLICK, [
            'hotspot_id' => $validated['hotspot_id'],
            'session_id' => $validated['session_id'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Track view event
     */
    public function trackView(Request $request, $slug)
    {
        $flipbook = Flipbook::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'session_id' => 'nullable|string',
        ]);

        FlipbookAnalytic::logEvent($flipbook->id, FlipbookAnalytic::EVENT_VIEW, [
            'session_id' => $validated['session_id'] ?? null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Download PDF
     */
    public function download($slug)
    {
        $flipbook = Flipbook::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Check download permission
        if (isset($flipbook->settings['allow_download']) && !$flipbook->settings['allow_download']) {
            abort(403, 'Download is not allowed for this flipbook.');
        }

        // Log download event
        FlipbookAnalytic::logEvent($flipbook->id, FlipbookAnalytic::EVENT_DOWNLOAD);

        return response()->download(
            storage_path('app/public/' . $flipbook->pdf_path),
            $flipbook->title . '.pdf'
        );
    }

    /**
     * Get analytics data
     */
    public function analytics(Flipbook $flipbook)
    {
        $this->authorize('view', $flipbook);

        $analytics = [
            'total_views' => $flipbook->analytics()
                ->where('event_type', FlipbookAnalytic::EVENT_VIEW)
                ->count(),
            'unique_visitors' => $flipbook->analytics()
                ->where('event_type', FlipbookAnalytic::EVENT_VIEW)
                ->distinct('session_id')
                ->count('session_id'),
            'total_page_turns' => $flipbook->analytics()
                ->where('event_type', FlipbookAnalytic::EVENT_PAGE_TURN)
                ->count(),
            'hotspot_clicks' => $flipbook->analytics()
                ->where('event_type', FlipbookAnalytic::EVENT_HOTSPOT_CLICK)
                ->count(),
            'downloads' => $flipbook->analytics()
                ->where('event_type', FlipbookAnalytic::EVENT_DOWNLOAD)
                ->count(),
            'page_heatmap' => $flipbook->analytics()
                ->where('event_type', FlipbookAnalytic::EVENT_PAGE_TURN)
                ->selectRaw('flipbook_page_id, COUNT(*) as views')
                ->groupBy('flipbook_page_id')
                ->get(),
            'hotspot_clicks_detail' => $flipbook->analytics()
                ->where('event_type', FlipbookAnalytic::EVENT_HOTSPOT_CLICK)
                ->with('hotspot')
                ->selectRaw('flipbook_hotspot_id, COUNT(*) as clicks')
                ->groupBy('flipbook_hotspot_id')
                ->get(),
        ];

        return response()->json($analytics);
    }
}
