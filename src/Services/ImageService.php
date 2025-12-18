<?php

namespace BuildForge\Services;

use GuzzleHttp\Client;

class ImageService
{
    private Client $client;
    private string $publicDir;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => false,
            'headers' => ['User-Agent' => 'BuildForge/1.0']
        ]);
        $this->publicDir = realpath(__DIR__ . '/../../public/img');
    }

    /**
     * Downloads an image, converts to WebP, saves locally.
     * @param string $subDir Relative to public/img (e.g., 'characters', 'skills')
     */
    public function processImage(string $url, string $name, string $subDir = 'characters'): string
    {
        try {
            $targetDir = $this->publicDir . '/' . $subDir . '/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // 1. Download
            $response = $this->client->get($url);
            $imageContent = $response->getBody()->getContents();

            // 2. Create GD Image
            $image = @imagecreatefromstring($imageContent);
            if (!$image) {
                throw new \Exception("Could not create image from content");
            }

            // 3. Resize if too massive (optional, but good for icons)
            // For now, keep original size but ensure WebP format

            // Handle Transparency
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);

            // 4. Save as WebP
            // Sanitize filename
            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower(str_replace(' ', '_', $name)));
            $filename = $safeName . '.webp';
            $outputPath = $targetDir . $filename;

            if (!imagewebp($image, $outputPath, 85)) {
                throw new \Exception("Failed to save WebP to $outputPath");
            }

            imagedestroy($image);

            echo "   [ImageService] Saved $subDir/$filename\n";

            return "img/$subDir/$filename";

        } catch (\Exception $e) {
            echo "   [ImageService] Error processing image for $name: " . $e->getMessage() . "\n";
            return "img/placeholder_character.png";
        }
    }

    /**
     * Parses HTML, finds <img> tags, downloads them, and replaces paths with local ones.
     * @param string $html Content containing <img> tags
     * @param string $prefix Prefix for filenames (e.g. skill name)
     * @return string Modified HTML
     */
    public function processHtmlContent(string $html, string $prefix): string
    {
        if (empty($html)) {
            return $html;
        }

        // Use DOMDocument to parse and manipulate
        // Suppress warnings for malformed HTML fragments
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        // UTF-8 hack to handle encoding issues and preserve UTF-8 characters
        // Split strings to avoid PHP short tag confusion
        $dom->loadHTML('<' . '?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        if ($images->length === 0) {
            return $html;
        }

        foreach ($images as $img) {
            $src = $img->getAttribute('src');
            $dataSrc = $img->getAttribute('data-src'); // Handle lazy loading if present

            $url = $dataSrc ?: $src;

            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                $filename = 'icon_' . substr(md5($url), 0, 10);

                try {
                    // Helper: clean URL
                    $url = preg_replace('/\/revision\/latest.*/', '', $url);

                    $localPath = $this->processImage($url, $filename, 'icons');

                    // Use relative path for browser
                    $img->setAttribute('src', $localPath);
                    $img->removeAttribute('data-src'); // Clean up
                    $img->setAttribute('class', 'inline-icon'); // Uniform styling

                } catch (\Exception $e) {
                    // Keep original or placeholder
                }
            }
        }

        // Save HTML fragments (body only)
        $body = $dom->saveHTML();
        // Remove the xml encoding hack wrapper
        $body = str_replace('<' . '?xml encoding="utf-8" ?>', '', $body);
        return trim($body);
    }
}
