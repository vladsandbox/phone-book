<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use App\DTO\Request\CreateContactRequest;
use App\DTO\Request\UpdateContactRequest;
use App\DTO\Response\ApiResponse;

class ContactService
{
    private Contact $contactModel;
    private string $uploadDir;

    public function __construct(Contact $contactModel)
    {
        $this->contactModel = $contactModel;
        $this->uploadDir = __DIR__ . '/../../public/uploads/contacts/';
    }

    /**
     * Create a new contact
     * @param int $userId
     * @param CreateContactRequest $request
     * @return ApiResponse
     */
    public function create(int $userId, CreateContactRequest $request): ApiResponse
    {
        // Validate contact data
        $errors = $this->validateContactData($request->toArray());

        if (!empty($errors)) {
            return ApiResponse::error($errors);
        }

        // Handle image upload
        $imagePath = null;
        if ($request->hasImage()) {
            $uploadResult = $this->handleImageUpload($request->getImageFile());
            if (!$uploadResult['success']) {
                return ApiResponse::error($uploadResult['errors']);
            }
            if (isset($uploadResult['path'])) {
                $imagePath = $uploadResult['path'];
            }
        }

        try {
            $this->contactModel->create(
                $userId,
                trim($request->getFirstName()),
                trim($request->getLastName()),
                trim($request->getPhone()),
                trim($request->getEmail()),
                $imagePath
            );

            return ApiResponse::success('Contact added successfully');
        } catch (\Exception $e) {
            // Delete uploaded file if database insert fails
            if ($imagePath !== null) {
                $this->deleteImage($imagePath);
            }

            return ApiResponse::error(['Error adding contact. Please try again.']);
        }
    }

    /**
     * Update an existing contact
     * @param int $userId
     * @param UpdateContactRequest $request
     * @return ApiResponse
     */
    public function update(int $userId, UpdateContactRequest $request): ApiResponse
    {
        // Validate contact data
        $errors = $this->validateContactData($request->toArray());

        if (!empty($errors)) {
            return ApiResponse::error($errors);
        }

        // Check if contact exists and belongs to user
        $existing = $this->contactModel->findById($request->getId(), $userId);
        if (!$existing) {
            return ApiResponse::error(['Contact not found']);
        }

        if ((int) ($existing['user_id'] ?? 0) !== $userId) {
            return ApiResponse::error(['Access denied']);
        }

        // Handle image upload
        $imagePath = null;
        $newImageUploaded = false;
        if ($request->hasImage()) {
            $uploadResult = $this->handleImageUpload($request->getImageFile());
            if (!$uploadResult['success']) {
                return ApiResponse::error($uploadResult['errors']);
            }
            if (isset($uploadResult['path'])) {
                $imagePath = $uploadResult['path'];
                $newImageUploaded = true;
            }
        }

        try {
            $this->contactModel->update(
                $request->getId(),
                $userId,
                trim($request->getFirstName()),
                trim($request->getLastName()),
                trim($request->getPhone()),
                trim($request->getEmail()),
                $imagePath
            );

            // Delete old image if new one was uploaded
            if ($newImageUploaded && !empty($existing['image_path'])) {
                $this->deleteImage($existing['image_path']);
            }

            return ApiResponse::success('Contact updated successfully');
        } catch (\Exception $e) {
            // Delete newly uploaded file if database update fails
            if ($newImageUploaded && $imagePath !== null) {
                $this->deleteImage($imagePath);
            }

            return ApiResponse::error(['Error updating contact. Please try again.']);
        }
    }

    /**
     * Get all contacts for a user
     * @param int $userId
     * @return ApiResponse
     */
    public function getAllByUserId(int $userId): ApiResponse
    {
        try {
            $contacts = $this->contactModel->findByUserId($userId);
            return ApiResponse::success(null, ['contacts' => $contacts]);
        } catch (\Exception $e) {
            return ApiResponse::error(['Error loading contacts']);
        }
    }

    /**
     * Get a contact by ID
     * @param int $contactId
     * @param int $userId
     * @return ApiResponse
     */
    public function getById(int $contactId, int $userId): ApiResponse
    {
        try {
            $contact = $this->contactModel->findById($contactId, $userId);

            if (!$contact) {
                return ApiResponse::error(['Contact not found']);
            }

            if ((int) ($contact['user_id'] ?? 0) !== $userId) {
                return ApiResponse::error(['Access denied']);
            }

            return ApiResponse::success(null, ['contact' => $contact]);
        } catch (\Exception $e) {
            return ApiResponse::error(['Error loading contact']);
        }
    }

    /**
     * Delete a contact
     * @param int $contactId
     * @param int $userId
     * @return ApiResponse
     */
    public function delete(int $contactId, int $userId): ApiResponse
    {
        try {
            // Check if contact exists and belongs to user
            $contact = $this->contactModel->findById($contactId, $userId);

            if (!$contact) {
                return ApiResponse::error(['Contact not found']);
            }

            if ((int) ($contact['user_id'] ?? 0) !== $userId) {
                return ApiResponse::error(['Access denied']);
            }

            // Delete contact from database
            $deleted = $this->contactModel->delete($contactId, $userId);

            if (!$deleted) {
                return ApiResponse::error(['Error deleting contact']);
            }

            // Delete image file if it exists
            if (!empty($contact['image_path'])) {
                $this->deleteImage($contact['image_path']);
            }

            return ApiResponse::success('Contact deleted successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(['Error deleting contact. Please try again.']);
        }
    }

    /**
     * Validate contact data
     * @param array<string, mixed> $data
     * @return array<string>
     */
    private function validateContactData(array $data): array
    {
        $errors = [];

        if (empty($data['first_name']) || trim($data['first_name']) === '') {
            $errors[] = 'First name is required';
        }

        if (empty($data['last_name']) || trim($data['last_name']) === '') {
            $errors[] = 'Last name is required';
        }

        if (empty($data['phone']) || trim($data['phone']) === '') {
            $errors[] = 'Phone number is required';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        return $errors;
    }

    /**
     * Handle image upload
     * @param array<string, mixed> $fileData
     * @return array{success: bool, path?: string, errors?: array<string>}
     */
    private function handleImageUpload(array $fileData): array
    {
        if (!isset($fileData['error']) || $fileData['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true];
        }

        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'errors' => ['Error uploading image']];
        }

        $errors = [];
        $maxSize = 5 * 1024 * 1024; // 5 MB
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

        // Check file size
        if ($fileData['size'] > $maxSize) {
            $errors[] = 'Image size must be less than 5 MB';
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileData['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes, true)) {
            $errors[] = 'Image must be JPEG or PNG format';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0o775, true);
        }

        // Generate unique filename
        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $filename = uniqid('contact_', true) . '.' . $extension;
        $imagePath = '/uploads/contacts/' . $filename;
        $fullPath = $this->uploadDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($fileData['tmp_name'], $fullPath)) {
            return ['success' => false, 'errors' => ['Error uploading image']];
        }

        return ['success' => true, 'path' => $imagePath];
    }

    /**
     * Delete an image file
     * @param string $imagePath
     * @return void
     */
    private function deleteImage(string $imagePath): void
    {
        $filePath = __DIR__ . '/../../public' . $imagePath;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}
