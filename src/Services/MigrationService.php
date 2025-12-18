<?php

namespace BuildForge\Services;

use BuildForge\Database;
use BuildForge\Scraping\FandomExpedition33Scraper;
use BuildForge\Models\Character;
use BuildForge\Models\Skill;
use BuildForge\Models\Weapon;
use BuildForge\Services\ImageService;

class MigrationService
{
    public function run(?callable $logger = null): array
    {
        $localLog = [];
        $log = function ($msg) use ($logger, &$localLog) {
            $localLog[] = $msg;
            if ($logger) {
                call_user_func($logger, $msg);
            }
        };

        $log("Starting Migration Service...");

        try {
            // 1. Initialize Database Structure
            $log("Initializing Database Structure...");
            $pdo = Database::getInstance()->getConnection();

            // Adjust path relative to src/Services
            $sqlPath = __DIR__ . '/../../database/structure.sql';
            if (!file_exists($sqlPath)) {
                throw new \Exception("Structure SQL not found at $sqlPath");
            }

            $sql = file_get_contents($sqlPath);
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    try {
                        $pdo->exec($stmt);
                    } catch (\Exception $e) {
                        // Ignore "Database exists" warnings
                        $log("Warning during SQL exec: " . $e->getMessage());
                    }
                }
            }
            $log("Database structure created/reset.");

            // 2. Scraping and Persistence
            $GAME_ID = 1;

            $log("Starting Scraping (Live from Fandom API)...");
            $scraper = new FandomExpedition33Scraper();
            $imageService = new ImageService();
            $characterModel = new Character();
            $skillModel = new Skill();

            $characters = $scraper->getCharacters();
            $characterIds = []; // Map character names to IDs for weapon linking

            foreach ($characters as $charDTO) {
                $log("Processing {$charDTO->name}...");

                // Download and Convert Image
                $localImagePath = $imageService->processImage($charDTO->imageUrl, $charDTO->name, 'characters');

                // Save Character with Local Image Path
                $charId = $characterModel->save($charDTO->name, $localImagePath, $charDTO->description, $GAME_ID);
                $log("  - Saved Character ID: $charId");

                // Get Skills
                $skills = $scraper->getSkills($charDTO->name);
                $log("  - Found " . count($skills) . " skills.");

                foreach ($skills as $skillDTO) {

                    // Process Skill Icon if exists
                    $localIconPath = null;
                    if ($skillDTO->iconPath && strpos($skillDTO->iconPath, 'http') !== false) {
                        $uniqueName = $charDTO->name . '_' . $skillDTO->name;
                        // Fix protocol relative URLs
                        if (strpos($skillDTO->iconPath, '//') === 0) {
                            $skillDTO->iconPath = 'https:' . $skillDTO->iconPath;
                        }
                        $localIconPath = $imageService->processImage($skillDTO->iconPath, $uniqueName, 'skills');
                    }

                    // Process Inline Images in Description
                    $processedDescription = $imageService->processHtmlContent($skillDTO->description, 'skill_inline');

                    // Process Inline Images in Additional Info
                    $processedAdditionalInfo = '';
                    if ($skillDTO->additionalInfo) {
                        $processedAdditionalInfo = $imageService->processHtmlContent(
                            $skillDTO->additionalInfo,
                            'skills/extra_' . strtolower(str_replace(' ', '_', $charDTO->name . '_' . $skillDTO->name))
                        );
                    }

                    $skillModel->save(
                        $skillDTO->name,
                        $processedDescription,
                        $localIconPath,
                        (int) $skillDTO->damage,
                        $skillDTO->cost,
                        (string) $skillDTO->type,
                        $charId,
                        $GAME_ID,
                        $processedAdditionalInfo
                    );
                }

                // Store character ID for weapon linking
                $characterIds[$charDTO->name] = $charId;
            }

            // 3. Scrape and Save Weapons
            $log("Starting Weapons Scraping...");
            $weaponModel = new Weapon();
            $weapons = $scraper->getWeapons();
            $log("Found " . count($weapons) . " weapons.");

            foreach ($weapons as $weaponDTO) {
                // Process weapon image if exists
                $localImagePath = null;
                if ($weaponDTO->imageUrl) {
                    $localImagePath = $imageService->processImage($weaponDTO->imageUrl, $weaponDTO->name, 'weapons');
                }

                // Process element (get or create, download icon)
                $elementId = null;
                if ($weaponDTO->element && !empty(trim($weaponDTO->element))) {
                    $localElementIcon = null;
                    if ($weaponDTO->elementIconUrl) {
                        $localElementIcon = $imageService->processImage(
                            $weaponDTO->elementIconUrl,
                            'element_' . strtolower(str_replace(' ', '_', $weaponDTO->element)),
                            'elements'
                        );
                    }
                    $elementId = $weaponModel->getOrCreateElement($weaponDTO->element, $localElementIcon, $GAME_ID);
                }

                // Save weapon with full stats
                $weaponId = $weaponModel->save(
                    $weaponDTO->name,
                    $weaponDTO->description,
                    $localImagePath,
                    $GAME_ID,
                    $weaponDTO->attack,
                    $elementId,
                    $weaponDTO->scaling
                );

                // Link to characters (handles Gustave/Verso sharing)
                foreach ($weaponDTO->usableBy as $charName) {
                    if (isset($characterIds[$charName])) {
                        $weaponModel->linkToCharacter($weaponId, $characterIds[$charName]);
                    }
                }
            }

            $log("Migration Finished Successfully.");

        } catch (\Exception $e) {
            $log("ERROR: " . $e->getMessage());
            throw $e;
        }

        return $localLog;
    }
}

