<?php

namespace BuildForge\Scraping;

use BuildForge\DTO\CharacterDTO;

interface ScraperInterface
{
    /**
     * @return CharacterDTO[]
     */
    public function getCharacters(): array;
}
