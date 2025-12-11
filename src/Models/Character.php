<?php

namespace BuildForge\Models;

use BuildForge\Database;
use PDO;

class Character
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function save(string $name, ?string $imageUrl, ?string $description, int $gameId = 1): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO characters (game_id, name, image_url, description) VALUES (?, ?, ?, ?) 
                                     ON DUPLICATE KEY UPDATE image_url = VALUES(image_url), description = VALUES(description)");
        $stmt->execute([$gameId, $name, $imageUrl, $description]);

        if ($stmt->rowCount() > 0) {
            return (int) $this->pdo->lastInsertId();
        }

        $stmt = $this->pdo->prepare("SELECT id FROM characters WHERE name = ? AND game_id = ?");
        $stmt->execute([$name, $gameId]);
        return (int) $stmt->fetchColumn();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM characters");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM characters WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
