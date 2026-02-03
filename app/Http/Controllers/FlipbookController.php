<?php

namespace App\Http\Controllers;

use App\Models\Flipbook;
use App\Models\FlipbookTemplate;
use App\Services\PdfConverterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlipbookController extends Controller
{
    protected $pdfConverter;

    public function __construct(PdfConverterService $pdfConverter)
    {
        $this->pdfConverter = $pdfConverter;
        $this->middleware('auth');
    }

    /**
     * Display a listing of flipbooks
     */
    public function index()
    {
        $flipbooks = Flipbook::with(['user', 'template'])
            ->latest()
            ->paginate(20);

        return view('flipbooks.index', compact('flipbooks'));
    }

    /**
     * Show the form for creating a new flipbook
     */
    public function create()
    {
        $templates = FlipbookTemplate::active()->get();
        return view('flipbooks.create', compact('templates'));
    }

    /**
     * Store a newly created flipbook
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pdf_file' => 'required|file|mimes:pdf|max:51200', // Max 50MB
            'template_id' => 'nullable|exists:flipbook_templates,id',
            'is_public' => 'boolean',
        ]);

        try {
            // Store PDF file
            $pdfFile = $request->file('pdf_file');
            $pdfPath = $pdfFile->store('flipbooks/pdfs', 'public');

            // Get page count
            $fullPath = Storage::disk('public')->path($pdfPath);

            try {
                $pageCount = $this->pdfConverter->getPageCount($fullPath);
            } catch (\Exception $e) {
                // If Imagick is not available, show detailed error
                if (strpos($e->getMessage(), 'Imagick extension') !== false) {
                    // Clean up uploaded file
                    Storage::disk('public')->delete($pdfPath);

                    return back()
                        ->withInput()
                        ->with('error', $e->getMessage());
                }
                throw $e;
            }

            // Create flipbook record
            $flipbook = Flipbook::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'pdf_path' => $pdfPath,
                'template_id' => $validated['template_id'] ?? null,
                'user_id' => Auth::id(),
                'total_pages' => $pageCount,
                'is_public' => $validated['is_public'] ?? true,
                'is_published' => false,
            ]);

            // Convert PDF to images in background (you might want to use a queue job for this)
            try {
                $this->convertPdfPages($flipbook, $fullPath);
            } catch (\Exception $e) {
                // Log error but don't fail - pages can be processed later
                Log::error('Failed to convert PDF pages: ' . $e->getMessage());
            }

            return redirect()
                ->route('flipbooks.edit', $flipbook)
                ->with('success', 'Flipbook created successfully! Pages are being processed.');
        } catch (\Exception $e) {
            // Clean up if exists
            if (isset($pdfPath)) {
                Storage::disk('public')->delete($pdfPath);
            }

            return back()
                ->withInput()
                ->with('error', 'Failed to create flipbook: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified flipbook
     */
    public function show(Flipbook $flipbook)
    {
        $flipbook->load(['pages', 'template']);

        return view('flipbooks.show', compact('flipbook'));
    }

    /**
     * Show the form for editing the flipbook
     */
    public function edit(Flipbook $flipbook)
    {
        $templates = FlipbookTemplate::active()->get();
        $flipbook->load('pages');

        return view('flipbooks.edit', compact('flipbook', 'templates'));
    }

    /**
     * Update the specified flipbook
     */
    public function update(Request $request, Flipbook $flipbook)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_id' => 'nullable|exists:flipbook_templates,id',
            'is_public' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $flipbook->update($validated);

        if ($request->boolean('is_published') && !$flipbook->published_at) {
            $flipbook->update(['published_at' => now()]);
        }

        return back()->with('success', 'Flipbook updated successfully!');
    }

    /**
     * Remove the specified flipbook
     */
    public function destroy(Flipbook $flipbook)
    {
        try {
            // Delete PDF and images
            Storage::disk('public')->delete($flipbook->pdf_path);

            foreach ($flipbook->pages as $page) {
                Storage::disk('public')->delete($page->image_path);
                if ($page->thumbnail_path) {
                    Storage::disk('public')->delete($page->thumbnail_path);
                }
            }

            $flipbook->delete();

            return redirect()
                ->route('flipbooks.index')
                ->with('success', 'Flipbook deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete flipbook: ' . $e->getMessage());
        }
    }

    /**
     * Show hotspot editor
     */
    public function editor(Flipbook $flipbook)
    {
        $flipbook->load(['pages.hotspots.product']);

        // Use PDF.js editor if no pages exist (no Imagick)
        if ($flipbook->pages->isEmpty()) {
            return view('flipbooks.editor-pdfjs', compact('flipbook'));
        }

        // Use the new improved editor with working drag-and-drop
        return view('flipbooks.editor-improved', compact('flipbook'));
    }

    /**
     * Publish/Unpublish flipbook
     */
    public function togglePublish(Flipbook $flipbook)
    {
        $flipbook->update([
            'is_published' => !$flipbook->is_published,
            'published_at' => !$flipbook->is_published ? now() : $flipbook->published_at,
        ]);

        $status = $flipbook->is_published ? 'published' : 'unpublished';

        return back()->with('success', "Flipbook {$status} successfully!");
    }

    /**
     * Convert PDF pages to images
     */
    protected function convertPdfPages(Flipbook $flipbook, $pdfPath)
    {
        try {
            $outputDir = 'flipbooks/' . $flipbook->id . '/pages';
            $images = $this->pdfConverter->convertPdfToImages($pdfPath, $outputDir);

            foreach ($images as $imageData) {
                $flipbook->pages()->create([
                    'page_number' => $imageData['page_number'],
                    'image_path' => $imageData['image_path'],
                    'width' => $imageData['width'],
                    'height' => $imageData['height'],
                ]);
            }

            // Generate thumbnail from first page
            if (!empty($images)) {
                $thumbnailPath = 'flipbooks/' . $flipbook->id . '/thumbnail.png';
                $this->pdfConverter->generateThumbnail($images[0]['image_path'], $thumbnailPath);
                $flipbook->update(['thumbnail' => $thumbnailPath]);
            }
        } catch (\Exception $e) {
            Log::error('PDF conversion failed: ' . $e->getMessage());
            // You might want to update flipbook status to 'failed' here
        }
    }
}
