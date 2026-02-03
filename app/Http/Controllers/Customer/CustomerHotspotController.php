<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flipbook;
use App\Models\FlipbookHotspot;
use App\Models\FlipbookPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CustomerHotspotController extends Controller
{
    /**
     * Show hotspot editor interface
     */
    public function index(Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $hotspots = $flipbook->hotspots()
            ->orderBy('page_number')
            ->orderBy('display_order')
            ->get();

        $pages = $flipbook->pages()->orderBy('page_number')->get();

        return view('customer.hotspot-editor.index', compact('flipbook', 'hotspots', 'pages'));
    }

    /**
     * Get all hotspots for a flipbook
     */
    public function getHotspots(Flipbook $flipbook, Request $request)
    {
        $this->authorize('view', $flipbook);

        $query = $flipbook->hotspots();

        // Filter by page if specified
        if ($request->has('page_number')) {
            $query->where('page_number', $request->page_number);
        }

        $hotspots = $query->orderBy('page_number')
            ->orderBy('display_order')
            ->get();

        return response()->json($hotspots);
    }

    public function store(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'page_number' => 'required|integer|min:1',
            'shape_type' => 'required|in:rectangle,polygon,freeform',
            'action_type' => 'required|in:link,internal_page,popup_image,popup_video,product',

            // Position and size (for rectangles)
            'x_position' => 'required_if:shape_type,rectangle|nullable|numeric|min:0|max:100',
            'y_position' => 'required_if:shape_type,rectangle|nullable|numeric|min:0|max:100',
            'width' => 'required_if:shape_type,rectangle|nullable|numeric|min:0|max:100',
            'height' => 'required_if:shape_type,rectangle|nullable|numeric|min:0|max:100',

            // Coordinates (for polygons and freeform)
            'coordinates' => 'required_unless:shape_type,rectangle|nullable|array',

            // Action-specific fields
            'target_url' => 'required_if:action_type,link|nullable|url',
            'target_page_number' => 'required_if:action_type,internal_page|nullable|integer|min:1',
            'popup_media_url' => 'nullable|string',
            'popup_type' => 'nullable|in:image,video',
            'product_id' => 'nullable|exists:products,id',
            'product_name' => 'required_without:product_id|nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',

            // Optional fields
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'animation' => 'nullable|string',
            'is_active' => 'nullable|boolean',

            // File uploads
            'popup_image' => 'nullable|image|max:5120',
            'thumbnail_image' => 'nullable|image|max:2048',
        ]);

        try {
            // Handle file uploads
            if ($request->hasFile('popup_image')) {
                $validated['popup_media_url'] = $request->file('popup_image')
                    ->store("flipbooks/{$flipbook->id}/hotspot-media", 'public');
                $validated['popup_type'] = 'image';
            }

            // Set defaults
            $validated['flipbook_id'] = $flipbook->id;
            $validated['is_active'] = $validated['is_active'] ?? true;
            $validated['type'] = $validated['action_type'];

            // Get or create page
            $page = FlipbookPage::firstOrCreate([
                'flipbook_id' => $flipbook->id,
                'page_number' => $validated['page_number']
            ], [
                'image_path' => null,
                'display_order' => $validated['page_number'] - 1,
            ]);

            $validated['flipbook_page_id'] = $page->id;

            // Get next display order
            $maxOrder = FlipbookHotspot::where('flipbook_id', $flipbook->id)
                ->where('page_number', $validated['page_number'])
                ->max('display_order');
            $validated['display_order'] = ($maxOrder ?? -1) + 1;

            $hotspot = FlipbookHotspot::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Hotspot created successfully.',
                'hotspot' => $hotspot->load('page'),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Hotspot creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create hotspot: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Flipbook $flipbook, FlipbookHotspot $hotspot)
    {
        $this->authorize('update', $flipbook);

        if ($hotspot->flipbook_id !== $flipbook->id) {
            return response()->json([
                'success' => false,
                'message' => 'Hotspot does not belong to this flipbook.',
            ], 403);
        }

        $validated = $request->validate([
            'shape_type' => 'sometimes|in:rectangle,polygon,freeform',
            'action_type' => 'sometimes|in:link,internal_page,popup_image,popup_video,product',

            'x_position' => 'nullable|numeric|min:0|max:100',
            'y_position' => 'nullable|numeric|min:0|max:100',
            'width' => 'nullable|numeric|min:0|max:100',
            'height' => 'nullable|numeric|min:0|max:100',

            'coordinates' => 'nullable|array',

            'target_url' => 'nullable|url',
            'target_page_number' => 'nullable|integer|min:1',
            'popup_media_url' => 'nullable|string',
            'popup_type' => 'nullable|in:image,video',
            'product_id' => 'nullable|exists:products,id',
            'product_name' => 'nullable|string|max:255',

            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string',
            'animation' => 'nullable|string',
            'is_active' => 'nullable|boolean',

            'popup_image' => 'nullable|image|max:5120',
        ]);

        try {
            // Handle file uploads
            if ($request->hasFile('popup_image')) {
                // Delete old image if exists
                if ($hotspot->popup_media_url && Storage::disk('public')->exists($hotspot->popup_media_url)) {
                    Storage::disk('public')->delete($hotspot->popup_media_url);
                }
                $validated['popup_media_url'] = $request->file('popup_image')
                    ->store("flipbooks/{$flipbook->id}/hotspot-media", 'public');
                $validated['popup_type'] = 'image';
            }

            if (isset($validated['action_type'])) {
                $validated['type'] = $validated['action_type'];
            }

            $hotspot->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Hotspot updated successfully.',
                'hotspot' => $hotspot->fresh()->load('page'),
            ]);
        } catch (\Exception $e) {
            Log::error('Hotspot update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update hotspot.',
            ], 500);
        }
    }

    public function destroy(Flipbook $flipbook, FlipbookHotspot $hotspot)
    {
        $this->authorize('update', $flipbook);

        if ($hotspot->flipbook_id !== $flipbook->id) {
            return response()->json([
                'success' => false,
                'message' => 'Hotspot does not belong to this flipbook.',
            ], 403);
        }

        try {
            // Delete uploaded media
            if ($hotspot->popup_media_url && Storage::disk('public')->exists($hotspot->popup_media_url)) {
                Storage::disk('public')->delete($hotspot->popup_media_url);
            }

            $hotspot->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hotspot deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Hotspot deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete hotspot.',
            ], 500);
        }
    }

    /**
     * Bulk delete hotspots
     */
    public function bulkDelete(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'hotspot_ids' => 'required|array',
            'hotspot_ids.*' => 'exists:flipbook_hotspots,id',
        ]);

        try {
            $hotspots = FlipbookHotspot::where('flipbook_id', $flipbook->id)
                ->whereIn('id', $validated['hotspot_ids'])
                ->get();

            foreach ($hotspots as $hotspot) {
                if ($hotspot->popup_media_url && Storage::disk('public')->exists($hotspot->popup_media_url)) {
                    Storage::disk('public')->delete($hotspot->popup_media_url);
                }
            }

            FlipbookHotspot::where('flipbook_id', $flipbook->id)
                ->whereIn('id', $validated['hotspot_ids'])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Hotspots deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk hotspot deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete hotspots.',
            ], 500);
        }
    }

    /**
     * Upload media for popup hotspots
     */
    public function uploadMedia(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'media' => 'required|file|mimes:jpeg,jpg,png,gif,mp4,webm,ogg|max:51200', // 50MB
            'type' => 'required|in:image,video',
        ]);

        try {
            $file = $request->file('media');
            $path = $file->store("flipbooks/{$flipbook->id}/hotspot-media", 'public');

            return response()->json([
                'success' => true,
                'message' => 'Media uploaded successfully.',
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ]);
        } catch (\Exception $e) {
            Log::error('Media upload failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload media.',
            ], 500);
        }
    }

    /**
     * Duplicate a hotspot
     */
    public function duplicate(Flipbook $flipbook, FlipbookHotspot $hotspot)
    {
        $this->authorize('update', $flipbook);

        if ($hotspot->flipbook_id !== $flipbook->id) {
            return response()->json([
                'success' => false,
                'message' => 'Hotspot does not belong to this flipbook.',
            ], 403);
        }

        try {
            $newHotspot = $hotspot->replicate();
            $newHotspot->title = ($hotspot->title ?? 'Hotspot') . ' (Copy)';

            // Get next display order
            $maxOrder = FlipbookHotspot::where('flipbook_id', $flipbook->id)
                ->where('page_number', $hotspot->page_number)
                ->max('display_order');
            $newHotspot->display_order = ($maxOrder ?? -1) + 1;

            $newHotspot->save();

            return response()->json([
                'success' => true,
                'message' => 'Hotspot duplicated successfully.',
                'hotspot' => $newHotspot,
            ]);
        } catch (\Exception $e) {
            Log::error('Hotspot duplication failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate hotspot.',
            ], 500);
        }
    }
}
