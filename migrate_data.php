<?php

require_once __DIR__ . '/vendor/autoload.php';

use BuildForge\Services\MigrationService;

echo "Starting Migration Script (via Service)...\n";

try {
    $service = new MigrationService();
    // Pass a closure to echo logs in real-time
    $service->run(function ($msg) {
        echo "$msg\n";
    });
} catch (\Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
    exit(1);
}
