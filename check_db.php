<?php
require_once __DIR__ . '/vendor/autoload.php';
use BuildForge\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    $chars = $pdo->query("SELECT count(*) FROM characters")->fetchColumn();
    $skills = $pdo->query("SELECT count(*) FROM skills")->fetchColumn();

    echo "Characters: $chars\n";
    echo "Skills: $skills\n";

    if ($chars > 0) {
        $stmt = $pdo->query("SELECT name FROM characters");
        $names = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Character Names: " . implode(", ", $names) . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
