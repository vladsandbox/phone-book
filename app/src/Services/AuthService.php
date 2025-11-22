<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\DTO\Request\RegisterRequest;
use App\DTO\Request\LoginRequest;
use App\DTO\Response\ApiResponse;

class AuthService
{
    private User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * Register a new user
     * @param RegisterRequest $request
     * @return ApiResponse
     */
    public function register(RegisterRequest $request): ApiResponse
    {
        // Validate registration data
        $errors = $this->validateRegistration($request);

        if (!empty($errors)) {
            return ApiResponse::error($errors);
        }

        // Create user
        try {
            $this->userModel->create(
                $request->getLogin(),
                $request->getEmail(),
                $request->getPassword()
            );

            return ApiResponse::success('Registration successful! You can now login.');
        } catch (\Exception $e) {
            return ApiResponse::error(['Registration failed. Please try again later.']);
        }
    }

    /**
     * Authenticate user
     * @param LoginRequest $request
     * @return ApiResponse
     */
    public function login(LoginRequest $request): ApiResponse
    {
        // Validate login data
        $errors = $this->validateLogin($request);

        if (!empty($errors)) {
            return ApiResponse::error($errors);
        }

        // Find user
        $user = $this->userModel->findByLogin($request->getLogin());

        if (!$user || !$this->userModel->verifyPassword($request->getPassword(), $user['password'])) {
            return ApiResponse::error(['Invalid login or password']);
        }

        return ApiResponse::success('Login successful!', ['user' => $user]);
    }

    /**
     * Validate registration data
     * @param RegisterRequest $request
     * @return array<string>
     */
    private function validateRegistration(RegisterRequest $request): array
    {
        $errors = [];

        // Validate login
        if (empty($request->getLogin()) || strlen($request->getLogin()) < 3) {
            $errors[] = 'Login must be at least 3 characters';
        }

        // Validate email
        if (empty($request->getEmail()) || !filter_var($request->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        // Validate password
        if (empty($request->getPassword()) || strlen($request->getPassword()) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        } else {
            // Check that password contains only Latin letters and numbers
            if (!preg_match('/^[A-Za-z0-9]+$/', $request->getPassword())) {
                $errors[] = 'Password must contain only Latin letters and numbers';
            } else {
                // Check password complexity
                $hasUpperCase = preg_match('/[A-Z]/', $request->getPassword());
                $hasLowerCase = preg_match('/[a-z]/', $request->getPassword());
                $hasDigit = preg_match('/[0-9]/', $request->getPassword());

                if (!$hasUpperCase || !$hasLowerCase || !$hasDigit) {
                    $errors[] = 'Password must contain uppercase and lowercase Latin letters, and numbers';
                }
            }
        }

        // Check for existing login
        if (!empty($request->getLogin()) && $this->userModel->loginExists($request->getLogin())) {
            $errors[] = 'Login already taken';
        }

        // Check for existing email
        if (!empty($request->getEmail()) && $this->userModel->emailExists($request->getEmail())) {
            $errors[] = 'Email already registered';
        }

        return $errors;
    }

    /**
     * Validate login data
     * @param LoginRequest $request
     * @return array<string>
     */
    private function validateLogin(LoginRequest $request): array
    {
        $errors = [];

        if (empty($request->getLogin())) {
            $errors[] = 'Please enter login';
        }

        if (empty($request->getPassword())) {
            $errors[] = 'Please enter password';
        }

        return $errors;
    }
}
