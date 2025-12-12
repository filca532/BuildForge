<?php
require_once __DIR__ . '/vendor/autoload.php';
use BuildForge\Database;

$desc = "Gustave is a member of Expedition 33 and a main protagonist in Clair Obscur: Expedition 33. Gustave grew up feeling suffocated by the Paintress' constant presence over Lumière. As an engineer, he has dedicated his life to the city’s defence and agricultural systems. Now, as an Expeditioner, he devotes his final year of life to defeating the Paintress and reclaiming a future for Lumière’s children.";

$pdo = Database::getInstance()->getConnection();
$stmt = $pdo->prepare("UPDATE characters SET description = ? WHERE name = 'Gustave'");
$stmt->execute([$desc]);

echo "Gustave description updated successfully.\n";
