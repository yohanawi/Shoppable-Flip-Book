<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flipbook;
use Illuminate\Http\Request;

class FlipPhysicsController extends Controller
{
    public function save(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'flip_speed' => 'required|numeric|min:0.1|max:5',
            'flip_style' => 'required|in:smooth,realistic,fast',
            'page_curl' => 'required|boolean',
            'shadow_effect' => 'required|boolean',
            'sound_enabled' => 'required|boolean',
            'auto_flip' => 'required|boolean',
            'auto_flip_delay' => 'nullable|integer|min:1|max:60',
            'drag_sensitivity' => 'required|numeric|min:0.1|max:2',
        ]);

        $flipbook->update([
            'flip_physics' => $validated
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Flip physics saved successfully!'
        ]);
    }
}
