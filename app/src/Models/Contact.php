<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Contact
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(int $userId, string $firstName, string $lastName, string $phone, string $email, ?string $imagePath = null): bool
    {
        $sql = "INSERT INTO contacts (user_id, first_name, last_name, phone, email, image_path) 
            VALUES (:user_id, :first_name, :last_name, :phone, :email, :image_path)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':image_path', $imagePath);

        return $stmt->execute();
    }

    public function findByUserId(int $userId): array
    {
        $sql = "SELECT * FROM contacts WHERE user_id = :user_id ORDER BY last_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function findById(int $id, int $userId): array|false
    {
        $sql = "SELECT * FROM contacts WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function update(int $id, int $userId, string $firstName, string $lastName, string $phone, string $email, ?string $imagePath = null): bool
    {
        if ($imagePath !== null) {
            $sql = "UPDATE contacts SET first_name = :first_name, last_name = :last_name, phone = :phone, email = :email, image_path = :image_path WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':image_path', $imagePath);
        } else {
            $sql = "UPDATE contacts SET first_name = :first_name, last_name = :last_name, phone = :phone, email = :email WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
        }

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }

    public function delete(int $id, int $userId): bool
    {
        $sql = "DELETE FROM contacts WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
