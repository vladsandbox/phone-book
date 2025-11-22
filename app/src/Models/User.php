<?php

declare(strict_types=1);
// app/Models/User.php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(string $login, string $email, string $password): bool
    {
        $sql = "INSERT INTO users (login, email, password) VALUES (:login, :email, :password)";
        $stmt = $this->db->prepare($sql);

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        return $stmt->execute();
    }

    /**
     * @return array<string, mixed>|false
     */
    public function findByLogin(string $login): array|false
    {
        $sql = "SELECT * FROM users WHERE login = :login LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @return array<string, mixed>|false
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }

    public function loginExists(string $login): bool
    {
        return $this->findByLogin($login) !== false;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== false;
    }
}
