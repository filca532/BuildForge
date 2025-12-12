<?php

namespace BuildForge\Services;

use GuzzleHttp\Client;

class ImageService
{
    private Client $client;
    private string $targetDir;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => false,
            'headers' => ['User-Agent' => 'BuildForge/1.0']
        ]);
        $this->targetDir = __DIR__ . '/../../public/img/characters/';

        if (!is_dir($this->targetDir)) {
            mkdir($this->targetDir, 0777, true);
        }
    }

    /**
     * Downloads an image from a URL, converts it to WebP, and saves it locally.
     * Returns the relative path to be stored in the DB (e.g., 'img/characters/name.webp').
     */
    public function processImage(string $url, string $characterName): string
    {
        try {

            // 1. Download
            $response = $this->client->get($url);
            $imageContent = $response->getBody()->getContents();

            // 2. Create GD Image
            $image = @imagecreatefromstring($imageContent);
            if (!$image) {
                throw new \Exception("Could not create image from content");
            }

            // 3. Handle Transparency
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);

            // 4. Save as WebP
            $filename = strtolower($characterName) . '.webp';
            $outputPath = $this->targetDir . $filename;

            // Quality 85 is usually a good balance
            if (!imagewebp($image, $outputPath, 85)) {
                throw new \Exception("Failed to save WebP to $outputPath");
            }

            imagedestroy($image);

            echo "   [ImageService] Saved $filename\n";

            return "img/characters/$filename";

        } catch (\Exception $e) {
            echo "   [ImageService] Error processing image for $characterName: " . $e->getMessage() . "\n";
            // Return placeholder if failed
            return "img/placeholder_character.png";
        }
    }
}
