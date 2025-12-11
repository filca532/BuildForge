<?php

require_once __DIR__ . '/vendor/autoload.php';

use BuildForge\Scraping\FandomExpedition33Scraper;

$scraper = new FandomExpedition33Scraper();
echo "Starting scrape...\n";
$characters = $scraper->getCharacters();

foreach ($characters as $char) {
    echo "--------------------------------------------------\n";
    echo "Name: " . $char->name . "\n";
    echo "Image: " . ($char->imageUrl ?? 'N/A') . "\n";
    echo "Description: " . substr($char->description, 0, 100) . "...\n";

    // Test Skills Scraping
    $skills = $scraper->getSkills($char->name);
    echo "Skills found: " . count($skills) . "\n";
    if (count($skills) > 0) {
        foreach (array_slice($skills, 0, 3) as $skill) {
            echo "  - {$skill->name} ({$skill->cost}, {$skill->damage} dmg)\n";
        }
    }
}

echo "\nDone.\n";
