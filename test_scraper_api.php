<?php
require_once __DIR__ . '/vendor/autoload.php';
use BuildForge\Scraping\FandomExpedition33Scraper;

echo "Testing API Scraper...\n";
$scraper = new FandomExpedition33Scraper();
$chars = $scraper->getCharacters();

foreach ($chars as $c) {
    echo "Name: " . $c->name . "\n";
    echo "Desc: " . substr($c->description, 0, 70) . "...\n";
    echo "Image: " . $c->imageUrl . "\n";
    echo "---\n";
}
