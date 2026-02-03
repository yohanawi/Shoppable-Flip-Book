<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flipbook;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function show(Flipbook $flipbook, $type)
    {
        $this->authorize('update', $flipbook);

        $viewMap = [
            'page_management' => 'customer.templates.page-management',
            'page_flip_physics' => 'customer.templates.flip-physics',
            'slicer' => 'customer.templates.slicer-improved', // Use improved editor
        ];

        $view = $viewMap[$type] ?? 'customer.templates.slicer-improved';

        return view($view, compact('flipbook'));
    }

    public function saveConfig(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'template_config' => 'nullable|array',
            'page_structure' => 'nullable|array',
            'flip_physics' => 'nullable|array',
        ]);

        $flipbook->update([
            'template_config' => $validated['template_config'] ?? null,
            'page_structure' => $validated['page_structure'] ?? null,
            'flip_physics' => $validated['flip_physics'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template configuration saved successfully!'
        ]);
    }
}
