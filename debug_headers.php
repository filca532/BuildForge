<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client(['verify' => false]);
// Fetch Skills page parse
$res = $client->get('https://clair-obscur.fandom.com/api.php', [
    'query' => [
        'action' => 'parse',
        'page' => 'Skills',
        'prop' => 'text',
        'format' => 'json'
    ]
]);
$json = json_decode((string) $res->getBody(), true);
$html = $json['parse']['text']['*'];
$crawler = new Crawler($html);

echo "H2 Headers found on Skills Page:\n";
$crawler->filter('h2')->each(function (Crawler $node) {
    echo " - " . strip_tags($node->text()) . "\n";
});
