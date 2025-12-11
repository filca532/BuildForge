<?php

namespace BuildForge;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        // Simple router logic
        $uri = parse_url($uri, PHP_URL_PATH);

        // Remove base path if valid (e.g., /BuildForge/public)
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);

        // Use case-insensitive check because Windows/WAMP might vary
        if (stripos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }

        // Ensure path starts with /
        $path = '/' . ltrim($uri, '/');

        // Remove query string and trailing slash if needed
        // For simplicity, strict matching for now

        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];

            if (is_array($handler)) {
                $controller = new $handler[0]();
                $action = $handler[1];
                $controller->$action();
            } else {
                call_user_func($handler);
            }
        } else {
            // 404
            http_response_code(404);
            echo "404 Not Found";
        }
    }
}
