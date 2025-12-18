<?php

namespace BuildForge\Controllers;

use BuildForge\Models\Character;
use BuildForge\Models\Skill;
use BuildForge\Models\Weapon;

class CharacterController extends Controller
{
    public function index(): void
    {
        $characterModel = new Character();
        $characters = $characterModel->getAll();

        $this->render('characters/index', [
            'title' => 'Characters',
            'characters' => $characters
        ]);
    }

    public function show(): void
    {
        $id = $_GET['id'] ?? 0;
        $characterModel = new Character();
        $character = $characterModel->getById((int) $id);

        if (!$character) {
            http_response_code(404);
            echo "Character not found";
            return;
        }

        $skillModel = new Skill();
        $skills = $skillModel->getByCharacterId((int) $id);

        $weaponModel = new Weapon();
        $weapons = $weaponModel->getByCharacter((int) $id);

        // Get active tab from query param, default to 'skills'
        $activeTab = $_GET['tab'] ?? 'skills';

        $this->render('characters/show', [
            'title' => $character['name'],
            'character' => $character,
            'skills' => $skills,
            'weapons' => $weapons,
            'activeTab' => $activeTab
        ]);
    }
}

