<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flipbook;
use Illuminate\Http\Request;

class PageManagementController extends Controller
{
    public function reorder(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'pages' => 'required|array',
            'pages.*.page_number' => 'required|integer',
            'pages.*.new_order' => 'required|integer',
        ]);

        $pageStructure = $flipbook->page_structure ?? [];
        foreach ($validated['pages'] as $page) {
            $pageStructure[$page['page_number']]['order'] = $page['new_order'];
        }

        $flipbook->update(['page_structure' => $pageStructure]);

        return response()->json([
            'success' => true,
            'message' => 'Pages reordered successfully!'
        ]);
    }

    public function update(Request $request, Flipbook $flipbook, $pageNumber)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'locked' => 'boolean',
            'hidden' => 'boolean',
        ]);

        $pageStructure = $flipbook->page_structure ?? [];
        $pageStructure[$pageNumber] = array_merge(
            $pageStructure[$pageNumber] ?? [],
            $validated
        );

        $flipbook->update(['page_structure' => $pageStructure]);

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully!'
        ]);
    }

    public function delete(Request $request, Flipbook $flipbook, $pageNumber)
    {
        $this->authorize('update', $flipbook);

        $pageStructure = $flipbook->page_structure ?? [];
        unset($pageStructure[$pageNumber]);

        $flipbook->update(['page_structure' => $pageStructure]);

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully!'
        ]);
    }
}
