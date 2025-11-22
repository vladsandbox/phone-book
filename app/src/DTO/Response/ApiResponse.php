<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ApiResponse
{
    private bool $success;
    private ?string $message;
    /** @var array<string>|null */
    private ?array $errors;
    private mixed $data;

    /**
     * @param bool $success
     * @param string|null $message
     * @param array<string>|null $errors
     * @param mixed $data
     */
    public function __construct(
        bool $success,
        ?string $message = null,
        ?array $errors = null,
        mixed $data = null
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->errors = $errors;
        $this->data = $data;
    }

    /**
     * Create success response
     * @param string|null $message
     * @param mixed $data
     * @return self
     */
    public static function success(?string $message = null, mixed $data = null): self
    {
        return new self(true, $message, null, $data);
    }

    /**
     * Create error response
     * @param array<string> $errors
     * @return self
     */
    public static function error(array $errors): self
    {
        return new self(false, null, $errors, null);
    }

    /**
     * Convert to array for JSON response
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $response = ['success' => $this->success];

        if ($this->message !== null) {
            $response['message'] = $this->message;
        }

        if ($this->errors !== null && !empty($this->errors)) {
            $response['errors'] = $this->errors;
        }

        if ($this->data !== null) {
            if (is_array($this->data)) {
                $response = array_merge($response, $this->data);
            } else {
                $response['data'] = $this->data;
            }
        }

        return $response;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return array<string>|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
