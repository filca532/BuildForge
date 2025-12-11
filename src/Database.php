<?php

namespace BuildForge;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $host = '127.0.0.1';
        $db = 'nexus_rpg';
        $user = 'root';
        $pass = ''; // Default WAMP password
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // Attempt to create database if it doesn't exist (First run)
            try {
                $dsnNoDb = "mysql:host=$host;charset=$charset";
                $pdo = new PDO($dsnNoDb, $user, $pass, $options);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `$db`"); // Explicitly select it
                $this->connection = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e2) {
                throw new \PDOException($e2->getMessage(), (int) $e2->getCode());
            }
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}