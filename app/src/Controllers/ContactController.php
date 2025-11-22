<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Services\ContactService;
use App\DTO\Request\CreateContactRequest;
use App\DTO\Request\UpdateContactRequest;

class ContactController extends Controller
{
    private ContactService $contactService;

    public function __construct()
    {
        session_start();

        // Initialize service with dependencies
        $contactModel = new Contact();
        $this->contactService = new ContactService($contactModel);
    }

    public function create(): never
    {
        $userId = $this->requireAuth();
        $request = new CreateContactRequest($_POST, $_FILES['image'] ?? null);
        $response = $this->contactService->create($userId, $request);

        $statusCode = $response->isSuccess() ? 200 : 400;
        $this->json($response->toArray(), $statusCode);
    }

    public function update(array $params): never
    {
        $userId = $this->requireAuth();
        $contactId = (int) ($params['id'] ?? 0);
        $request = new UpdateContactRequest($contactId, $_POST, $_FILES['image'] ?? null);
        $response = $this->contactService->update($userId, $request);

        // Determine status code based on error type
        $statusCode = 200;
        if (!$response->isSuccess()) {
            $statusCode = 400;
            $errors = $response->getErrors();
            if ($errors) {
                if (in_array('Contact not found', $errors)) {
                    $statusCode = 404;
                } elseif (in_array('Access denied', $errors)) {
                    $statusCode = 403;
                }
            }
        }

        $this->json($response->toArray(), $statusCode);
    }

    public function getAll(): never
    {
        $userId = $this->requireAuth();
        $response = $this->contactService->getAllByUserId($userId);

        $statusCode = $response->isSuccess() ? 200 : 500;
        $this->json($response->toArray(), $statusCode);
    }

    public function edit(array $params): never
    {
        $userId = $this->requireAuth();
        $contactId = (int) ($params['id'] ?? 0);
        $response = $this->contactService->getById($contactId, $userId);

        // Determine status code based on error type
        $statusCode = 200;
        if (!$response->isSuccess()) {
            $statusCode = 500;
            $errors = $response->getErrors();
            if ($errors) {
                if (in_array('Contact not found', $errors)) {
                    $statusCode = 404;
                } elseif (in_array('Access denied', $errors)) {
                    $statusCode = 403;
                }
            }
        }

        $this->json($response->toArray(), $statusCode);
    }

    public function delete(array $params): never
    {
        $userId = $this->requireAuth();
        $contactId = (int) ($params['id'] ?? 0);
        $response = $this->contactService->delete($contactId, $userId);

        // Determine status code based on error type
        $statusCode = 200;
        if (!$response->isSuccess()) {
            $statusCode = 500;
            $errors = $response->getErrors();
            if ($errors) {
                if (in_array('Contact not found', $errors)) {
                    $statusCode = 404;
                } elseif (in_array('Access denied', $errors)) {
                    $statusCode = 403;
                }
            }
        }

        $this->json($response->toArray(), $statusCode);
    }
}
