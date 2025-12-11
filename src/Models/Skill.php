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

    public function save(string $name, ?string $description, int $damage, string $cost, string $type, ?int $characterId = null, int $gameId = 1): int
    {
        // Check if exists
        $stmt = $this->pdo->prepare("SELECT id FROM skills WHERE name = ? AND character_id " . ($characterId ? "= ?" : "IS NULL"));
        $params = $characterId ? [$name, $characterId] : [$name];
        $stmt->execute($params);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            return (int) $existing;
        }

        $stmt = $this->pdo->prepare("INSERT INTO skills (game_id, character_id, name, description, damage, cost, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$gameId, $characterId, $name, $description, $damage, $cost, $type]);
        return (int) $this->pdo->lastInsertId();
    }

    public function getByCharacterId(int $characterId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM skills WHERE character_id = ?");
        $stmt->execute([$characterId]);
        return $stmt->fetchAll();
    }
}
