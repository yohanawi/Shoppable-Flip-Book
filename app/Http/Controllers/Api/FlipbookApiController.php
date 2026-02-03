<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlipbookPage;
use App\Models\Hotspot;
use App\Models\Product;
use Illuminate\Http\Request;

class FlipbookApiController extends Controller
{
    /**
     * Get hotspots for a page
     */
    public function getPageHotspots(FlipbookPage $page)
    {
        $hotspots = $page->hotspots()->with('product')->get();

        return response()->json([
            'success' => true,
            'hotspots' => $hotspots
        ]);
    }

    /**
     * Get hotspot count for a page
     */
    public function getPageHotspotCount(FlipbookPage $page)
    {
        return response()->json([
            'success' => true,
            'count' => $page->hotspots()->count()
        ]);
    }

    /**
     * Store a new hotspot
     */
    public function storeHotspot(Request $request, FlipbookPage $page)
    {
        $validated = $request->validate([
            'type' => 'required|in:link,product,video,popup,internal',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'x' => 'required|numeric|min:0|max:100',
            'y' => 'required|numeric|min:0|max:100',
            'width' => 'required|numeric|min:0|max:100',
            'height' => 'required|numeric|min:0|max:100',
            'target_url' => 'nullable|url|max:500',
            'target_page' => 'nullable|integer|min:1',
            'product_id' => 'nullable|exists:products,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'opacity' => 'nullable|numeric|min:0|max:1',
            'is_active' => 'boolean',
        ]);

        $hotspot = $page->hotspots()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hotspot created successfully',
            'hotspot' => $hotspot->load('product')
        ], 201);
    }

    /**
     * Update a hotspot
     */
    public function updateHotspot(Request $request, Hotspot $hotspot)
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:link,product,video,popup,internal',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'x' => 'sometimes|numeric|min:0|max:100',
            'y' => 'sometimes|numeric|min:0|max:100',
            'width' => 'sometimes|numeric|min:0|max:100',
            'height' => 'sometimes|numeric|min:0|max:100',
            'target_url' => 'nullable|url|max:500',
            'target_page' => 'nullable|integer|min:1',
            'product_id' => 'nullable|exists:products,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'opacity' => 'nullable|numeric|min:0|max:1',
            'is_active' => 'boolean',
        ]);

        $hotspot->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hotspot updated successfully',
            'hotspot' => $hotspot->fresh()->load('product')
        ]);
    }

    /**
     * Delete a hotspot
     */
    public function deleteHotspot(Hotspot $hotspot)
    {
        $hotspot->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hotspot deleted successfully'
        ]);
    }

    /**
     * Record hotspot click
     */
    public function recordClick(Hotspot $hotspot)
    {
        $hotspot->recordClick();

        return response()->json([
            'success' => true,
            'clicks' => $hotspot->clicks
        ]);
    }

    /**
     * Get products for autocomplete
     */
    public function getProducts(Request $request)
    {
        $search = $request->get('search', '');

        $products = Product::active()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'sku', 'price', 'image')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}
