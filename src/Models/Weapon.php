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
        ?int $attack = null
    ): int {

        $stmt = $this->pdo->prepare("
            INSERT INTO weapons (game_id, name, description, image_url, attack) 
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                description = VALUES(description), 
                image_url = VALUES(image_url), 
                attack = VALUES(attack)
        ");
        $stmt->execute([$gameId, $name, $description, $imageUrl, $attack]);

        if ($stmt->rowCount() > 0 && $this->pdo->lastInsertId() > 0) {
            return (int) $this->pdo->lastInsertId();
        }

        // Get existing ID
        $stmt = $this->pdo->prepare("SELECT id FROM weapons WHERE name = ? AND game_id = ?");
        $stmt->execute([$name, $gameId]);
        return (int) $stmt->fetchColumn();
    }

    public function addElement(int $weaponId, int $elementId): void
    {
        $stmt = $this->pdo->prepare("INSERT IGNORE INTO weapon_elements (weapon_id, element_id) VALUES (?, ?)");
        $stmt->execute([$weaponId, $elementId]);
    }

    public function getOrCreateStat(string $code, string $name, int $gameId = 1): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM stats WHERE code = ? AND game_id = ?");
        $stmt->execute([$code, $gameId]);
        $id = $stmt->fetchColumn();

        if ($id) {
            return (int) $id;
        }

        $stmt = $this->pdo->prepare("INSERT INTO stats (game_id, name, code) VALUES (?, ?, ?)");
        $stmt->execute([$gameId, $name, $code]);
        return (int) $this->pdo->lastInsertId();
    }

    public function addScaling(int $weaponId, int $statId, string $grade): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO weapon_scalings (weapon_id, stat_id, grade) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE grade = VALUES(grade)
        ");
        $stmt->execute([$weaponId, $statId, $grade]);
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
            SELECT w.*
            FROM weapons w
            INNER JOIN character_weapons cw ON w.id = cw.weapon_id
            WHERE cw.character_id = ?
            ORDER BY w.name ASC
        ");
        $stmt->execute([$characterId]);
        $weapons = $stmt->fetchAll();

        return $this->attachAllData($weapons);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM weapons ORDER BY name ASC");
        $weapons = $stmt->fetchAll();

        return $this->attachAllData($weapons);
    }

    private function attachAllData(array $weapons): array
    {
        if (empty($weapons))
            return [];

        $weapons = $this->attachScalings($weapons);
        $weapons = $this->attachElements($weapons);


        return $weapons;
    }

    private function attachElements(array $weapons): array
    {
        $weaponIds = array_column($weapons, 'id');
        $placeholders = implode(',', array_fill(0, count($weaponIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT we.weapon_id, e.name, e.icon_url, e.color
            FROM weapon_elements we
            JOIN elements e ON we.element_id = e.id
            WHERE we.weapon_id IN ($placeholders)
        ");
        $stmt->execute($weaponIds);
        $elements = $stmt->fetchAll(PDO::FETCH_GROUP);

        foreach ($weapons as &$weapon) {
            $weapon['elements'] = isset($elements[$weapon['id']]) ? $elements[$weapon['id']] : [];
            // For backward compatibility with View (singular assumption if any)
            $first = $weapon['elements'][0] ?? null;
            $weapon['element_name'] = $first['name'] ?? null;
            $weapon['element_icon'] = $first['icon_url'] ?? null;
            $weapon['element_color'] = $first['color'] ?? null;
        }
        return $weapons;
    }



    private function attachScalings(array $weapons): array
    {
        // ... previous implementation ...
        $weaponIds = array_column($weapons, 'id');
        $placeholders = implode(',', array_fill(0, count($weaponIds), '?'));

        $stmt = $this->pdo->prepare("
            SELECT ws.weapon_id, s.name as stat_name, s.code as stat_code, ws.grade
            FROM weapon_scalings ws
            JOIN stats s ON ws.stat_id = s.id
            WHERE ws.weapon_id IN ($placeholders)
        ");
        $stmt->execute($weaponIds);
        $scalings = $stmt->fetchAll(PDO::FETCH_GROUP);

        foreach ($weapons as &$weapon) {
            $weapon['scaling'] = [];
            if (isset($scalings[$weapon['id']])) {
                foreach ($scalings[$weapon['id']] as $scale) {
                    $weapon['scaling'][$scale['stat_name']] = $scale['grade'];
                }
            }
        }
        return $weapons;
    }
}
