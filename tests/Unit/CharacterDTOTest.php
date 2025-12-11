<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use BuildForge\DTO\CharacterDTO;

class CharacterDTOTest extends TestCase
{
    public function testCharacterDTOInitialization(): void
    {
        $char = new CharacterDTO(
            name: 'TestChar',
            imageUrl: 'http://test.com/img.png',
            description: 'A test character'
        );

        $this->assertEquals('TestChar', $char->name);
        $this->assertEquals('http://test.com/img.png', $char->imageUrl);
        $this->assertEquals('A test character', $char->description);
        $this->assertIsArray($char->skills);
        $this->assertEmpty($char->skills);
    }
}
