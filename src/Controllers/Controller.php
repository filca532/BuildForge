<?php

namespace BuildForge\Controllers;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);

        // Output buffering to capture view content
        ob_start();
        require __DIR__ . "/../../views/{$view}.php";
        $content = ob_get_clean();

        // Render within layout
        require __DIR__ . "/../../views/layouts/main.php";
    }
}
