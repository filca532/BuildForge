<?php
require_once __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;

$client = new Client(['verify' => false, 'allow_redirects' => true]);

$candidates = [
    'COE33_char_icon_Monoco.png', // Tried, failed
    'Monoco.png',
    'Monoco_icon.png',
    'Character_Monoco.png',
    'Monoco_Portrait.png',
    'Expedition33_Monoco.png',
    'COE33_Monoco.png',
    'Monoco_render.png',
    'Monoco_Infobox.png'
];

foreach ($candidates as $file) {
    $url = "https://clair-obscur.fandom.com/wiki/Special:FilePath/" . $file;
    echo "Checking $file ... ";
    try {
        $res = $client->head($url); // Use HEAD to be faster
        $status = $res->getStatusCode();
        echo "$status\n";
        if ($status == 200) {
            echo "MATCH FOUND: $file\n";
            // Download it
            file_put_contents("public/img/characters/monoco.png", file_get_contents($url));
            // Update DB
            require_once 'src/Database.php';
            $pdo = \BuildForge\Database::getInstance()->getConnection();
            $pdo->prepare("UPDATE characters SET image_url = 'img/characters/monoco.png' WHERE name = 'Monoco'")->execute();
            echo "DB Updated for Monoco.\n";
            exit;
        }
    } catch (Exception $e) {
        echo "404/Error\n";
    }
}
echo "No image found for Monoco.\n";
