<?php

namespace BuildForge\Controllers;

use BuildForge\Models\Game;

class HomeController extends Controller
{
    public function index(): void
    {
        $gameModel = new Game();
        $games = $gameModel->getAll();

        $this->render('home/index', [
            'title' => 'Home',
            'games' => $games
        ]);
    }
}
