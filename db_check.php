<?php
require 'vendor/autoload.php';
require 'src/Database.php';

use BuildForge\Database;

try {
    $pdo = Database::getInstance()->getConnection();
    // Select char name, skill name, icon path, and additional info
    $stmt = $pdo->query("
        SELECT 
            c.name as character_name, 
            s.name as skill_name, 
            s.icon_path, 
            s.additional_info 
        FROM skills s 
        JOIN characters c ON s.character_id = c.id
        ORDER BY c.name, s.name
    ");

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $iconStatus = !empty($row['icon_path']) ? "OK ({$row['icon_path']})" : "MISSING";
        $infoStatus = !empty($row['additional_info']) ? "HAS INFO" : "EMPTY";
        echo "{$row['character_name']} - {$row['skill_name']}: Icon=$iconStatus | Info=$infoStatus\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
