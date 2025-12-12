<?php
require_once __DIR__ . '/vendor/autoload.php';

use BuildForge\Database;

echo "Downloading Images Locally...\n";

$images = [
    'Gustave' => 'https://static.wikia.nocookie.net/clair-obscur/images/1/1d/COE33_char_icon_Gustave.png/revision/latest?cb=20241017005437', // Removed future timestamp, used a generic query, or just base
    // Actually, Fandom images often work better without query strings if revision is there.
    // Let's try the base URL + revision
    'Gustave' => 'https://static.wikia.nocookie.net/clair-obscur/images/1/1d/COE33_char_icon_Gustave.png',
    'Lune' => 'https://static.wikia.nocookie.net/clair-obscur/images/9/91/COE33_char_icon_Lune.png',
    'Sciel' => 'https://static.wikia.nocookie.net/clair-obscur/images/2/24/COE33_char_icon_Sciel.png',
    'Maelle' => 'https://static.wikia.nocookie.net/clair-obscur/images/d/d6/COE33_char_icon_Maelle.png',
    'Verso' => 'https://static.wikia.nocookie.net/clair-obscur/images/e/e0/COE33_char_icon_Verso.png',
    'Monoco' => 'https://static.wikia.nocookie.net/clair-obscur/images/a/a5/COE33_char_icon_Monoco.png'
];

$pdo = Database::getInstance()->getConnection();

foreach ($images as $name => $url) {
    echo "Processing $name...\n";
    $content = @file_get_contents($url);
    if ($content) {
        $filename = "img/characters/" . strtolower($name) . ".png";
        $localPath = __DIR__ . '/public/' . $filename;
        file_put_contents($localPath, $content);
        echo " - Downloaded to $filename\n";

        $stmt = $pdo->prepare("UPDATE characters SET image_url = ? WHERE name = ?");
        $stmt->execute([$filename, $name]);
    } else {
        echo " - FAILED to download $url\n";
    }
}