<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use BuildForge\Database;

$client = new Client([
    'base_uri' => 'https://clair-obscur.fandom.com',
    'verify' => false,
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ]
]);

echo "Fetching Characters Listing Page...\n";
try {
    $res = $client->get('/wiki/Characters');
    $html = (string) $res->getBody();
} catch (Exception $e) {
    die("Failed to fetch characters page: " . $e->getMessage());
}

$crawler = new Crawler($html);
$pdo = Database::getInstance()->getConnection();

$targets = ['Sciel', 'Verso', 'Monoco'];
$found = [];

// Method 1: Check wikia-gallery-items
$crawler->filter('.wikia-gallery-item')->each(function (Crawler $node) use (&$found) {
    $text = $node->text();
    $link = $node->filter('a.image');
    if ($link->count() > 0) {
        $img = $link->filter('img');
        if ($img->count() > 0) {
            $src = $img->attr('src');
            // Check if one of our targets is in the text
            foreach (['Sciel', 'Verso', 'Monoco'] as $t) {
                if (stripos($text, $t) !== false) {
                    // Start of src often has /revision/latest... trim it to get cleaner url or use as is
                    // Usually we want to remove /scale-to-width-down/X
                    $cleanSrc = preg_replace('/\/scale-to-width-down\/\d+/', '', $src);
                    $found[$t] = $cleanSrc;
                }
            }
        }
    }
});

echo "Found images: " . print_r($found, true) . "\n";

foreach ($targets as $name) {
    if (isset($found[$name])) {
        echo "Downloading $name...\n";
        $url = $found[$name];
        $content = @file_get_contents($url);
        if ($content) {
            $filename = "img/characters/" . strtolower($name) . ".png";
            $localPath = __DIR__ . '/public/' . $filename;
            file_put_contents($localPath, $content);
            echo " - Saved to $filename\n";

            $stmt = $pdo->prepare("UPDATE characters SET image_url = ? WHERE name = ?");
            $stmt->execute([$filename, $name]);
        } else {
            echo " - Failed download $url\n";
        }
    } else {
        echo " - No image found for $name (keeping placeholder)\n";
    }
}
