<?php
require_once __DIR__ . '/vendor/autoload.php';

use BuildForge\Database;
use BuildForge\Models\Character;

echo "Debug DB Script\n";
echo "---------------\n";

try {
    $pdo = Database::getInstance()->getConnection();
    echo "Connected to DB: " . $pdo->query('select database()')->fetchColumn() . "\n";

    // Check Games
    $gamesCount = $pdo->query("SELECT count(*) FROM games")->fetchColumn();
    echo "Games count: $gamesCount\n";

    if ($gamesCount == 0) {
        echo "No games found! Inserting one...\n";
        $pdo->exec("INSERT INTO games (name, slug, description) VALUES ('Expedition 33', 'expedition-33', 'Debug Game')");
        $gameId = $pdo->lastInsertId();
        echo "Inserted Game ID: $gameId\n";
    } else {
        $gameId = 1;
        echo "Game ID 1 exists.\n";
    }

    // Try Inserting Character
    echo "Attempting to insert 'TestChar'...\n";
    $charModel = new Character();
    $id = $charModel->save("TestChar", "http://img.com/a.png", "Desc", $gameId);
    echo "Inserted Character ID: $id\n";

    // Verify Insertion
    $verify = $pdo->query("SELECT * FROM characters WHERE id = $id")->fetch();
    print_r($verify);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
