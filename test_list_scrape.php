<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client(['verify' => false]);
$res = $client->get('https://clair-obscur.fandom.com/wiki/Characters');
$html = (string) $res->getBody();

$crawler = new Crawler($html);

echo "Page Title: " . $crawler->filter('h1')->text() . "\n";

// Try to find character links/images
echo "Checking Gallery items...\n";
$crawler->filter('.wikia-gallery-item')->each(function ($node) {
    echo " - " . $node->text() . "\n";
});

// Try generic links in content
echo "Checking Content links...\n";
$crawler->filter('#mw-content-text a')->each(function ($node) {
    $href = $node->attr('href');
    $text = trim($node->text());
    if (strpos($href, '/wiki/') === 0 && !empty($text)) {
        // echo "Link: $text ($href)\n"; // Too verbose
    }
});
