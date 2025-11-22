<?php

declare(strict_types=1);

namespace App\DTO\Request;

class CreateContactRequest
{
    private string $firstName;
    private string $lastName;
    private string $phone;
    private string $email;
    /** @var array<string, mixed>|null */
    private ?array $imageFile;

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed>|null $imageFile
     */
    public function __construct(array $data, ?array $imageFile = null)
    {
        $this->firstName = (string) ($data['first_name'] ?? '');
        $this->lastName = (string) ($data['last_name'] ?? '');
        $this->phone = (string) ($data['phone'] ?? '');
        $this->email = (string) ($data['email'] ?? '');
        $this->imageFile = $imageFile;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getImageFile(): ?array
    {
        return $this->imageFile;
    }

    public function hasImage(): bool
    {
        return $this->imageFile !== null;
    }

    /**
     * Convert to array
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }
}
