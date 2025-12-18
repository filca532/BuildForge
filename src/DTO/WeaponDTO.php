<?php

namespace BuildForge\DTO;

class WeaponDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $imageUrl = null,
        public array $usableBy = [],         // ['Gustave', 'Verso'] for shared weapons
        public ?int $attack = null,          // Power stat
        public ?string $element = null,      // "Fire", "Void", "Physical", etc.
        public ?string $elementIconUrl = null, // URL to element icon
        public ?array $scaling = null        // ["Vit" => "B", "Def" => "A", "Agi" => null, "Luck" => "S"]
    ) {
    }
}
