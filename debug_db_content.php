<?php
require_once __DIR__ . '/vendor/autoload.php';

use BuildForge\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query("SELECT id, name, image_url, description FROM characters");
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "--- DB DEBUG REPORT ---\n";
    foreach ($characters as $char) {
        echo "ID: " . $char['id'] . " | Name: " . $char['name'] . "\n";
        echo "  Img: " . ($char['image_url'] ? $char['image_url'] : "[NULL]") . "\n";
        echo "  Desc: " . substr($char['description'], 0, 60) . "...\n";
        echo "-----------------------\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
