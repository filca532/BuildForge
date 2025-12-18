<?php

namespace BuildForge\Models;

use BuildForge\Database;
use PDO;

class Skill
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function save(string $name, ?string $description, ?string $iconPath, int $damage, string $cost, string $type, ?int $characterId = null, int $gameId = 1, ?string $additionalInfo = null): int
    {
        // Check if exists
        $stmt = $this->pdo->prepare("SELECT id FROM skills WHERE name = ? AND character_id " . ($characterId ? "= ?" : "IS NULL"));
        $params = $characterId ? [$name, $characterId] : [$name];
        $stmt->execute($params);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            // Update existing extended
            $update = $this->pdo->prepare("UPDATE skills SET icon_path = ?, description = ?, additional_info = ? WHERE id = ?");
            $update->execute([$iconPath, $description, $additionalInfo, $existing]);
            return (int) $existing;
        }

        $stmt = $this->pdo->prepare("INSERT INTO skills (game_id, character_id, name, description, icon_path, damage, cost, type, additional_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$gameId, $characterId, $name, $description, $iconPath, $damage, $cost, $type, $additionalInfo]);
        return (int) $this->pdo->lastInsertId();
    }

    public function getByCharacterId(int $characterId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM skills WHERE character_id = ?");
        $stmt->execute([$characterId]);
        return $stmt->fetchAll();
    }
}
