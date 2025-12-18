<?php

namespace BuildForge\Models;

use BuildForge\Database;
use PDO;

class Game
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM games ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM games WHERE slug = ?");
        $stmt->execute([$slug]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
