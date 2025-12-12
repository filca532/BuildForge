<?php
require_once __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client([
    'base_uri' => 'https://clair-obscur.fandom.com',
    'verify' => false,
    'headers' => ['User-Agent' => 'BuildForge/1.0']
]);

echo "Testing API Parse for Characters List...\n";
try {
    $res = $client->get('/api.php', [
        'query' => [
            'action' => 'parse',
            'page' => 'Characters',
            'prop' => 'text',
            'format' => 'json'
        ]
    ]);

    $json = json_decode((string) $res->getBody(), true);

    if (isset($json['parse']['text']['*'])) {
        $html = $json['parse']['text']['*'];
        $crawler = new Crawler($html);

        echo "Searching for Tables...\n";
        $crawler->filter('table')->each(function (Crawler $table, $i) {
            echo "Table #$i:\n";
            $table->filter('tr')->each(function (Crawler $row, $j) {
                if ($j < 5) { // Show first 5 rows
                    $cells = $row->filter('th, td')->each(function (Crawler $cell) {
                        return trim($cell->text());
                    });
                    echo "  Row $j: " . implode(" | ", $cells) . "\n";
                }
            });
            echo "---\n";
        });
    } else {
        echo "API Parse Failed (No text).\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
