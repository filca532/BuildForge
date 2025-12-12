<?php
require_once __DIR__ . '/vendor/autoload.php';
use BuildForge\Database;
use GuzzleHttp\Client;

$client = new Client(['verify' => false, 'allow_redirects' => true]);
$pdo = Database::getInstance()->getConnection();

$files = [
    'Sciel' => 'COE33_char_icon_Sciel.png',
    'Verso' => 'COE33_char_icon_Verso.png',
    'Monoco' => 'COE33_char_icon_Monoco.png'
];

foreach ($files as $name => $file) {
    $url = "https://clair-obscur.fandom.com/wiki/Special:FilePath/" . $file;
    echo "Trying $name via $url ...\n";
    try {
        $res = $client->get($url);
        echo " - Status: " . $res->getStatusCode() . "\n";
        $content = (string) $res->getBody();
        if (strlen($content) > 1000) {
            $filename = "img/characters/" . strtolower($name) . ".png";
            file_put_contents("public/" . $filename, $content);
            echo " - SUCCESS! Saved to $filename\n";

            $stmt = $pdo->prepare("UPDATE characters SET image_url = ? WHERE name = ?");
            $stmt->execute([$filename, $name]);
        } else {
            echo " - Content too small (" . strlen($content) . "), likely blocked.\n";
        }
    } catch (Exception $e) {
        echo " - Failed: " . $e->getMessage() . "\n";
    }
}
