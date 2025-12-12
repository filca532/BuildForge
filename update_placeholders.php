<?php
require_once __DIR__ . '/vendor/autoload.php';
use BuildForge\Database;

$pdo = Database::getInstance()->getConnection();
$pdo->exec("UPDATE characters SET image_url = 'img/placeholder_character.png' WHERE name IN ('Sciel', 'Verso', 'Monoco')");
echo "Updated Sciel, Verso, Monoco to use placeholder image.\n";
