<?php
require_once __DIR__ . '/vendor/autoload.php';
use BuildForge\Scraping\FandomExpedition33Scraper;

echo "Testing Stealth Scraper...\n";
$scraper = new FandomExpedition33Scraper();

// Try strictly with one known character
$chars = $scraper->getCharacters();

foreach ($chars as $c) {
    echo "Name: " . $c->name . "\n";
    echo "Desc Start: " . substr($c->description, 0, 50) . "...\n";
    echo "Image: " . $c->imageUrl . "\n";
    echo "---\n";
}
