<?php

namespace App\Jobs;

use App\Models\Flipbook;
use App\Services\PdfConverterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessFlipbookPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $flipbook;
    protected $pdfPath;

    /**
     * Create a new job instance.
     */
    public function __construct(Flipbook $flipbook, $pdfPath)
    {
        $this->flipbook = $flipbook;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Execute the job.
     */
    public function handle(PdfConverterService $pdfConverter): void
    {
        try {
            Log::info("Processing flipbook PDF: {$this->flipbook->id}");

            // Convert PDF to images
            $outputDir = 'flipbooks/' . $this->flipbook->id . '/pages';
            $images = $pdfConverter->convertPdfToImages($this->pdfPath, $outputDir);

            Log::info("Converted {$this->flipbook->id} to " . count($images) . " pages");

            // Create page records
            foreach ($images as $imageData) {
                $this->flipbook->pages()->create([
                    'page_number' => $imageData['page_number'],
                    'image_path' => $imageData['image_path'],
                    'width' => $imageData['width'],
                    'height' => $imageData['height'],
                ]);
            }

            // Generate thumbnail from first page
            if (!empty($images)) {
                $thumbnailPath = 'flipbooks/' . $this->flipbook->id . '/thumbnail.png';
                $pdfConverter->generateThumbnail($images[0]['image_path'], $thumbnailPath);
                $this->flipbook->update(['thumbnail' => $thumbnailPath]);
            }

            // Update flipbook status
            $this->flipbook->update([
                'total_pages' => count($images),
            ]);

            Log::info("Successfully processed flipbook: {$this->flipbook->id}");
        } catch (\Exception $e) {
            Log::error("Failed to process flipbook {$this->flipbook->id}: " . $e->getMessage());

            // Mark flipbook as failed (you might want to add a status field)
            $this->flipbook->update([
                'settings' => array_merge($this->flipbook->settings ?? [], [
                    'processing_failed' => true,
                    'error_message' => $e->getMessage(),
                ])
            ]);

            throw $e; // Re-throw to mark job as failed
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Flipbook processing job failed completely for ID {$this->flipbook->id}: " . $exception->getMessage());

        // Notify admin or update status
        // You could send an email notification here
    }
}
