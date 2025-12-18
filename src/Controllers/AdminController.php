<?php

namespace BuildForge\Controllers;

use BuildForge\Services\MigrationService;

class AdminController extends Controller
{
    public function migrate(): void
    {
        // Increase time limit for scraping
        set_time_limit(300);

        // Start Output Buffering to capture rogue echos (e.g. from ImageService)
        ob_start();

        $logs = [];
        try {
            $service = new MigrationService();
            // Pass a logger closure if we want to capture structured logs
            $logs = $service->run(function ($msg) {
                // Determine if we should flush progress? No, just collect.
            });
            $title = "Migration Complete";
        } catch (\Exception $e) {
            $title = "Migration Failed";
            $logs[] = "ERROR: " . $e->getMessage();
        }

        // Capture any raw output (scraper/image service noise)
        $rawOutput = ob_get_clean();

        // Split raw output into lines and prepend to logs if meaningful
        if (!empty($rawOutput)) {
            $rawLines = array_filter(explode("\n", $rawOutput), 'trim');
            // Merge raw lines at the start or end? 
            // The user calls it a "parrafada" (ramble), so maybe just suppressing it is better?
            // But some info might be useful. Let's merge it into $logs to show it INSIDE the box.
            $logs = array_merge($logs, $rawLines);
        }

        $this->render('admin/migrate', [
            'title' => $title,
            'logs' => $logs
        ]);
    }
}
