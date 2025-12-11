<?php

namespace BuildForge\Controllers;

use BuildForge\Models\Character;

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

        $skillModel = new \BuildForge\Models\Skill();
        $skills = $skillModel->getByCharacterId((int) $id);

        $this->render('characters/show', [
            'title' => $character['name'],
            'character' => $character,
            'skills' => $skills
        ]);
    }
}
