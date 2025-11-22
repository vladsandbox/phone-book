<?php

declare(strict_types=1);

namespace App\DTO\Request;

class LoginRequest
{
    private string $login;
    private string $password;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->login = (string) ($data['login'] ?? '');
        $this->password = (string) ($data['password'] ?? '');
    }

    public function getLogin(): string
    {
        return $this->login;
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
            'password' => $this->password,
        ];
    }
}
