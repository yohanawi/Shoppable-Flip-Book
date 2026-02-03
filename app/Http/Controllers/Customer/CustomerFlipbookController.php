<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Flipbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerFlipbookController extends Controller
{
    public function index()
    {
        $flipbooks = Flipbook::where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('customer.catalog.index', compact('flipbooks'));
    }

    public function create()
    {
        return view('customer.catalog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:51200', // 50MB max
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_type' => 'required|in:page_management,page_flip_physics,slicer',
            'visibility' => 'required|in:public,private,unlisted',
        ]);

        // Store PDF
        $pdfPath = $request->file('pdf')->store('flipbooks/pdfs/' . Auth::id(), 'public');

        // Create flipbook
        $flipbook = Flipbook::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
            'description' => $validated['description'],
            'pdf_path' => $pdfPath,
            'template_type' => $validated['template_type'],
            'visibility' => $validated['visibility'],
            'status' => 'draft',
            'is_published' => false,
        ]);

        return redirect()->route('customer.template.show', [
            'flipbook' => $flipbook->id,
            'type' => $flipbook->template_type
        ])->with('success', 'FlipBook created successfully! Now configure your template.');
    }

    public function show(Flipbook $flipbook)
    {
        $this->authorize('view', $flipbook);
        return view('customer.catalog.show', compact('flipbook'));
    }

    public function edit(Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);
        return view('customer.catalog.edit', compact('flipbook'));
    }

    public function update(Request $request, Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_type' => 'required|in:page_management,page_flip_physics,slicer',
            'visibility' => 'required|in:public,private,unlisted',
        ]);

        $flipbook->update($validated);

        return redirect()->route('customer.catalog.index')
            ->with('success', 'FlipBook updated successfully!');
    }

    public function destroy(Flipbook $flipbook)
    {
        $this->authorize('delete', $flipbook);

        // Delete PDF file
        if ($flipbook->pdf_path && Storage::disk('public')->exists($flipbook->pdf_path)) {
            Storage::disk('public')->delete($flipbook->pdf_path);
        }

        // Delete associated hotspots
        $flipbook->hotspots()->delete();

        $flipbook->delete();

        return redirect()->route('customer.catalog.index')
            ->with('success', 'FlipBook deleted successfully!');
    }

    public function publish(Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $flipbook->update([
            'status' => 'live',
            'is_published' => true,
            'published_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FlipBook published successfully!',
            'url' => route('flipbook.viewer', $flipbook->slug),
        ]);
    }

    public function unpublish(Flipbook $flipbook)
    {
        $this->authorize('update', $flipbook);

        $flipbook->update([
            'status' => 'draft',
            'is_published' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FlipBook unpublished successfully!',
        ]);
    }

    public function preview(Flipbook $flipbook)
    {
        $this->authorize('view', $flipbook);
        return view('customer.catalog.preview', compact('flipbook'));
    }

    public function duplicate(Flipbook $flipbook)
    {
        $this->authorize('view', $flipbook);

        $newFlipbook = $flipbook->replicate();
        $newFlipbook->title = $flipbook->title . ' (Copy)';
        $newFlipbook->slug = Str::slug($newFlipbook->title) . '-' . Str::random(6);
        $newFlipbook->status = 'draft';
        $newFlipbook->is_published = false;
        $newFlipbook->published_at = null;
        $newFlipbook->save();

        // Copy hotspots
        foreach ($flipbook->hotspots as $hotspot) {
            $newHotspot = $hotspot->replicate();
            $newHotspot->flipbook_id = $newFlipbook->id;
            $newHotspot->save();
        }

        return redirect()->route('customer.catalog.edit', $newFlipbook)
            ->with('success', 'FlipBook duplicated successfully!');
    }
}
