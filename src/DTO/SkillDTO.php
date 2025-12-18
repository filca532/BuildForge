<?php

namespace BuildForge\DTO;

class SkillDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $iconPath = null,
        public ?int $damage = null,
        public ?string $cost = null,
        public ?string $type = null, // Active, Passive, etc.
        public ?string $additionalInfo = null
    ) {
    }
}
