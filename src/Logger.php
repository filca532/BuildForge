<?php

namespace BuildForge;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;

class Logger
{
    private static ?MonologLogger $instance = null;

    public static function get(): MonologLogger
    {
        if (self::$instance === null) {
            self::$instance = new MonologLogger('buildforge');
            // Log to a file in the logs directory
            $logFile = __DIR__ . '/../logs/app.log';
            if (!is_dir(dirname($logFile))) {
                mkdir(dirname($logFile), 0777, true);
            }
            self::$instance->pushHandler(new StreamHandler($logFile, MonologLogger::DEBUG));
        }
        return self::$instance;
    }
}
