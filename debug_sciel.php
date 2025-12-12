<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://clair-obscur.fandom.com',
    'verify' => false,
    'http_errors' => false,
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language' => 'en-US,en;q=0.9',
    ]
]);

echo "Fetching /wiki/Sciel...\n";
$res = $client->get('/wiki/Sciel');
echo "Status: " . $res->getStatusCode() . "\n";
echo "Headers:\n";
foreach ($res->getHeaders() as $name => $values) {
    echo $name . ": " . implode(", ", $values) . "\n";
}
echo "\nBody Preview:\n";
echo substr($res->getBody(), 0, 1000) . "\n";
