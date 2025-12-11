<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client(['verify' => false]);
echo "Fetching Characters Listing Page...\n";
$res = $client->get('https://clair-obscur.fandom.com/wiki/Characters');
$html = (string) $res->getBody();

$crawler = new Crawler($html);

// Find links in gallery items
echo "Gallery Items:\n";
$crawler->filter('.wikia-gallery-item .lightbox-caption a')->each(function ($node) {
    echo " - Text: " . $node->text() . " | Href: " . $node->attr('href') . "\n";
});

// Find links in any list
echo "\nList Items:\n";
$crawler->filter('#mw-content-text ul li a')->each(function ($node) {
    $href = $node->attr('href');
    if (strpos($href, '/wiki/') !== false && strpos($href, ':') === false) {
        echo " - Text: " . $node->text() . " | Href: " . $href . "\n";
    }
});
