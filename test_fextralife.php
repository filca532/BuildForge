<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client([
    'verify' => false,
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    ]
]);

$url = 'https://expedition33.wiki.fextralife.com/Skills';
echo "Fetching $url ...\n";

try {
    $response = $client->get($url);
    $html = (string) $response->getBody();

    $crawler = new Crawler($html);

    // Look for the table. Often has class 'wiki_table'
    $tables = $crawler->filter('table');
    echo "Found " . $tables->count() . " tables.\n";

    if ($tables->count() > 0) {
        $table = $tables->first();

        // Inspect Header
        $table->filter('tr')->each(function (Crawler $row, $i) {
            if ($i < 2) { // Print first 2 rows (Header + First Data)
                echo "\n--- Row $i ---\n";
                $row->filter('td, th')->each(function (Crawler $cell, $j) {
                    echo "  Col $j HTML: " . trim(substr($cell->html(), 0, 150)) . "...\n";
                    echo "  Col $j Text: " . trim($cell->text()) . "\n";
                });
            }
        });
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
