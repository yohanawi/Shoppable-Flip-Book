<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PdfConverterService
{
    /**
     * Check if Imagick is available
     *
     * @return bool
     */
    public function isImagickAvailable()
    {
        return extension_loaded('imagick') && class_exists('Imagick');
    }

    /**
     * Get installation instructions for Imagick
     *
     * @return string
     */
    public function getImagickInstallationInstructions()
    {
        $os = PHP_OS_FAMILY;

        $instructions = "Imagick extension is required but not installed.\n\n";

        if ($os === 'Windows') {
            $instructions .= "Windows Installation:\n";
            $instructions .= "1. Download Imagick DLL from: https://pecl.php.net/package/imagick\n";
            $instructions .= "2. Choose the version matching your PHP version (check with 'php -v')\n";
            $instructions .= "3. Extract php_imagick.dll to your PHP ext folder (e.g., C:\\laragon\\bin\\php\\php8.x\\ext\\)\n";
            $instructions .= "4. Add 'extension=imagick' to your php.ini file\n";
            $instructions .= "5. Restart your web server (Apache/Nginx)\n";
            $instructions .= "6. Verify with: php -m | findstr imagick\n\n";
            $instructions .= "Alternative for Windows:\n";
            $instructions .= "- Use Laragon's Quick Add > PHP Extensions > Imagick (if available)\n";
        } elseif ($os === 'Linux') {
            $instructions .= "Linux Installation:\n";
            $instructions .= "Ubuntu/Debian:\n";
            $instructions .= "  sudo apt-get update\n";
            $instructions .= "  sudo apt-get install php-imagick\n";
            $instructions .= "  sudo systemctl restart apache2 # or php-fpm\n\n";
            $instructions .= "CentOS/RHEL:\n";
            $instructions .= "  sudo yum install php-imagick\n";
            $instructions .= "  sudo systemctl restart httpd\n\n";
        } elseif ($os === 'Darwin') {
            $instructions .= "macOS Installation:\n";
            $instructions .= "Using Homebrew:\n";
            $instructions .= "  brew install imagemagick\n";
            $instructions .= "  pecl install imagick\n";
            $instructions .= "  Add 'extension=imagick.so' to your php.ini\n\n";
        }

        $instructions .= "Verify installation: php -m | grep imagick";

        return $instructions;
    }

    /**
     * Convert PDF to images
     *
     * @param string $pdfPath - Full path to PDF file
     * @param string $outputDir - Directory to save images
     * @param int $resolution - DPI resolution (default 150)
     * @return array - Array of generated image paths
     */
    public function convertPdfToImages($pdfPath, $outputDir, $resolution = 150)
    {
        if (!$this->isImagickAvailable()) {
            throw new Exception($this->getImagickInstallationInstructions());
        }

        try {
            $imagick = new \Imagick();
            $imagick->setResolution($resolution, $resolution);
            $imagick->readImage($pdfPath);

            $totalPages = $imagick->getNumberImages();
            $images = [];

            foreach ($imagick as $pageNumber => $page) {
                $page->setImageFormat('png');
                $page->setImageCompression(\Imagick::COMPRESSION_JPEG);
                $page->setImageCompressionQuality(90);

                // Set white background
                $page->setImageBackgroundColor('white');
                $page = $page->flattenImages();

                $pageNum = str_pad($pageNumber + 1, 4, '0', STR_PAD_LEFT);
                $filename = "page_{$pageNum}.png";
                $filepath = $outputDir . '/' . $filename;

                // Save to storage
                Storage::disk('public')->put($filepath, $page->getImageBlob());

                $images[] = [
                    'page_number' => $pageNumber + 1,
                    'image_path' => $filepath,
                    'width' => $page->getImageWidth(),
                    'height' => $page->getImageHeight(),
                ];

                $page->clear();
            }

            $imagick->clear();
            $imagick->destroy();

            return $images;
        } catch (Exception $e) {
            Log::error('PDF Conversion Error: ' . $e->getMessage());
            throw new Exception('Failed to convert PDF to images: ' . $e->getMessage());
        }
    }

    /**
     * Generate thumbnail from first page
     *
     * @param string $imagePath - Path to first page image
     * @param string $outputPath - Path for thumbnail
     * @param int $width - Thumbnail width
     * @param int $height - Thumbnail height
     * @return string - Path to thumbnail
     */
    public function generateThumbnail($imagePath, $outputPath, $width = 300, $height = 400)
    {
        if (!$this->isImagickAvailable()) {
            throw new Exception('Imagick extension is required for thumbnail generation.');
        }

        try {
            $imagick = new \Imagick(Storage::disk('public')->path($imagePath));
            $imagick->thumbnailImage($width, $height, true);

            Storage::disk('public')->put($outputPath, $imagick->getImageBlob());

            $imagick->clear();
            $imagick->destroy();

            return $outputPath;
        } catch (Exception $e) {
            Log::error('Thumbnail Generation Error: ' . $e->getMessage());
            throw new Exception('Failed to generate thumbnail: ' . $e->getMessage());
        }
    }

    /**
     * Extract text from PDF using pdftotext (if available)
     *
     * @param string $pdfPath
     * @param int $pageNumber
     * @return string|null
     */
    public function extractTextFromPage($pdfPath, $pageNumber)
    {
        // This requires pdftotext to be installed on the system
        // Alternative: Use PDF parser libraries

        try {
            $output = shell_exec("pdftotext -f {$pageNumber} -l {$pageNumber} {$pdfPath} -");
            return $output ?: null;
        } catch (Exception $e) {
            Log::warning('Text extraction failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get PDF page count
     *
     * @param string $pdfPath
     * @return int
     */
    public function getPageCount($pdfPath)
    {
        if (!$this->isImagickAvailable()) {
            // Try alternative methods first
            $pageCount = $this->getPageCountFallback($pdfPath);
            if ($pageCount > 0) {
                return $pageCount;
            }

            throw new Exception($this->getImagickInstallationInstructions());
        }

        try {
            $imagick = new \Imagick();
            $imagick->pingImage($pdfPath);
            $pageCount = $imagick->getNumberImages();
            $imagick->clear();
            $imagick->destroy();

            return $pageCount;
        } catch (Exception $e) {
            Log::error('Page count error: ' . $e->getMessage());

            // Try fallback
            $pageCount = $this->getPageCountFallback($pdfPath);
            if ($pageCount > 0) {
                return $pageCount;
            }

            return 0;
        }
    }

    /**
     * Get page count using fallback methods (without Imagick)
     *
     * @param string $pdfPath
     * @return int
     */
    protected function getPageCountFallback($pdfPath)
    {
        // Method 1: Try pdfinfo command
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $output = shell_exec("pdfinfo \"$pdfPath\" 2>&1 | findstr Pages");
            } else {
                $output = shell_exec("pdfinfo \"$pdfPath\" 2>&1 | grep Pages");
            }

            if ($output && preg_match('/Pages:\s+(\d+)/', $output, $matches)) {
                Log::info('PDF page count obtained using pdfinfo: ' . $matches[1]);
                return (int)$matches[1];
            }
        } catch (Exception $e) {
            Log::debug('pdfinfo method failed: ' . $e->getMessage());
        }

        // Method 2: Count /Type /Page occurrences in PDF
        try {
            $content = file_get_contents($pdfPath);
            if ($content) {
                preg_match_all("/\/Type\s*\/Page[^s]/", $content, $matches);
                if (!empty($matches[0])) {
                    Log::info('PDF page count obtained by parsing: ' . count($matches[0]));
                    return count($matches[0]);
                }
            }
        } catch (Exception $e) {
            Log::debug('PDF parsing method failed: ' . $e->getMessage());
        }

        // Method 3: Try FPDI library if available
        if (class_exists('setasign\\Fpdi\\Fpdi')) {
            try {
                $pdf = new \setasign\Fpdi\Fpdi();
                $pageCount = $pdf->setSourceFile($pdfPath);
                Log::info('PDF page count obtained using FPDI: ' . $pageCount);
                return $pageCount;
            } catch (Exception $e) {
                Log::debug('FPDI method failed: ' . $e->getMessage());
            }
        }

        return 0;
    }
}
