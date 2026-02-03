<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flipbook;
use App\Models\FlipbookPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerPageManagementController extends Controller
{
    /**
     * Show page management interface
     */
    public function index(Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $pages = $flipbook->pages()
            ->orderBy('display_order')
            ->orderBy('page_number')
            ->get();

        return view('customer.page-management.index', compact('flipbook', 'pages'));
    }

    /**
     * Get all pages for a flipbook
     */
    public function getPages(Flipbook $flipbook)
    {
        $this->authorize('view', $flipbook);

        $pages = $flipbook->pages()
            ->orderBy('display_order')
            ->orderBy('page_number')
            ->get()
            ->map(function ($page) {
                return [
                    'id' => $page->id,
                    'page_number' => $page->page_number,
                    'custom_name' => $page->custom_name,
                    'display_order' => $page->display_order,
                    'is_locked' => $page->is_locked,
                    'is_hidden' => $page->is_hidden,
                    'thumbnail_url' => $page->getThumbnailUrl(),
                    'image_url' => $page->getImageUrl(),
                ];
            });

        return response()->json($pages);
    }

    /**
     * Rename a page
     */
    public function rename(Request $request, Flipbook $flipbook, $pageId)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'custom_name' => 'required|string|max:255',
        ]);

        $page = FlipbookPage::where('flipbook_id', $flipbook->id)
            ->where('id', $pageId)
            ->firstOrFail();

        if ($page->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot rename a locked page.',
            ], 403);
        }

        $page->update(['custom_name' => $validated['custom_name']]);

        return response()->json([
            'success' => true,
            'message' => 'Page renamed successfully.',
            'page' => $page,
        ]);
    }

    /**
     * Delete a page
     */
    public function delete(Flipbook $flipbook, $pageId)
    {
        $this->authorize('update', $flipbook);

        $page = FlipbookPage::where('flipbook_id', $flipbook->id)
            ->where('id', $pageId)
            ->firstOrFail();

        if ($page->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a locked page.',
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Delete associated hotspots
            $page->hotspots()->delete();

            // Delete the page
            $page->delete();

            // Reorder remaining pages
            $remainingPages = $flipbook->pages()->orderBy('display_order')->get();
            foreach ($remainingPages as $index => $p) {
                $p->update(['display_order' => $index]);
            }

            // Update total pages count
            $flipbook->update(['total_pages' => $remainingPages->count()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Page deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Page deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete page.',
            ], 500);
        }
    }

    /**
     * Reorder pages (drag and drop)
     */
    public function reorder(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'page_orders' => 'required|array',
            'page_orders.*.id' => 'required|exists:flipbook_pages,id',
            'page_orders.*.display_order' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['page_orders'] as $pageOrder) {
                $page = FlipbookPage::where('flipbook_id', $flipbook->id)
                    ->where('id', $pageOrder['id'])
                    ->first();

                if ($page && !$page->is_locked) {
                    $page->update(['display_order' => $pageOrder['display_order']]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pages reordered successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Page reordering failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder pages.',
            ], 500);
        }
    }

    /**
     * Toggle page lock status
     */
    public function toggleLock(Flipbook $flipbook, $pageId)
    {
        $this->authorize('update', $flipbook);

        $page = FlipbookPage::where('flipbook_id', $flipbook->id)
            ->where('id', $pageId)
            ->firstOrFail();

        $page->update(['is_locked' => !$page->is_locked]);

        return response()->json([
            'success' => true,
            'message' => $page->is_locked ? 'Page locked.' : 'Page unlocked.',
            'is_locked' => $page->is_locked,
        ]);
    }

    /**
     * Toggle page visibility
     */
    public function toggleVisibility(Flipbook $flipbook, $pageId)
    {
        $this->authorize('update', $flipbook);

        $page = FlipbookPage::where('flipbook_id', $flipbook->id)
            ->where('id', $pageId)
            ->firstOrFail();

        if ($page->is_locked) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change visibility of a locked page.',
            ], 403);
        }

        $page->update(['is_hidden' => !$page->is_hidden]);

        return response()->json([
            'success' => true,
            'message' => $page->is_hidden ? 'Page hidden.' : 'Page visible.',
            'is_hidden' => $page->is_hidden,
        ]);
    }

    /**
     * Bulk operations on pages
     */
    public function bulkOperation(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'operation' => 'required|in:lock,unlock,hide,show,delete',
            'page_ids' => 'required|array',
            'page_ids.*' => 'exists:flipbook_pages,id',
        ]);

        DB::beginTransaction();
        try {
            $pages = FlipbookPage::where('flipbook_id', $flipbook->id)
                ->whereIn('id', $validated['page_ids'])
                ->get();

            foreach ($pages as $page) {
                switch ($validated['operation']) {
                    case 'lock':
                        $page->update(['is_locked' => true]);
                        break;
                    case 'unlock':
                        $page->update(['is_locked' => false]);
                        break;
                    case 'hide':
                        if (!$page->is_locked) {
                            $page->update(['is_hidden' => true]);
                        }
                        break;
                    case 'show':
                        if (!$page->is_locked) {
                            $page->update(['is_hidden' => false]);
                        }
                        break;
                    case 'delete':
                        if (!$page->is_locked) {
                            $page->hotspots()->delete();
                            $page->delete();
                        }
                        break;
                }
            }

            // Update total pages if deleted
            if ($validated['operation'] === 'delete') {
                $flipbook->update(['total_pages' => $flipbook->pages()->count()]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk operation completed successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk operation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete bulk operation.',
            ], 500);
        }
    }
}
