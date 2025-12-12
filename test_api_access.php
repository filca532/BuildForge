<?php
require_once __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://clair-obscur.fandom.com',
    'verify' => false,
    'headers' => [
        'User-Agent' => 'BuildForge-Scraper/1.0 (Contact: admin@example.com)'
    ]
]);

$params = [
    'action' => 'query',
    'prop' => 'extracts|pageimages',
    'titles' => 'Lune|Gustave|Sciel|Maelle|Verso|Monoco',
    'explaintext' => 1,
    'exintro' => 1,
    'piprop' => 'original',
    'format' => 'json'
];

echo "Testing MediaWiki API Access...\n";
try {
    $res = $client->get('/api.php', ['query' => $params]);
    echo "Status: " . $res->getStatusCode() . "\n";
    $json = json_decode((string) $res->getBody(), true);

    if (isset($json['query']['pages'])) {
        foreach ($json['query']['pages'] as $page) {
            echo "Title: " . $page['title'] . "\n";
            echo "Desc: " . substr($page['extract'] ?? 'NO EXTRACT', 0, 50) . "...\n";
            echo "Image: " . ($page['original']['source'] ?? 'NO IMAGE') . "\n";
            echo "---\n";
        }
    } else {
        echo "Unexpected Response: ---\n" . substr((string) $res->getBody(), 0, 200) . "\n";
    }

} catch (Exception $e) {
    echo "API Request Failed: " . $e->getMessage() . "\n";
}
