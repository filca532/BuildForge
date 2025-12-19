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
        public array $elements = [],         // [['name' => 'Fire', 'icon' => 'url']]
        public ?array $scaling = null        // ["Vit" => "B", "Def" => "A"]
    ) {
    }
}
