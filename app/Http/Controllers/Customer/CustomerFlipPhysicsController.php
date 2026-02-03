<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flipbook;
use App\Models\FlipbookPhysicsPreset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerFlipPhysicsController extends Controller
{
    /**
     * Show flip physics configuration interface
     */
    public function index(Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $presets = FlipbookPhysicsPreset::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        $currentPhysics = $flipbook->flip_physics ?? $this->getDefaultPhysics();

        return view('customer.flip-physics.index', compact('flipbook', 'presets', 'currentPhysics'));
    }

    /**
     * Get all available presets
     */
    public function getPresets()
    {
        $presets = FlipbookPhysicsPreset::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json($presets);
    }

    /**
     * Get a specific preset
     */
    public function getPreset($presetId)
    {
        $preset = FlipbookPhysicsPreset::findOrFail($presetId);
        return response()->json($preset);
    }

    /**
     * Save flip physics configuration
     */
    public function save(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'preset_id' => 'nullable|exists:flipbook_physics_presets,id',
            'parameters' => 'required|array',
            'parameters.duration' => 'required|integer|min:100|max:3000',
            'parameters.acceleration' => 'required|numeric|min:0.1|max:5.0',
            'parameters.hardness' => 'required|integer|min:0|max:50',
            'parameters.elevation' => 'required|integer|min:0|max:1000',
            'parameters.corners' => 'required|in:forward,backward,all',
            'parameters.startFlipAngle' => 'required|integer|min:-90|max:90',
        ]);

        try {
            $flipbook->update([
                'flip_physics' => [
                    'preset_id' => $validated['preset_id'],
                    'parameters' => $validated['parameters'],
                    'updated_at' => now()->toDateTimeString(),
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Flip physics saved successfully.',
                'physics' => $flipbook->flip_physics,
            ]);
        } catch (\Exception $e) {
            Log::error('Flip physics save failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save flip physics configuration.',
            ], 500);
        }
    }

    /**
     * Apply preset to flipbook
     */
    public function applyPreset(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'preset_id' => 'required|exists:flipbook_physics_presets,id',
        ]);

        try {
            $preset = FlipbookPhysicsPreset::findOrFail($validated['preset_id']);

            $flipbook->update([
                'flip_physics' => [
                    'preset_id' => $preset->id,
                    'parameters' => $preset->parameters,
                    'updated_at' => now()->toDateTimeString(),
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Preset '{$preset->name}' applied successfully.",
                'physics' => $flipbook->flip_physics,
            ]);
        } catch (\Exception $e) {
            Log::error('Apply preset failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply preset.',
            ], 500);
        }
    }

    /**
     * Reset to default physics
     */
    public function reset(Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        try {
            $defaultPreset = FlipbookPhysicsPreset::where('is_default', true)->first();

            $flipbook->update([
                'flip_physics' => [
                    'preset_id' => $defaultPreset->id,
                    'parameters' => $defaultPreset->parameters,
                    'updated_at' => now()->toDateTimeString(),
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reset to default physics successfully.',
                'physics' => $flipbook->flip_physics,
            ]);
        } catch (\Exception $e) {
            Log::error('Reset physics failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset physics.',
            ], 500);
        }
    }

    /**
     * Get default physics parameters
     */
    private function getDefaultPhysics()
    {
        $defaultPreset = FlipbookPhysicsPreset::where('is_default', true)->first();

        if ($defaultPreset) {
            return [
                'preset_id' => $defaultPreset->id,
                'parameters' => $defaultPreset->parameters,
            ];
        }

        return [
            'preset_id' => null,
            'parameters' => [
                'duration' => 800,
                'acceleration' => 1.5,
                'hardness' => 10,
                'elevation' => 300,
                'corners' => 'forward',
                'startFlipAngle' => 0,
            ],
        ];
    }
}
