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
                // Ignore "Database exists" or specific table exists errors if needed, 
                // but our script usually drops tables.
                // However, creating database might fail inside exec if active transaction, but here it is fresh.
                // Or if 'USE' command fails.
                echo "Warning during SQL exec: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "Database structure created.\n";

} catch (\Exception $e) {
    die("Database Initialization Error: " . $e->getMessage());
}

// 2. Scrape and Save Data
try {
    // Assuming Game 1 is created by structure.sql (Expedition 33)
    $GAME_ID = 1;

    echo "Starting Scraping...\n";
    $scraper = new FandomExpedition33Scraper();
    $characterModel = new Character();
    $skillModel = new Skill();

    $characters = $scraper->getCharacters();

    foreach ($characters as $charDTO) {
        echo "Processing {$charDTO->name}...\n";

        // Save Character
        $charId = $characterModel->save($charDTO->name, $charDTO->imageUrl, $charDTO->description, $GAME_ID);
        echo "  - Saved Character ID: $charId\n";

        // Get Skills
        // Note: The original scraper getSkills($charName) logic might need review if it was generating randoms.
        // Assuming it works as per previous state.
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
