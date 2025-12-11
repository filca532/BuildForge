<?php

namespace BuildForge\Controllers;

use BuildForge\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        $this->render('auth/login', ['title' => 'Login']);
    }

    public function attemptLogin(): void
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: /');
        } else {
            // Simple error handling
            $_SESSION['flash_error'] = 'Invalid credentials';
            header('Location: /login');
        }
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /');
    }

    public function register(): void
    {
        $this->render('auth/register', ['title' => 'Register']);
    }

    public function store(): void
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Validation skipped for brevity

        $userModel = new User();
        try {
            $userModel->create($username, $password);
            header('Location: /login');
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Username already taken or error';
            header('Location: /register');
        }
    }
}
