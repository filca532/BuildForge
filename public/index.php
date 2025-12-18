<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BuildForge\Router;
use BuildForge\Controllers\HomeController;
use BuildForge\Controllers\CharacterController;
use BuildForge\Controllers\BuildController;
use BuildForge\Controllers\AuthController;
use BuildForge\Controllers\AdminController;

// Detect Base URL
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /BuildForge/public/index.php
$dirName = dirname($scriptName); // e.g., /BuildForge/public
define('BASE_URL', rtrim($dirName, '/\\'));

$router = new Router();

// Define Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/characters', [CharacterController::class, 'index']);
$router->get('/character', [CharacterController::class, 'show']);

$router->get('/builds', [BuildController::class, 'index']);
$router->get('/builds/create', [BuildController::class, 'create']);
$router->post('/builds/store', [BuildController::class, 'store']);
$router->post('/builds/delete', [BuildController::class, 'delete']);

// Auth Routes
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login/store', [AuthController::class, 'attemptLogin']);
$router->get('/logout', [AuthController::class, 'logout']);

// Admin Routes
$router->get('/admin/migrate', [AdminController::class, 'migrate']);

// API Routes
$router->get('/api/builds/top-rated', [\BuildForge\Controllers\ApiController::class, 'topRated']);

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
