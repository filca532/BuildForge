<?php

namespace BuildForge\Models;

use BuildForge\Database;
use PDO;

class Build
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT b.*, c.name as character_name, u.username 
            FROM builds b 
            JOIN characters c ON b.character_id = c.id 
            JOIN users u ON b.user_id = u.id
            ORDER BY b.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM builds WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(int $userId, int $characterId, string $name, string $description, array $skillIds): int
    {
        $this->pdo->beginTransaction();
        try {
            // Insert Build
            $stmt = $this->pdo->prepare("INSERT INTO builds (user_id, character_id, name, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $characterId, $name, $description]);
            $buildId = (int) $this->pdo->lastInsertId();

            // Insert Skills
            $stmtSkill = $this->pdo->prepare("INSERT INTO build_skills (build_id, skill_id) VALUES (?, ?)");
            foreach ($skillIds as $skillId) {
                $stmtSkill->execute([$buildId, $skillId]);
            }

            $this->pdo->commit();
            return $buildId;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM builds WHERE id = ?");
        $stmt->execute([$id]);
    }
}
