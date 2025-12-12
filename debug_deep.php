<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://clair-obscur.fandom.com',
    'verify' => false,
    'http_errors' => false, // Don't throw on 404
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124'
    ]
]);

// Test Sciel Page
echo "Fetching /wiki/Sciel...\n";
$res = $client->get('/wiki/Sciel');
echo "Status: " . $res->getStatusCode() . "\n";
$html = (string) $res->getBody();
echo "Length: " . strlen($html) . "\n";
if (strpos($html, 'og:description') !== false) {
    echo "Found og:description\n";
} else {
    echo "NO og:description found.\n";
}

// Test Gustave Image
$imgUrl = "https://static.wikia.nocookie.net/clair-obscur/images/1/1d/COE33_char_icon_Gustave.png/revision/latest?cb=20250506001950";
echo "\nTesting Image: $imgUrl\n";
try {
    $imgRes = $client->get($imgUrl);
    echo "Img Status: " . $imgRes->getStatusCode() . "\n";
    echo "Content-Type: " . implode(', ', $imgRes->getHeader('Content-Type')) . "\n";
} catch (Exception $e) {
    echo "Img Error: " . $e->getMessage() . "\n";
}
