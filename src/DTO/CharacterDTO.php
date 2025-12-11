<?php

namespace BuildForge\DTO;

class CharacterDTO
{
    public function __construct(
        public string $name,
        public ?string $imageUrl = null,
        public ?string $description = null,
        public array $skills = [], // Array of SkillDTO
        public array $stats = []   // Associative array of base stats
    ) {
    }
}
