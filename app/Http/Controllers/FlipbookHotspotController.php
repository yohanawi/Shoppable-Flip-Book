<?php

namespace App\Http\Controllers;

use App\Models\Flipbook;
use App\Models\FlipbookPage;
use App\Models\FlipbookHotspot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlipbookHotspotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new hotspot
     */
    public function store(Request $request, FlipbookPage $page)
    {
        $validated = $request->validate([
            'type' => 'required|in:link,product,video,popup,internal,external',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'x_position' => 'required|numeric|min:0|max:100',
            'y_position' => 'required|numeric|min:0|max:100',
            'width' => 'required|numeric|min:0|max:100',
            'height' => 'required|numeric|min:0|max:100',
            'target_url' => 'nullable|url',
            'target_route' => 'nullable|string',
            'target_params' => 'nullable|array',
            'product_id' => 'nullable|integer',
            'popup_content' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'animation' => 'nullable|string',
            'target_type' => 'nullable|in:_blank,_self,modal,cart',
            'is_active' => 'boolean',
        ]);

        $hotspot = $page->hotspots()->create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'hotspot' => $hotspot,
            ]);
        }

        return back()->with('success', 'Hotspot created successfully!');
    }

    /**
     * Update a hotspot
     */
    public function update(Request $request, FlipbookHotspot $hotspot)
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:link,product,video,popup,internal,external',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'x_position' => 'sometimes|numeric|min:0|max:100',
            'y_position' => 'sometimes|numeric|min:0|max:100',
            'width' => 'sometimes|numeric|min:0|max:100',
            'height' => 'sometimes|numeric|min:0|max:100',
            'target_url' => 'nullable|url',
            'target_route' => 'nullable|string',
            'target_params' => 'nullable|array',
            'product_id' => 'nullable|integer',
            'popup_content' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'animation' => 'nullable|string',
            'target_type' => 'nullable|in:_blank,_self,modal,cart',
            'is_active' => 'boolean',
        ]);

        $hotspot->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'hotspot' => $hotspot->fresh(),
            ]);
        }

        return back()->with('success', 'Hotspot updated successfully!');
    }

    /**
     * Delete a hotspot
     */
    public function destroy(FlipbookHotspot $hotspot)
    {
        $hotspot->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Hotspot deleted successfully!');
    }

    /**
     * Duplicate a hotspot
     */
    public function duplicate(FlipbookHotspot $hotspot)
    {
        $newHotspot = $hotspot->replicate();
        $newHotspot->x_position += 2; // Slight offset
        $newHotspot->y_position += 2;
        $newHotspot->save();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'hotspot' => $newHotspot,
            ]);
        }

        return back()->with('success', 'Hotspot duplicated successfully!');
    }

    /**
     * Toggle hotspot active status
     */
    public function toggleActive(FlipbookHotspot $hotspot)
    {
        $hotspot->update(['is_active' => !$hotspot->is_active]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $hotspot->is_active,
            ]);
        }

        return back()->with('success', 'Hotspot status updated!');
    }

    /**
     * Track hotspot click (for analytics)
     */
    public function trackClick(Request $request, FlipbookHotspot $hotspot)
    {
        $hotspot->trackClick(
            session()->getId(),
            Auth::id()
        );

        return response()->json(['success' => true]);
    }
}
