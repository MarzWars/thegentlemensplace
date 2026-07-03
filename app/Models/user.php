<?php
namespace App\Models;

use App\Config\Database;

class User
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findByVerifyToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email_verify_token = ? AND email_verify_expires > NOW() LIMIT 1"
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1"
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users
                (uuid, username, email, password_hash, date_of_birth, status,
                 age_verified, age_verified_at, age_verify_method)
            VALUES
                (:uuid, :username, :email, :password_hash, :date_of_birth, :status,
                 :age_verified, :age_verified_at, :age_verify_method)
        ");
        $stmt->execute([
            ':uuid'               => $data['uuid'],
            ':username'           => $data['username'],
            ':email'              => $data['email'],
            ':password_hash'      => $data['password_hash'],
            ':date_of_birth'      => $data['date_of_birth'],
            ':status'             => $data['status'],
            ':age_verified'       => $data['age_verified'],
            ':age_verified_at'    => $data['age_verified_at'],
            ':age_verify_method'  => $data['age_verify_method'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function setEmailVerifyToken(int $userId, string $token): void
    {
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $this->db->prepare(
            "UPDATE users SET email_verify_token = ?, email_verify_expires = ? WHERE id = ?"
        )->execute([$token, $expires, $userId]);
    }

    public function verifyEmail(int $userId): void
    {
        $this->db->prepare(
            "UPDATE users SET email_verified = 1, email_verify_token = NULL,
             email_verify_expires = NULL, status = 'active' WHERE id = ?"
        )->execute([$userId]);
    }

    public function updatePassword(int $userId, string $hash): void
    {
        $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?")
                 ->execute([$hash, $userId]);
    }

    public function setResetToken(int $userId, string $token): void
    {
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->db->prepare(
            "UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?"
        )->execute([$token, $expires, $userId]);
    }

    public function clearResetToken(int $userId): void
    {
        $this->db->prepare(
            "UPDATE users SET reset_token = NULL, reset_expires = NULL WHERE id = ?"
        )->execute([$userId]);
    }

    public function updateLastLogin(int $userId, string $ip): void
    {
        $this->db->prepare(
            "UPDATE users SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?"
        )->execute([$ip, $userId]);
    }

    public function updateProfile(int $userId, array $fields): void
    {
        $allowed = ['phone'];
        $set = [];
        $params = [];
        foreach ($allowed as $col) {
            if (array_key_exists($col, $fields)) {
                $set[]    = "{$col} = ?";
                $params[] = $fields[$col];
            }
        }
        if (empty($set)) return;
        $params[] = $userId;
        $this->db->prepare("UPDATE users SET " . implode(', ', $set) . ", updated_at = NOW() WHERE id = ?")
                 ->execute($params);
    }

    /** Generate a UUID v4 without Composer dependency */
    public static function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
