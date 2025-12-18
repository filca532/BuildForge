<?php

namespace BuildForge\Models;

use BuildForge\Database;
use PDO;

class Weapon
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Save or update a weapon with full stats
     */
    public function save(
        string $name,
        ?string $description,
        ?string $imageUrl,
        int $gameId = 1,
        ?int $attack = null,
        ?int $elementId = null,
        ?array $scaling = null
    ): int {
        $scalingJson = $scaling ? json_encode($scaling) : null;

        $stmt = $this->pdo->prepare("
            INSERT INTO weapons (game_id, name, description, image_url, attack, element_id, scaling) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                description = VALUES(description), 
                image_url = VALUES(image_url), 
                attack = VALUES(attack),
                element_id = VALUES(element_id),
                scaling = VALUES(scaling)
        ");
        $stmt->execute([$gameId, $name, $description, $imageUrl, $attack, $elementId, $scalingJson]);

        if ($stmt->rowCount() > 0 && $this->pdo->lastInsertId() > 0) {
            return (int) $this->pdo->lastInsertId();
        }

        // Get existing ID
        $stmt = $this->pdo->prepare("SELECT id FROM weapons WHERE name = ? AND game_id = ?");
        $stmt->execute([$name, $gameId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get or create an element by name
     */
    public function getOrCreateElement(string $name, ?string $iconUrl = null, int $gameId = 1): int
    {
        // Check if exists
        $stmt = $this->pdo->prepare("SELECT id FROM elements WHERE name = ? AND game_id = ?");
        $stmt->execute([$name, $gameId]);
        $id = $stmt->fetchColumn();

        if ($id) {
            return (int) $id;
        }

        // Create new
        $color = $this->getElementColor($name);
        $stmt = $this->pdo->prepare("INSERT INTO elements (game_id, name, icon_url, color) VALUES (?, ?, ?, ?)");
        $stmt->execute([$gameId, $name, $iconUrl, $color]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Get default color for known elements
     */
    private function getElementColor(string $name): string
    {
        return match (strtolower($name)) {
            'fire' => '#FF5722',
            'ice' => '#00BCD4',
            'lightning' => '#FFEB3B',
            'void' => '#9C27B0',
            'physical' => '#795548',
            'earth' => '#8BC34A',
            'light' => '#FFC107',
            default => '#9E9E9E'
        };
    }

    public function linkToCharacter(int $weaponId, int $characterId): void
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO character_weapons (character_id, weapon_id) VALUES (?, ?)");
        $stmt->execute([$characterId, $weaponId]);
    }

    public function getByCharacter(int $characterId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT w.*, e.name as element_name, e.icon_url as element_icon, e.color as element_color
            FROM weapons w
            LEFT JOIN elements e ON w.element_id = e.id
            INNER JOIN character_weapons cw ON w.id = cw.weapon_id
            WHERE cw.character_id = ?
            ORDER BY w.name ASC
        ");
        $stmt->execute([$characterId]);
        $weapons = $stmt->fetchAll();

        // Decode scaling JSON
        foreach ($weapons as &$weapon) {
            if ($weapon['scaling']) {
                $weapon['scaling'] = json_decode($weapon['scaling'], true);
            }
        }
        return $weapons;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT w.*, e.name as element_name, e.icon_url as element_icon, e.color as element_color
            FROM weapons w
            LEFT JOIN elements e ON w.element_id = e.id
            ORDER BY w.name ASC
        ");
        $weapons = $stmt->fetchAll();

        foreach ($weapons as &$weapon) {
            if ($weapon['scaling']) {
                $weapon['scaling'] = json_decode($weapon['scaling'], true);
            }
        }
        return $weapons;
    }
}
