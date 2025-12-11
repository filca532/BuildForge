<?php

namespace BuildForge\Controllers;

use BuildForge\Models\Character;
use BuildForge\Models\Build;
use BuildForge\Models\Skill;

class BuildController extends Controller
{
    public function index(): void
    {
        $buildModel = new Build();
        $builds = $buildModel->getAll();

        $this->render('builds/index', [
            'title' => 'Community Builds',
            'builds' => $builds
        ]);
    }

    public function create(): void
    {
        // For simplicity, we assume generic user (ID 1) since we don't have full Auth session yet.
        // Or we should redirect to login.

        $characterModel = new Character();
        $characters = $characterModel->getAll();

        // If character selected, fetch skills
        $selectedCharId = $_GET['character_id'] ?? null;
        $skills = [];
        if ($selectedCharId) {
            $skillModel = new Skill();
            $skills = $skillModel->getByCharacterId((int) $selectedCharId);
        }

        $this->render('builds/create', [
            'title' => 'Create Build',
            'characters' => $characters,
            'selectedCharId' => $selectedCharId,
            'skills' => $skills
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /builds/create');
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $characterId = $_POST['character_id'];
        $name = $_POST['name'];
        $description = $_POST['description'] ?? '';
        $skillIds = $_POST['skills'] ?? [];
        $userId = $_SESSION['user_id'];

        $buildModel = new Build();
        try {
            $buildModel->create($userId, $characterId, $name, $description, $skillIds);
            header('Location: /builds'); // Redirect to list
        } catch (\Exception $e) {
            echo "Error saving build: " . $e->getMessage();
        }
    }

    public function delete(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $buildModel = new Build();
        $build = $buildModel->getById((int) $id);

        if ($build && $build['user_id'] == $_SESSION['user_id']) {
            $buildModel->delete((int) $id);
        }

        header('Location: /builds');
    }
}
