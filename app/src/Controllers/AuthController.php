<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\AuthService;
use App\DTO\Request\RegisterRequest;
use App\DTO\Request\LoginRequest;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        session_start();

        // Initialize service with dependencies
        $userModel = new User();
        $this->authService = new AuthService($userModel);
    }

    public function showRegister(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $this->view('register');
    }

    public function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        $this->view('login');
    }

    public function register(): never
    {
        $data = $this->getInput();
        $request = new RegisterRequest($data);
        $response = $this->authService->register($request);

        $statusCode = $response->isSuccess() ? 200 : 400;
        $this->json($response->toArray(), $statusCode);
    }

    public function login(): never
    {
        $data = $this->getInput();
        $request = new LoginRequest($data);
        $response = $this->authService->login($request);

        if ($response->isSuccess()) {
            // Set session
            $userData = $response->getData();
            if (isset($userData['user'])) {
                $_SESSION['user_id'] = $userData['user']['id'];
                $_SESSION['user_login'] = $userData['user']['login'];
            }

            $this->json($response->toArray());
        }

        // Determine status code based on error type
        $statusCode = 400;
        $errors = $response->getErrors();
        if ($errors && in_array('Invalid login or password', $errors)) {
            $statusCode = 401;
        }

        $this->json($response->toArray(), $statusCode);
    }

    public function logout(): never
    {
        session_destroy();
        $this->redirect('/login');
    }
}
