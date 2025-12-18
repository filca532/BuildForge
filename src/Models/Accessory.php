<?php

namespace BuildForge\Models;

use BuildForge\Database;
use PDO;

class Accessory
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function save(
        string $name,
        int $categoryId,
        ?string $description = null,
        ?string $imageUrl = null,
        ?string $wikiUrl = null,
        ?string $effect = null,
        ?string $cost = null,
        int $gameId = 1
    ): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO accessories (game_id, category_id, name, description, image_url, wiki_url, effect, cost) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
                description = VALUES(description), 
                image_url = VALUES(image_url), 
                wiki_url = VALUES(wiki_url),
                effect = VALUES(effect),
                cost = VALUES(cost)
        ");
        $stmt->execute([$gameId, $categoryId, $name, $description, $imageUrl, $wikiUrl, $effect, $cost]);

        if ($stmt->rowCount() > 0 && $this->pdo->lastInsertId() > 0) {
            return (int) $this->pdo->lastInsertId();
        }

        // Get existing ID
        $stmt = $this->pdo->prepare("SELECT id FROM accessories WHERE name = ? AND game_id = ?");
        $stmt->execute([$name, $gameId]);
        return (int) $stmt->fetchColumn();
    }

    public function linkToCharacter(int $accessoryId, int $characterId): void
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO character_accessories (character_id, accessory_id) VALUES (?, ?)");
        $stmt->execute([$characterId, $accessoryId]);
    }

    public function getByCharacter(int $characterId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, ac.name as category_name, ac.slug as category_slug 
            FROM accessories a
            INNER JOIN character_accessories ca ON a.id = ca.accessory_id
            INNER JOIN accessory_categories ac ON a.category_id = ac.id
            WHERE ca.character_id = ?
            ORDER BY ac.name ASC, a.name ASC
        ");
        $stmt->execute([$characterId]);
        return $stmt->fetchAll();
    }

    public function getByCategory(int $categoryId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM accessories 
            WHERE category_id = ?
            ORDER BY name ASC
        ");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public function getAll(int $gameId = 1): array
    {
        $stmt = $this->pdo->prepare("
            SELECT a.*, ac.name as category_name, ac.slug as category_slug 
            FROM accessories a
            INNER JOIN accessory_categories ac ON a.category_id = ac.id
            WHERE a.game_id = ?
            ORDER BY ac.name ASC, a.name ASC
        ");
        $stmt->execute([$gameId]);
        return $stmt->fetchAll();
    }
}
