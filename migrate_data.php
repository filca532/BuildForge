<?php

require_once __DIR__ . '/vendor/autoload.php';

use BuildForge\Database;
use BuildForge\Scraping\FandomExpedition33Scraper;
use BuildForge\Models\Character;
use BuildForge\Models\Skill;

echo "Starting Migration Process...\n";

// 1. Initialize Database Structure
try {
    echo "Initializing Database Structure...\n";
    $pdo = Database::getInstance()->getConnection();
    // Re-execute structure.sql to set up nexus_rpg
    $sql = file_get_contents(__DIR__ . '/database/structure.sql');

    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            try {
                $pdo->exec($stmt);
            } catch (Exception $e) {
                // Ignore "Database exists" warnings
                echo "Warning during SQL exec: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "Database structure created.\n";

} catch (\Exception $e) {
    die("Database Initialization Error: " . $e->getMessage());
}

use BuildForge\Services\ImageService;

// ...

try {
    // Assuming Game 1 is created by structure.sql (Expedition 33)
    $GAME_ID = 1;

    echo "Starting Scraping (Live from Fandom API)...\n";
    $scraper = new FandomExpedition33Scraper();
    $imageService = new ImageService();
    $characterModel = new Character();
    $skillModel = new Skill();

    $characters = $scraper->getCharacters();

    foreach ($characters as $charDTO) {
        echo "Processing {$charDTO->name}...\n";

        // Download and Convert Image
        $localImagePath = $imageService->processImage($charDTO->imageUrl, $charDTO->name);

        // Save Character with Local Image Path
        $charId = $characterModel->save($charDTO->name, $localImagePath, $charDTO->description, $GAME_ID);
        echo "  - Saved Character ID: $charId\n";
        echo "  - Image Path: {$localImagePath}\n";
        echo "  - Desc Preview: " . substr($charDTO->description, 0, 50) . "...\n";


        // Get Skills
        $skills = $scraper->getSkills($charDTO->name);
        echo "  - Found " . count($skills) . " skills.\n";

        foreach ($skills as $skillDTO) {
            $skillModel->save(
                $skillDTO->name,
                $skillDTO->description,
                (int) $skillDTO->damage,
                $skillDTO->cost,
                (string) $skillDTO->type,
                $charId,
                $GAME_ID
            );
        }
    }

    echo "Migration Script Finished.\n";

    // VERIFICATION
    echo "\n[VERIFICATION]\n";
    $conn = Database::getInstance()->getConnection();
    $countChar = $conn->query("SELECT count(*) FROM characters")->fetchColumn();
    $countSkill = $conn->query("SELECT count(*) FROM skills")->fetchColumn();

    echo "Characters in DB: $countChar\n";
    echo "Skills in DB: $countSkill\n";

    if ($countChar == 0) {
        echo "CRITICAL WARNING: Database is empty despite migration!\n";
    } else {
        echo "SUCCESS: Data persisted correctly.\n";
    }

} catch (\Exception $e) {
    die("Migration Error: " . $e->getMessage());
}
