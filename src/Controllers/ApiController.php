<?php

namespace BuildForge\Controllers;

use BuildForge\Models\Build;

class ApiController
{
    private function jsonResponse(array $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public function topRated(): void
    {
        // For this demo, "top rated" is just "most recent" 
        // because we haven't implemented a rating system yet.
        // We will fetch up to 5 builds.

        try {
            $buildModel = new Build();
            $allBuilds = $buildModel->getAll(); // In real app, use limit logic in SQL
            $topBuilds = array_slice($allBuilds, 0, 5);

            // Format for API consumer
            $response = array_map(function ($b) {
                return [
                    'id' => $b['id'],
                    'name' => $b['name'],
                    'character' => $b['character_name'],
                    'author' => $b['username'],
                    'description' => $b['description'],
                    'created_at' => $b['created_at']
                ];
            }, $topBuilds);

            $this->jsonResponse(['data' => $response]);

        } catch (\Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
