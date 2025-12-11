<?php

require_once __DIR__ . '/vendor/autoload.php';

// Simulate Client Request using Guzzle (already installed)
use GuzzleHttp\Client;

echo "Testing API Endpoint: /api/builds/top-rated\n";
echo "-----------------------------------------\n";

try {
    // Assuming local server - since we can't spin up a real server easily here, passing URL 
    // BUT wait, I can't request 'http://localhost' from inside the container unless the user has wamp running.
    // The user HAS wamp. So I can try http://localhost/BuildForge/public/api/builds/top-rated

    // However, I don't know the exact port or if virtual host is set up.
    // Best effort: Try localhost on standard port.

    $client = new Client(['base_uri' => 'http://localhost/BuildForge/public/']);

    // Note: If this fails due to network, I'll simulate it by calling the controller directly in a separate test script.
    // But let's try real HTTP if possible. Actually, "php -S" is safer.

    echo "This script assumes the server is running. If not, it will fail.\n";
    echo "Skipping Guzzle implementation to avoid hanging if no server.\n";
    echo "Simulating internal call:\n\n";

    // Simulate internal call essentially
    $_SERVER['REQUEST_URI'] = '/api/builds/top-rated';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    // Capture output
    ob_start();
    require __DIR__ . '/public/index.php';
    $output = ob_get_clean();

    echo "Response received:\n";
    echo $output . "\n";

    $json = json_decode($output, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "\n✅ Valid JSON received.\n";
        echo "Count: " . count($json['data']) . "\n";
    } else {
        echo "\n❌ Invalid JSON.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
