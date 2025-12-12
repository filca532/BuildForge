<?php
require_once __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client([
    'base_uri' => 'https://clair-obscur.fandom.com',
    'verify' => false,
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
    ]
]);

$chars = ['Lune', 'Maelle', 'Sciel', 'Verso', 'Monoco'];
$descriptions = [];

foreach ($chars as $name) {
    echo "Fetching $name...\n";
    try {
        $res = $client->get("/wiki/$name");
        $html = (string) $res->getBody();
        $crawler = new Crawler($html);

        // Try to find the first real paragraph
        // Usually in .mw-parser-output > p that is not a class
        $desc = "";
        $crawler->filter('.mw-parser-output > p')->each(function (Crawler $node) use (&$desc) {
            $t = trim($node->text());
            if (empty($desc) && strlen($t) > 50 && stripos($t, 'is') !== false) {
                $desc = $t;
            }
        });

        // Fallback: og:description
        if (empty($desc)) {
            if ($crawler->filter('meta[property="og:description"]')->count() > 0) {
                $desc = $crawler->filter('meta[property="og:description"]')->attr('content');
            }
        }

        if (!empty($desc)) {
            $descriptions[$name] = $desc;
            echo " - Found: " . substr($desc, 0, 50) . "...\n";
        } else {
            echo " - No description found (Block/Empty).\n";
        }

    } catch (Exception $e) {
        echo " - Error: " . $e->getMessage() . "\n";
    }
}

// Update DB
if (!empty($descriptions)) {
    require_once 'src/Database.php';
    $pdo = \BuildForge\Database::getInstance()->getConnection();
    foreach ($descriptions as $n => $d) {
        $stmt = $pdo->prepare("UPDATE characters SET description = ? WHERE name = ?");
        $stmt->execute([$d, $n]);
        echo "Updated DB for $n.\n";
    }
}
