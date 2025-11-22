<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }

    protected function json(mixed $data, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): never
    {
        header("Location: $url");
        exit;
    }

    protected function getInput(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $json = file_get_contents('php://input');
            return json_decode($json, true);
        }

        return $_POST;
    }

    protected function requireAuth(): int
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(['success' => false, 'errors' => ['Authorization required']], 401);
        }
        return (int) $_SESSION['user_id'];
    }
}
