<?php

namespace BuildForge\Models;

use BuildForge\Database;
use PDO;

class AccessoryCategory
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function save(
        string $name,
        string $slug,
        ?string $description = null,
        ?string $iconUrl = null,
        int $maxEquippable = 1,
        int $gameId = 1
    ): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO accessory_categories (game_id, name, slug, description, icon_url, max_equippable) 
            VALUES (?, ?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
                name = VALUES(name),
                description = VALUES(description), 
                icon_url = VALUES(icon_url),
                max_equippable = VALUES(max_equippable)
        ");
        $stmt->execute([$gameId, $name, $slug, $description, $iconUrl, $maxEquippable]);

        if ($stmt->rowCount() > 0 && $this->pdo->lastInsertId() > 0) {
            return (int) $this->pdo->lastInsertId();
        }

        // Get existing ID
        $stmt = $this->pdo->prepare("SELECT id FROM accessory_categories WHERE slug = ? AND game_id = ?");
        $stmt->execute([$slug, $gameId]);
        return (int) $stmt->fetchColumn();
    }

    public function getBySlug(string $slug, int $gameId = 1): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM accessory_categories WHERE slug = ? AND game_id = ?");
        $stmt->execute([$slug, $gameId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getAll(int $gameId = 1): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM accessory_categories WHERE game_id = ? ORDER BY name ASC");
        $stmt->execute([$gameId]);
        return $stmt->fetchAll();
    }
}
