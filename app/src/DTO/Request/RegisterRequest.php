<?php

declare(strict_types=1);

namespace App\DTO\Request;

class RegisterRequest
{
    private string $login;
    private string $email;
    private string $password;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->login = (string) ($data['login'] ?? '');
        $this->email = (string) ($data['email'] ?? '');
        $this->password = (string) ($data['password'] ?? '');
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Convert to array
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'login' => $this->login,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
