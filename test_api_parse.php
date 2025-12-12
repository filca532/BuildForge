<?php
require_once __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://clair-obscur.fandom.com',
    'verify' => false,
    'headers' => ['User-Agent' => 'BuildForge/1.0']
]);

echo "Testing API Parse for Description...\n";
try {
    // Parse the 'Lune' page
    $res = $client->get('/api.php', [
        'query' => [
            'action' => 'parse',
            'page' => 'Lune',
            'prop' => 'text',
            'format' => 'json'
        ]
    ]);

    $json = json_decode((string) $res->getBody(), true);

    if (isset($json['parse']['text']['*'])) {
        $html = $json['parse']['text']['*'];
        echo "HTML Length: " . strlen($html) . "\n";

        // Simple regex to find the first paragraph
        if (preg_match('/<p>(.*?)<\/p>/s', $html, $matches)) {
            echo "First Paragraph Preview:\n" . strip_tags(substr($matches[1], 0, 200)) . "...\n";
        } else {
            echo "No paragraph found in API output.\n";
        }
    } else {
        echo "API Parse Failed (No text returned).\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
